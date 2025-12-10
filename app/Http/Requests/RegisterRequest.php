<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'phone' => ['required', 'string', 'regex:/^9639\d{8}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'date_of_birth' => ['required', 'date'],
            'id_photo' => ['required', 'image', 'mimes:png,jpg', 'max:2048'],
            'personal_photo' => ['required', 'image', 'mimes:png,jpg', 'max:2048'],
        ];
    }
}
