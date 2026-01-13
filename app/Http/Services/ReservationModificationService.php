<?php

namespace App\Http\Services;

use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Reservation;
use App\Models\ReservationModification;
use App\Notifications\Reservation\ReservationModificationAcceptedNotification;
use App\Notifications\Reservation\ReservationModificationRequestNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ReservationModificationService extends CrudService
{
    private ReservationConflictService $reservationConflictService;

    private ApartmentAvailabilityService $apartmentAvailabilityService;

    public function __construct(
        ReservationConflictService $reservationConflictService,
        ApartmentAvailabilityService $apartmentAvailabilityService
    ) {
        $this->reservationConflictService = $reservationConflictService;
        $this->apartmentAvailabilityService = $apartmentAvailabilityService;
    }

    protected function getModelClass(): string
    {
        return ReservationModification::class;
    }

    /**
     * تنفيذ طلب تعديل (حفظ وإشعار)
     */
    public function requestModification(int $reservationId, array $data)
    {
        $reservation = Reservation::with('apartment', 'user')->findOrFail($reservationId);
        $user = Auth::user();
        $modifications = collect();

        // Only date or cancel modifications are allowed
        if (($data['type'] ?? null) === 'date') {
            // Start date
            $start = $data['start_date'] ?? $reservation->start_date;
            if ($start != $reservation->start_date) {
                $modifications->push(ReservationModification::create([
                    'user_id' => $user->id,
                    'reservation_id' => $reservation->id,
                    'type' => 'start_date',
                    'old_value' => $reservation->start_date,
                    'new_value' => $start,
                    'status' => 'pending',
                ]));
            }

            // End date
            $end = $data['end_date'] ?? $reservation->end_date;
            if ($end != $reservation->end_date) {
                $modifications->push(ReservationModification::create([
                    'user_id' => $user->id,
                    'reservation_id' => $reservation->id,
                    'type' => 'end_date',
                    'old_value' => $reservation->end_date,
                    'new_value' => $end,
                    'status' => 'pending',
                ]));
            }
        } elseif (($data['type'] ?? null) === 'cancel') {
            $modifications->push(ReservationModification::create([
                'user_id' => $user->id,
                'reservation_id' => $reservation->id,
                'type' => 'cancel',
                'old_value' => null,
                'new_value' => null,
                'status' => 'pending',
            ]));
        } else {
            throw new \Exception(__('errors.invalid_modification_type'));
        }

        // Notify the other party
        foreach ($modifications as $modification) {
            $modification->load(['reservation', 'user']);
            $receiver = $reservation->apartment->user_id === $user->id
                ? $reservation->user  // Owner sent modification → notify tenant
                : $reservation->apartment->owner; // Tenant sent modification → notify owner

            Notification::send($receiver, new ReservationModificationRequestNotification($modification));
        }

        return $modifications;
    }

    /**
     * قبول التعديل
     */
    public function acceptModification(int $modificationId)
    {
        $modification = ReservationModification::with('reservation.apartment')->findOrFail($modificationId);
        $reservation = $modification->reservation;
        $apartment = $reservation->apartment;
        $user = Auth::user();

        if ($modification->status !== 'pending') {
            throw new \Exception(__('errors.no_pending_modification'));
        }

        // Authorization: the one who did NOT request the modification must accept
        if ($user->id === $modification->user_id) {
            throw new \Exception(__('errors.unauthorized'));
        }

        // Determine allowed actions
        $isOwner = $user->id === $apartment->user_id;
        $isTenant = $user->id === $reservation->user_id;

        switch ($modification->type) {
            case 'start_date':
            case 'end_date':
            case 'date':
                if (! $isOwner) {
                    throw new \Exception(__('errors.unauthorized'));
                }
                break;

            case 'cancel':
                if (! $isTenant) {
                    throw new \Exception(__('errors.unauthorized'));
                }
                break;

            default:
                throw new \Exception(__('errors.invalid_modification_type'));
        }

        // Apply modification
        switch ($modification->type) {
            case 'date':
                $reservation->start_date = $modification->new_value['start_date'];
                $reservation->end_date = $modification->new_value['end_date'];
                break;

            case 'start_date':
                $reservation->start_date = $modification->new_value;
                break;

            case 'end_date':
                $reservation->end_date = $modification->new_value;
                break;

            case 'cancel':
                $reservation->status = ReservationStatus::CANCELLED;
                break;
        }

        $reservation->save();
        $modification->update(['status' => 'accepted']);

        // Update apartment availability if dates changed
        if (in_array($modification->type, ['date', 'start_date', 'end_date'], true)) {
            $start = Carbon::parse($reservation->start_date);
            $end = Carbon::parse($reservation->end_date);

            $this->reservationConflictService->rejectConflictingRequests(
                $reservation->apartment_id,
                $reservation->start_date,
                $reservation->end_date,
                $reservation->id
            );

            $newAvailability = $this->apartmentAvailabilityService->applyReservation(
                $apartment,
                $start,
                $end
            );

            $apartment->update(['availability' => $newAvailability]);
        }

        // Notify the original requester
        Notification::send($modification->user, new ReservationModificationAcceptedNotification($modification));

        return $modification;
    }

    /**
     * رفض التعديل
     */
    public function rejectModification(int $modificationId)
    {
        $modification = ReservationModification::with('reservation.apartment')->findOrFail($modificationId);
        $reservation = $modification->reservation;
        $apartment = $reservation->apartment;
        $user = Auth::user();

        if ($modification->status !== 'pending') {
            throw new \Exception(__('errors.no_pending_modification'));
        }

        // The requester cannot reject their own modification
        if ($user->id === $modification->user_id) {
            throw new \Exception(__('errors.unauthorized'));
        }

        // Authorization: who can reject
        $isOwner = $user->id === $apartment->user_id;
        $isTenant = $user->id === $reservation->user_id;

        switch ($modification->type) {
            case 'start_date':
            case 'end_date':
            case 'date':
                if (! $isOwner) {
                    throw new \Exception(__('errors.unauthorized'));
                }
                break;

            case 'cancel':
                if (! $isTenant) {
                    throw new \Exception(__('errors.unauthorized'));
                }
                break;

            default:
                throw new \Exception(__('errors.invalid_modification_type'));
        }

        // Mark as rejected
        $modification->update(['status' => 'rejected']);

        // Notify the original requester
        $receiver = $modification->user;
        Notification::send($receiver, new ReservationModificationRejectedNotification($modification));

        return $modification;
    }

    public function getSendReservationModifications()
    {
        $user = Auth::user();

        $reservationModifications = $user->reservationModifications;

        return $reservationModifications;
    }

    // Sent modifications by the current user as an owner
    public function getOwnerSendReservationModifications()
    {
        $user = Auth::user();

        return ReservationModification::query()
            ->with(['reservation.apartment', 'reservation.user', 'user'])
            ->where('user_id', $user->id) // <- CURRENT USER CREATED IT
            ->whereHas('reservation.apartment', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();
    }

    // Sent modifications by the current user as a tenant
    public function getTenantSendReservationModifications()
    {
        $user = Auth::user();

        return ReservationModification::query()
            ->with(['reservation.apartment', 'reservation.user', 'user'])
            ->where('user_id', $user->id) // <- CURRENT USER CREATED IT
            ->whereHas('reservation', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();
    }

    // Received modifications for the current user as an owner
    public function getOwnerReceiveReservationModifications()
    {
        $user = Auth::user();

        return ReservationModification::query()
            ->with(['reservation.apartment', 'reservation.user', 'user'])
            ->whereHas('reservation.apartment', fn ($q) => $q->where('user_id', $user->id))
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->get();
    }

    // Received modifications for the current user as a tenant
    public function getTenantReceiveReservationModifications()
    {
        $user = Auth::user();

        return ReservationModification::query()
            ->with(['reservation.apartment', 'reservation.user', 'user'])
            ->whereHas('reservation', fn ($q) => $q->where('user_id', $user->id))
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->get();
    }
}
