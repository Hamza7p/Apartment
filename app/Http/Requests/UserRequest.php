<?php

namespace App\Http\Requests;

use App\Enums\Role\RoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
                    'phone' => ['required', 'string', 'regex:/^9639\d{8}$/', 'unique:users,phone'],
                    'password' => ['required', 'string', 'min:4'],
                    'first_name' => ['required', 'string', 'max:255'],
                    'last_name' => ['required', 'string', 'max:255'],
                    'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
                    'date_of_birth' => ['required', 'date'],
                    'id_photo' => ['nullable', 'integer', 'exists:media,id'],
                    'personal_photo' => ['nullable', 'integer', 'exists:media,id'],
                    'role' => ['required', Rule::in(RoleName::values())],
                ];
            case 'PUT':
                return [
                    'phone' => ['string', 'regex:/^9639\d{8}$/', Rule::unique('users', 'phone')->ignore($this->user->id)],
                    'first_name' => ['string', 'max:255'],
                    'last_name' => ['string', 'max:255'],
                    'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user->id)],
                    'date_of_birth' => ['date'],
                    'id_photo' => ['nullable', 'integer', 'exists:media,id'],
                    'personal_photo' => ['nullable', 'integer', 'exists:media,id'],
                    'status' => [Rule::in(RoleName::values())],
                ];
        }
    }
}
