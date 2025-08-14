<?php

namespace Modules\Auth\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow anyone to register
    }

    public function rules()
    {
        return [
            'tenant_name' => 'required|string|max:255',
            'admin_name'  => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
        ];
    }
}
