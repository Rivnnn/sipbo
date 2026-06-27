<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pengajuan.create');
    }

    public function rules(): array
    {
        return [
            'program_anggaran_id' => 'required|exists:program_anggarans,id',
            'judul_usulan' => 'required|string|max:200',
            'keterangan' => 'nullable|string|max:1000',
            'nominal_usulan' => 'required|numeric|min:0.01|max:999999999999.99',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'program_anggaran_id.required' => 'Program anggaran wajib dipilih.',
            'program_anggaran_id.exists' => 'Program anggaran tidak valid.',
            'judul_usulan.required' => 'Judul usulan wajib diisi.',
            'judul_usulan.max' => 'Judul usulan maksimal 200 karakter.',
            'nominal_usulan.required' => 'Nominal usulan wajib diisi.',
            'nominal_usulan.numeric' => 'Nominal usulan harus berupa angka.',
            'nominal_usulan.min' => 'Nominal usulan minimal Rp 0,01.',
            'file_lampiran.mimes' => 'Lampiran harus berformat PDF, JPG, atau PNG.',
            'file_lampiran.max' => 'Ukuran lampiran maksimal 2MB.',
        ];
    }
}
