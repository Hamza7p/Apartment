<?php

namespace App\Http\Requests;

use App\Enums\Reservation\ReservationStatus;
use App\Http\Services\ReservationConflictService;
use App\Models\Apartment;
use App\Models\ReservationRequest as ReservationRequestModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReservationRequest extends FormRequest
{
    protected ?Apartment $apartment = null;

    /**
     * Authorization
     */
    public function authorize(): bool
    {
        if ($this->isMethod('PUT')) {
            $id = $this->route('reservation_request');

            $reservationRequest = ReservationRequestModel::find($id);

            return $reservationRequest
                && $reservationRequest->status->value === ReservationStatus::PENDING->value
                && $reservationRequest->user_id === Auth::id();
        }

        return true;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        switch ($this->method()) {

            case 'POST':
                return [
                    'apartment_id' => [
                        'required',
                        'integer',
                        'exists:apartments,id',
                        function ($attribute, $value, $fail) {
                            $this->apartment = Apartment::find($value);

                            if ($this->apartment && $this->apartment->user_id === Auth::id()) {
                                $fail(__('errors.unauthorized'));
                            }

                            if (! $this->apartment) {
                                $fail(__('errors.not_found', [
                                    'model' => app()->getLocale() === 'en'
                                        ? 'apartment'
                                        : 'الشقة',
                                ]));
                            }
                        },
                    ],
                    'start_date' => ['required', 'date', 'after_or_equal:today'],
                    'end_date' => ['required', 'date', 'after:start_date'],
                    'note' => ['nullable', 'string'],
                ];

            case 'PUT':
                return [
                    'start_date' => ['sometimes', 'date', 'after_or_equal:today'],
                    'end_date' => ['sometimes', 'date', 'after:start_date'],
                    'note' => ['nullable', 'string'],
                ];

            default:
                return [];
        }
    }

    /**
     * Advanced validation (conflict check)
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (! $this->apartment && $this->isMethod('POST')) {
                return;
            }

            /** @var ReservationConflictService $conflictService */
            $conflictService = app(ReservationConflictService::class);

            $apartmentId = $this->apartment
                ? $this->apartment->id
                : ReservationRequestModel::find($this->route('reservation_request'))?->apartment_id;

            if (! $apartmentId) {
                return;
            }

            $hasConflict = $conflictService->hasConflict(
                $apartmentId,
                $this->input('start_date'),
                $this->input('end_date')
            );

            if ($hasConflict) {
                $validator->errors()->add(
                    'start_date',
                    __('errors.apartment_not_available')
                );
            }
        });
    }
}
