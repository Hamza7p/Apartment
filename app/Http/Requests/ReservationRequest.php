<?php

namespace App\Http\Requests;

use App\Models\Apartment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReservationRequest extends FormRequest
{
    protected ?Apartment $apartment = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch (request()->method()) {
            default:
                return [];
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
                        },
                    ],
                    'start_date' => [
                        'required',
                        'date',
                        function ($attribute, $value, $fail) {
                            if (! $this->apartment) {
                                $fail(__('errors.not_found', ['model' => app()->getLocale() == 'en' ? 'apartrment' : 'الشقة']));

                                return;
                            }

                            $startDate = Carbon::parse($value);
                            $availableAt = Carbon::parse($this->apartment->available_at);

                            if ($startDate->lt($availableAt)) {
                                $fail(__('errors.apartment_not_available'));
                            }
                        },

                    ],
                    'end_date' => ['required', 'date', 'after:start_date'],
                    'note' => ['nullable', 'string'],
                ];
            case 'PUT':
                return [
                    //
                ];
        }
    }
}
