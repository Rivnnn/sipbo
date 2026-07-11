<?php
// app/Http/Requests/StorePengajuanRequest.php

namespace App\Http\Requests;

use App\Models\ProgramAnggaran;
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
            'program_anggaran_id' => [
                'required',
                'exists:program_anggarans,id',
                function ($attribute, $value, $fail) {
                    $program = ProgramAnggaran::find($value);
                    if (!$program) return;

                    $sisaPagu = $program->sisa_pagu;
                    $nominal  = (float) $this->input('nominal_usulan', 0);

                    if ($nominal > $sisaPagu) {
                        $fail(
                            "Nominal melebihi sisa pagu DIPA program ini. " .
                                "Sisa pagu: Rp " . number_format($sisaPagu, 0, ',', '.')
                        );
                    }
                },
            ],
            'judul_usulan'    => 'required|string|max:200',
            'keterangan'      => 'nullable|string|max:1000',
            'nominal_usulan'  => 'required|numeric|min:1000',
            'file_lampiran'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'program_anggaran_id.required' => 'Program anggaran wajib dipilih.',
            'judul_usulan.required'        => 'Judul usulan wajib diisi.',
            'nominal_usulan.required'      => 'Nominal usulan wajib diisi.',
            'nominal_usulan.min'           => 'Nominal minimal Rp 1.000.',
            'file_lampiran.mimes'          => 'Lampiran harus PDF, JPG, atau PNG.',
            'file_lampiran.max'            => 'Ukuran lampiran maksimal 2MB.',
        ];
    }
}
