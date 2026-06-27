<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjukanPolrestabesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pengajuan.update-eksternal');
    }

    public function rules(): array
    {
        return [
            'nomor_referensi_astina' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_referensi_astina.required' => 'Nomor referensi ASTINA wajib diisi.',
            'nomor_referensi_astina.max' => 'Nomor referensi maksimal 50 karakter.',
        ];
    }
}
