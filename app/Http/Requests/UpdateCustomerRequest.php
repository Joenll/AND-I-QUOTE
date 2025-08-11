<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date|before:today',
            'address' => 'nullable|string|max:1000',
            'email' => [
                'sometimes',
                'required',
                'email:rfc,dns',
                Rule::unique('customers', 'email')->ignore($this->customer->id)
            ],
            'contact' => 'required|string|min:11|max:11',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Customer name is required.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'contact.required' => 'Contact number is required.',
            'contact.max' => 'Contact number cannot exceed 11 characters.',
            'contact.min' => 'Contact number must be at least 11 characters.',
        ];
    }
}
