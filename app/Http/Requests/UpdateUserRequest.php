<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'gender' => ['required', 'in:male,female'],
            'role' => ['required', 'in:patient,doctor'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],

            'specialisation' => ['required_if:role,doctor', 'nullable', 'string', 'max:255'],
            'phone_number' => ['required_if:role,doctor', 'nullable', 'string', 'max:20', 'regex:/^(06|07)\d{8}$/'],
            'biography' => ['required_if:role,doctor', 'nullable', 'string', 'max:1000'],

            'date_of_birth' => ['required_if:role,patient', 'nullable', 'date', 'before:today'],
            'blood_type' => ['required_if:role,patient', 'nullable', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-', 'max:3'],
        ];
    }
}
