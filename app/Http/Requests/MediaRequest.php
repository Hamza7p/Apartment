<?php

namespace App\Http\Requests;

use App\Enums\Medium\MediumFor;
use App\Enums\Medium\MediumType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaRequest extends FormRequest
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
    public function rules()
    {
        switch (request()->method()) {
            default:
            case 'POST':
                return [
                    'media' => ['required', 'array', 'min:1'],
                    'media.*.medium' => ['required', 'file'],
                    'media.*.type' => ['required', Rule::in(MediumType::values())],
                    'media.*.for' => ['required', Rule::in(MediumFor::values())],
                ];
            case 'PUT':
                return [
                    //
                ];
        }
    }
}
