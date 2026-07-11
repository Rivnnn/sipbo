<?php
// app/Http/Requests/StoreUnitRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.units');
    }

    public function rules(): array
    {
        return [
            'nama_unit' => 'required|string|max:100',
            'kode_unit' => 'required|string|max:20|unique:unit_kerjas,kode_unit',
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
