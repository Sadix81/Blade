<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'max:100', 'string', 'unique:users,username'],
            'lastname' => ['required', 'max:100', 'string'],
            'mobile' => ['required', 'regex:/[0-9]{10}/', 'digits:11', 'unique:users,mobile'],
            'password' => ['required', 'min:8', 'regex:/[0-9]/', 'regex:/[A-Z]/', 'regex:/[a-z]/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'twofactor' => ['nullable'],
            'email_verified_at' => ['nullable'],
            'avatar' => ['nullable', 'image'],
            'country' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:1000'],
            'codepost' => ['nullable', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
