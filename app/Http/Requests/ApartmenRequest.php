<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApartmenRequest extends FormRequest
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
                    'title' => ['required', 'array'],
                    'title.en' => ['required', 'string'],
                    'title.ar' => ['required', 'string'],

                    'description' => ['required', 'array'],
                    'description.en' => ['required', 'string'],
                    'description.ar' => ['required', 'string'],

                    'currency' => ['required', 'in:$,€,SYP'],

                    'governorate' => ['required', 'array'],
                    'governorate.en' => ['required', 'string'],
                    'governorate.ar' => ['required', 'string'],

                    'city' => ['required', 'array'],
                    'city.en' => ['required', 'string'],
                    'city.ar' => ['required', 'string'],

                    'price' => ['required', 'numeric', 'min:0'],

                    'address' => ['required', 'array'],
                    'address.en' => ['required', 'string'],
                    'address.ar' => ['required', 'string'],

                    'number_of_room' => ['required', 'integer'],
                    'number_of_bathroom' => ['required', 'integer'],
                    'area' => ['required', 'integer'],
                    'floor' => ['required', 'integer'],

                ];
            case 'PUT':
                return [
                    'title' => ['sometimes', 'array'],
                    'title.en' => ['required_with:title', 'string'],
                    'title.ar' => ['required_with:title'],

                    'description' => ['sometimes', 'array'],
                    'description.en' => ['required_with:description', 'string'],
                    'description.ar' => ['required_with:description', 'string'],

                    'currency' => ['sometimes', 'in:$,€,SYP'],

                    'governorate' => ['sometimes', 'array'],
                    'governorate.en' => ['required_with:governorate', 'string'],
                    'governorate.ar' => ['required_with:governorate', 'string'],

                    'city' => ['sometimes', 'array'],
                    'city.en' => ['required_with:city', 'string'],
                    'city.ar' => ['required_with:city', 'string'],

                    'price' => ['sometimes', 'numeric', 'min:0'],

                    'address' => ['sometimes', 'array'],
                    'address.en' => ['required_with:address', 'string'],
                    'address.ar' => ['required_with:address', 'string'],

                    'number_of_room' => ['sometimes', 'integer'],
                    'number_of_bathroom' => ['sometimes', 'integer'],
                    'area' => ['sometimes', 'integer'],
                    'floor' => ['sometimes', 'integer'],
                ];
        }
    }
}
