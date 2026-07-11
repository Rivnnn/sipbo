<?php
// app/Http/Requests/UpdateUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.users');
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:100',
            'username'      => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'username')->ignore($this->route('user')),
            ],
            'password'      => 'nullable|string|min:6',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'role'          => 'required|in:staf_unit,kasium,pimpinan',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'password.min'      => 'Password minimal 6 karakter.',
        ];
    }
}
