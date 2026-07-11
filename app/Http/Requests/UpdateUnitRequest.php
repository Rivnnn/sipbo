<?php
// app/Http/Requests/UpdateUnitRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.units');
    }

    public function rules(): array
    {
        return [
            'nama_unit' => 'required|string|max:100',
            'kode_unit' => [
                'required',
                'string',
                'max:20',
                Rule::unique('unit_kerjas', 'kode_unit')->ignore($this->route('unit')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_unit.required' => 'Nama unit wajib diisi.',
            'kode_unit.required' => 'Kode unit wajib diisi.',
            'kode_unit.unique'   => 'Kode unit sudah digunakan.',
        ];
    }
}
