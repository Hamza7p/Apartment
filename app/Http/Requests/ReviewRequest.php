<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
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
                    'apartment_id' => ['required', 'integer', 'exists:apartments,id'],
                    'rate' => ['required', 'integer', 'between:0,5'],
                ];
            case 'PUT':
                return [
                    'apartment_id' => ['sometimes', 'integer', 'exists:apartments,id'],
                    'rate' => ['sometimes', 'integer', 'between:0,5'],
                ];
        }
    }
}
