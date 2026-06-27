<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengajuan = $this->route('id') ? \App\Models\PengajuanAnggaran::find($this->route('id')) : null;

        return $pengajuan
            && $pengajuan->user_id === $this->user()->id
            && $pengajuan->status === 'draft';
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
            'judul_usulan.required' => 'Judul usulan wajib diisi.',
            'nominal_usulan.required' => 'Nominal usulan wajib diisi.',
            'nominal_usulan.numeric' => 'Nominal usulan harus berupa angka.',
            'file_lampiran.mimes' => 'Lampiran harus berformat PDF, JPG, atau PNG.',
            'file_lampiran.max' => 'Ukuran lampiran maksimal 2MB.',
        ];
    }
}
