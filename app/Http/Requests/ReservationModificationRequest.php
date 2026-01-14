<?php

namespace App\Http\Requests;

use App\Http\Services\ReservationConflictService;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReservationModificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'in:date,cancel'], // Removed total_amount
        ];

        switch ($this->input('type')) {
            case 'date':
                // Optional because we will default to existing reservation values
                $rules['start_date'] = ['nullable', 'date'];
                $rules['end_date'] = ['nullable', 'date'];
                break;

            case 'cancel':
                $rules['new_value'] = ['nullable']; // placeholder if needed
                break;
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $reservationId = $this->route('id');
            $reservation = Reservation::with('apartment')->findOrFail($reservationId);
            $user = $this->user();

            // Authorization
            switch ($this->type) {
                case 'date':
                    // Only tenant can request date modification
                    if ($user->id !== $reservation->user_id) {
                        $validator->errors()->add('type', __('errors.unauthorized'));

                        return;
                    }

                    // Use existing reservation dates if user did not send them

                    $newStart = Carbon::parse($this->input('start_date') ?? $reservation->start_date);
                    $newEnd = Carbon::parse($this->input('end_date') ?? $reservation->end_date);

                    if ($newEnd->lt($newStart)) {
                        $validator->errors()->add('end_date', __('errors.end_date_before_start_date'));
                    }

                    // Check for conflicts
                    $conflictService = app(ReservationConflictService::class);
                    $hasConflict = $conflictService->hasConflict(
                        $reservation->apartment_id,
                        $newStart,
                        $newEnd,
                        $reservation->id
                    );

                    if ($hasConflict) {
                        $validator->errors()->add('start_date', __('errors.apartment_not_available'));
                        $validator->errors()->add('end_date', __('errors.apartment_not_available'));
                    }
                    break;

                case 'cancel':
                    // Only tenant can request cancel
                    if ($user->id !== $reservation->user_id) {
                        $validator->errors()->add('type', __('errors.unauthorized'));
                    }
                    break;
            }
        });
    }
}
