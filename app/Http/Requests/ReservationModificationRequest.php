<?php

namespace App\Http\Requests;

use App\Http\Services\ReservationConflictService;
use App\Models\Reservation;
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
            'type' => ['required', 'in:start_date,end_date,total_amount,cancel'],
        ];

        switch ($this->input('type')) {
            case 'start_date':
            case 'end_date':
                $rules['new_value'] = ['required', 'date'];
                break;
            case 'total_amount':
                $rules['new_value'] = ['required', 'numeric', 'min:1'];
                break;
            case 'cancel':
                $rules['new_value'] = ['nullable'];
                break;
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $reservationId = $this->route('id');
            $reservation = Reservation::findOrFail($reservationId);

            $user = $this->user();
            if (in_array($this->type, ['start_date', 'end_date']) && $user->id !== $reservation->user_id) {
                $validator->errors()->add('type', __('errors.unauthorized'));

                return;
            }

            if ($this->type === 'total_amount' && $user->id !== $reservation->apartment->user_id) {
                $validator->errors()->add('type', __('errors.unauthorized'));

                return;
            }
            if (in_array($this->type, ['start_date', 'end_date'])) {
                $newStart = $this->type === 'start_date' ? $this->new_value : $reservation->start_date;
                $newEnd = $this->type === 'end_date' ? $this->new_value : $reservation->end_date;

                $conflictService = app(ReservationConflictService::class);
                $hasConflict = $conflictService->hasConflict(
                    $reservation->apartment_id,
                    $newStart,
                    $newEnd,
                    $reservation->id
                );

                if ($hasConflict) {
                    $validator->errors()->add('new_value', __('errors.apartment_not_available'));
                }
            }
        });
    }
}
