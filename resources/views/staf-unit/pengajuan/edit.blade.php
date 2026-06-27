@extends('layouts.app')
@section('title', 'Edit Pengajuan Anggaran')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl p-4 lg:p-6 max-w-2xl">

    @if($errors->any())
    <div class="flash-error rounded-xl p-3 mb-4 text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('staf.pengajuan.update', $pengajuan->id) }}" method="POST"
        enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Program Anggaran</label>
            <select name="program_anggaran_id" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
                @foreach($programs as $p)
                <option value="{{ $p->id }}"
                    {{ old('program_anggaran_id', $pengajuan->program_anggaran_id) == $p->id ? 'selected' : '' }}>
                    {{ $p->nama_program }} ({{ $p->kode_program }})
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Judul Usulan</label>
            <input type="text" name="judul_usulan"
                value="{{ old('judul_usulan', $pengajuan->judul_usulan) }}"
                required maxlength="200"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Keterangan</label>
            <textarea name="keterangan" rows="3"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">{{ old('keterangan', $pengajuan->keterangan) }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Nominal Usulan</label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-sipbo-text-muted dark:text-light-text-muted text-sm">Rp</span>
                <input type="number" step="0.01" name="nominal_usulan"
                    value="{{ old('nominal_usulan', $pengajuan->nominal_usulan) }}" required
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 pl-9 text-sm text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
            </div>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Lampiran</label>
            @if($pengajuan->file_lampiran)
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mb-2">
                File saat ini:
                <a href="{{ Storage::url($pengajuan->file_lampiran) }}" target="_blank"
                    class="text-sipbo-gold hover:underline">Lihat File</a>
            </p>
            @endif
            <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text-muted dark:text-light-text-muted
                       file:bg-sipbo-gold file:text-sipbo-bg file:border-0 file:rounded-lg
                       file:px-3 file:py-1 file:mr-3 file:text-xs file:font-medium file:cursor-pointer">
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                Kosongkan jika tidak ingin mengganti file. Maks 2MB.
            </p>
        </div>

        <div class="flex gap-3 pt-1">
            <a href="{{ route('pengajuan.show', $pengajuan->id) }}"
                class="border border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted
                       px-4 py-2 rounded-xl text-sm
                       hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                Batal
            </a>
            <button type="submit"
                class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                       px-4 py-2 rounded-xl text-sm font-semibold transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection