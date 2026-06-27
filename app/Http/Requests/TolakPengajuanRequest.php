<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TolakPengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dikontrol via middleware permission di route
    }

    public function rules(): array
    {
        return [
            'catatan' => 'required|string|min:5|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
            'catatan.min' => 'Alasan penolakan minimal 5 karakter.',
            'catatan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}
