<!-- resources/views/staf-unit/pengajuan/index.blade.php -->
@extends('layouts.app')
@section('title', 'Pengajuan Anggaran Saya')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel border border-sipbo-border dark:border-light-border rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border flex justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar Pengajuan Anggaran</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">Semua pengajuan yang Anda buat.</p>
        </div>
        <x-btn-primary size="sm" onclick="openModal('pengajuan-create')">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Pengajuan Baru
        </x-btn-primary>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3">Judul</th>
                    <th class="text-left p-3 hidden md:table-cell">Program</th>
                    <th class="text-right p-3">Nominal</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3 hidden lg:table-cell">Diperbarui</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                <tr class="border-b border-sipbo-border/50 dark:border-light-border/50 hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light">
                    <td class="p-3 font-medium text-sipbo-text dark:text-light-text">{{ $p->judul_usulan }}</td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">{{ $p->programAnggaran->nama_program }}</td>
                    <td class="p-3 text-right text-sipbo-text dark:text-light-text">Rp {{ number_format($p->nominal_usulan, 0, ',', '.') }}</td>
                    <td class="p-3">
                        @php [$class, $label] = $p->statusBadge(); @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">{{ $label }}</span>
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted text-xs hidden lg:table-cell">{{ $p->updated_at->diffForHumans() }}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            @if($p->status === 'draft')
                            <x-btn-secondary size="sm"
                                onclick="openEditPengajuan(
                                    {{ $p->id }},
                                    '{{ addslashes($p->judul_usulan) }}',
                                    {{ $p->program_anggaran_id }},
                                    {{ $p->nominal_usulan }},
                                    '{{ addslashes($p->keterangan ?? '') }}'
                                )">
                                <i data-lucide="pencil" class="w-3 h-3"></i> Edit
                            </x-btn-secondary>

                            <form id="form-kirim-{{ $p->id }}" action="{{ route('staf.pengajuan.kirim', $p->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <x-btn-primary type="button" size="sm"
                                onclick="confirmAction({message: 'Kirim pengajuan {{ addslashes($p->judul_usulan) }} untuk diverifikasi?', formId: 'form-kirim-{{ $p->id }}', confirmLabel: 'Ya, Kirim'})">
                                <i data-lucide="send" class="w-3 h-3"></i> Kirim
                            </x-btn-primary>
                            @endif

                            <x-btn-secondary size="sm" href="{{ route('pengajuan.show', $p->id) }}">
                                <i data-lucide="eye" class="w-3 h-3"></i> Detail
                            </x-btn-secondary>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-sipbo-text-muted dark:text-light-text-muted">Belum ada pengajuan. Klik "Pengajuan Baru" untuk membuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-sipbo-border dark:border-light-border">
        {{ $pengajuans->links() }}
    </div>
</div>

{{-- ============ MODAL CREATE ============ --}}
<x-modal id="pengajuan-create" title="Buat Pengajuan Baru" size="lg">
    <form action="{{ route('staf.pengajuan.store') }}" method="POST"
        enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Program Anggaran</label>
            <select name="program_anggaran_id" required class="sipbo-input">
                <option value="">-- Pilih Program --</option>
                @foreach($programs as $p)
                <option value="{{ $p->id }}" {{ old('program_anggaran_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nama_program }} ({{ $p->kode_program }})
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Judul Usulan</label>
            <input type="text" name="judul_usulan" value="{{ old('judul_usulan') }}" required maxlength="200"
                class="sipbo-input" placeholder="Contoh: ATK & Konsumsi Rapat Bulan Juni">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" class="sipbo-input"
                placeholder="Jelaskan detail kebutuhan (opsional)">{{ old('keterangan') }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Nominal Usulan</label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-sipbo-text-muted dark:text-light-text-muted text-sm font-medium">Rp</span>
                <input type="number" step="0.01" name="nominal_usulan" value="{{ old('nominal_usulan') }}" required
                    class="sipbo-input pl-10" placeholder="0">
            </div>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Lampiran (opsional)</label>
            <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"
                class="sipbo-input file:bg-sipbo-gold file:text-sipbo-bg file:border-0
                          file:rounded-lg file:px-3 file:py-1 file:mr-3
                          file:text-xs file:font-medium file:cursor-pointer">
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">PDF, JPG, PNG. Maks 2MB.</p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <x-btn-secondary type="button" onclick="closeModal('pengajuan-create')">Batal</x-btn-secondary>
            <x-btn-primary type="submit">Simpan sebagai Draft</x-btn-primary>
        </div>
    </form>
</x-modal>

{{-- ============ MODAL EDIT ============ --}}
<x-modal id="pengajuan-edit" title="Edit Pengajuan" size="lg">
    <form id="form-edit-pengajuan" method="POST"
        enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Program Anggaran</label>
            <select name="program_anggaran_id" id="edit-pengajuan-program" required class="sipbo-input">
                @foreach($programs as $p)
                <option value="{{ $p->id }}">{{ $p->nama_program }} ({{ $p->kode_program }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Judul Usulan</label>
            <input type="text" name="judul_usulan" id="edit-pengajuan-judul" required maxlength="200" class="sipbo-input">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Keterangan</label>
            <textarea name="keterangan" id="edit-pengajuan-keterangan" rows="3" class="sipbo-input"></textarea>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Nominal Usulan</label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-sipbo-text-muted dark:text-light-text-muted text-sm font-medium">Rp</span>
                <input type="number" step="0.01" name="nominal_usulan" id="edit-pengajuan-nominal" required
                    class="sipbo-input pl-10">
            </div>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Lampiran Baru (opsional)</label>
            <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"
                class="sipbo-input file:bg-sipbo-gold file:text-sipbo-bg file:border-0
                          file:rounded-lg file:px-3 file:py-1 file:mr-3
                          file:text-xs file:font-medium file:cursor-pointer">
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">Kosongkan jika tidak ingin mengganti file.</p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <x-btn-secondary type="button" onclick="closeModal('pengajuan-edit')">Batal</x-btn-secondary>
            <x-btn-primary type="submit">Simpan Perubahan</x-btn-primary>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>
    function openEditPengajuan(id, judul, programId, nominal, keterangan) {
        document.getElementById('form-edit-pengajuan').action = `/staf/pengajuan/${id}`;
        document.getElementById('edit-pengajuan-judul').value = judul;
        document.getElementById('edit-pengajuan-program').value = programId;
        document.getElementById('edit-pengajuan-nominal').value = nominal;
        document.getElementById('edit-pengajuan-keterangan').value = keterangan;
        openModal('pengajuan-edit');
    }
</script>
@endpush

@endsection