<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBkuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bku.input');
    }

    public function rules(): array
    {
        return [
            'program_anggaran_id' => 'required|exists:program_anggarans,id',
            'pengajuan_anggaran_id' => 'nullable|exists:pengajuan_anggarans,id',
            'tanggal_transaksi' => 'required|date|before_or_equal:today',
            'uraian' => 'required|string|max:200',
            'kredit' => 'required|numeric|min:0.01|max:999999999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'program_anggaran_id.required' => 'Program anggaran wajib dipilih.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksi.before_or_equal' => 'Tanggal transaksi tidak boleh di masa depan.',
            'uraian.required' => 'Uraian transaksi wajib diisi.',
            'kredit.required' => 'Nominal kredit wajib diisi.',
            'kredit.numeric' => 'Nominal kredit harus berupa angka.',
            'kredit.min' => 'Nominal kredit minimal Rp 0,01.',
        ];
    }
}
