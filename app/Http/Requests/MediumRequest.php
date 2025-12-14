<?php

namespace App\Http\Requests;

use App\Enums\Medium\MediumFor;
use App\Enums\Medium\MediumType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediumRequest extends FormRequest
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
                    'medium' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,word', 'max:4096'],
                    'type' => ['required', Rule::in(MediumType::values())],
                    'for' => ['required', Rule::in(MediumFor::values())]
                ];
            case 'PUT':
                return [
                    //
                ];
        }
    }
}
