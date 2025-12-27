<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
                    'phone' => ['string', 'regex:/^9639\d{8}$/', Rule::unique('users', 'phone')->ignore(Auth::user()->id)],
                    'first_name' => ['string', 'max:255'],
                    'last_name' => ['string', 'max:255'],
                    'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore(Auth::user()->id)],
                    'date_of_birth' => ['date'],
                    'id_photo' => ['nullable', 'integer', 'exists:media,id'],
                    'personal_photo' => ['nullable', 'integer', 'exists:media,id'],
                ];
            case 'PUT':
                return [
                    //
                ];
        }
    }
}
