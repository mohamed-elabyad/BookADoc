<?php

namespace App\Http\Requests;

use App\Enums\SpecialtyEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterDoctorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique(User::class, 'email')->where('role', 'doctor')],
            'password' => ['required', 'min:6', 'confirmed'],
            'specialty' => ['required', 'in:' . implode(',', SpecialtyEnum::values())],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'ticket_price' => ['required', 'integer', 'min:50', 'max:10000'],
            'work_from' => ['required', 'date_format:H:i:s'],
            'work_to' => ['required', 'date_format:H:i:s', 'after:work_from'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'license' => ['required', 'mimes:pdf,doc,docx', 'max:4096'],
            'degree' => ['required', 'mimes:pdf,doc,docx', 'max:4096'],
            'bio' => ['nullable', 'string', 'max:1000']
        ];
    }
}
