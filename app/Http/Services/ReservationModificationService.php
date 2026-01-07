<?php

namespace App\Http\Services;

use App\Enums\Apartment\ApartmentStatus;
use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\Base\CrudService;
use App\Models\Reservation;
use App\Models\ReservationModification;
use App\Notifications\Reservation\ReservationModificationAcceptedNotification;
use App\Notifications\Reservation\ReservationModificationRequestNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class ReservationModificationService extends CrudService
{
    private ReservationConflictService $reservationConflictService;

    public function __construct(
        ReservationConflictService $reservationConflictService)
    {
        $this->reservationConflictService = $reservationConflictService;
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

        // حفظ الطلب في جدول التعديلات
        $modification = ReservationModification::create([
            'reservation_id' => $reservation->id,
            'type' => $data['type'],
            'old_value' => match ($data['type']) {
                'start_date' => $reservation->start_date,
                'end_date' => $reservation->end_date,
                'total_amount' => $reservation->total_amount,
                default => null,
            },
            'new_value' => $data['new_value'] ?? null,
            'requested_by' => $user->id,
            'status' => 'pending',
        ]);

        $modification->load([
            'reservation',
            'user',
        ]);

        // تحديد الطرف المستقبل للإشعار
        $receiver = match ($data['type']) {
            'total_price' => $reservation->user,
            default => $reservation->apartment->owner,
        };

        Notification::send($receiver, new ReservationModificationRequestNotification($modification));

        return $modification;
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
            throw new Exception(__('errors.no_pending_modification'));
        }

        // لا يمكن قبول طلبك أنت
        if ($user->id === $modification->requested_by) {
            throw new Exception(__('errors.unauthorized'));
        }

        // صلاحيات الطرف الآخر
        switch ($modification->type) {
            case 'start_date':
            case 'end_date':
                Gate::authorize('respondToDateModification', $reservation);
                break;

            case 'total_amount':
                Gate::authorize('respondToPriceModification', $reservation);
                break;

            case 'cancel':
                Gate::authorize('respondToCancel', $reservation);
                break;
        }

        // تطبيق التعديل
        match ($modification->type) {
            'start_date' => $reservation->start_date = $modification->new_value,
            'end_date' => $reservation->end_date = $modification->new_value,
            'total_amount' => $reservation->total_amount = $modification->new_value,
            'cancel' => $reservation->status = ReservationStatus::CANCELED,
        };

        $reservation->save();

        $modification->update(['status' => 'accepted']);
        if (in_array($modification->type, ['start_date', 'end_date'], true)) {
            $this->reservationConflictService->rejectConflictingRequests(
                $reservation->apartment_id,
                $reservation->start_date,
                $reservation->end_date,
                $reservation->id
            );
            // --- Update availability JSON ---
            $availability = $apartment->availability;
            $start = Carbon::parse($reservation->start_date);
            $end = Carbon::parse($reservation->end_date);

            if (! is_array($availability) || empty($availability)) {
                $availability = [
                    [
                        'from' => now()->toDateTimeString(),
                        'to' => '2099-12-31 23:59:59',
                    ],
                ];
            }

            // Now $availability is always an array
            $new_availability = [];

            foreach ($availability as $period) {
                $period_start = Carbon::parse($period['from']);
                $period_end = Carbon::parse($period['to']);

                // If no overlap with current reservation, keep it
                if ($end->lt($period_start) || $start->gt($period_end)) {
                    $new_availability[] = $period;
                } else {
                    // Split overlapping period
                    if ($period_start->lt($start)) {
                        $new_availability[] = [
                            'from' => $period_start->toDateTimeString(),
                            'to' => $start->subDay()->toDateTimeString(),
                        ];
                    }
                    if ($period_end->gt($end)) {
                        $new_availability[] = [
                            'from' => $end->addDay()->toDateTimeString(),
                            'to' => $period_end->toDateTimeString(),
                        ];
                    }
                }
            }

            // Save updated availability after reservation
            $apartment->availability = $new_availability;
            $apartment->status = ApartmentStatus::RESERVED->value;
            $apartment->save();
        }

        // إشعار صاحب الطلب
        Notification::send($reservation->user, new ReservationModificationAcceptedNotification($modification));

        return $modification;
    }

    /**
     * رفض التعديل
     */
    public function rejectModification(int $modificationId)
    {
        $modification = ReservationModification::with('reservation')->findOrFail($modificationId);
        $reservation = $modification->reservation;
        $user = Auth::user();

        if ($modification->status !== 'pending') {
            throw new Exception(__('errors.unauthorized'));
        }

        // صلاحيات مثل Accept
        if ($modification->type === 'total_price') {
            Gate::authorize('modifyPrice', $reservation);
        } else {
            Gate::authorize('modifyDates', $reservation);
        }

        $modification->status = 'rejected';
        $modification->save();

        $receiver = $modification->user;

        Notification::send($receiver, new ReservationModificationRequestNotification($modification));

        return $modification;
    }
}
