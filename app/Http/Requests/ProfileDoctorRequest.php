<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileDoctorRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'ticket_price' => ['sometimes', 'integer', 'min:50', 'max:10000'],
            'work_from' => ['sometimes', 'date_format:H:i:s'],
            'work_to' => ['sometimes', 'date_format:H:i:s', 'after:work_from'],
            'image' => ['sometimes', 'nullable',  'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:1000']
        ];
    }
}
