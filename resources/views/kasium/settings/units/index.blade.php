<!-- resources/views/kasium/settings/units/index.blade.php -->
@extends('layouts.app')
@section('title', 'Settings | Unit Kerja')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel border border-sipbo-border dark:border-light-border rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border flex justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar Unit Kerja</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">Kelola unit kerja di lingkungan Polsek.</p>
        </div>
        <x-btn-primary size="sm" onclick="openModal('unit-create')">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Unit
        </x-btn-primary>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3">Kode</th>
                    <th class="text-left p-3">Nama Unit</th>
                    <th class="text-center p-3 hidden md:table-cell">Jumlah User</th>
                    <th class="text-center p-3 hidden md:table-cell">Jumlah Pengajuan</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $u)
                <tr class="border-b border-sipbo-border/50 dark:border-light-border/50 hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light">
                    <td class="p-3">
                        <span class="bg-sipbo-panel-light dark:bg-light-panel-light text-sipbo-text-muted dark:text-light-text-muted px-2 py-0.5 rounded-full text-[10px]">
                            {{ $u->kode_unit }}
                        </span>
                    </td>
                    <td class="p-3 font-medium text-sipbo-text dark:text-light-text">{{ $u->nama_unit }}</td>
                    <td class="p-3 text-center text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">{{ $u->users_count }}</td>
                    <td class="p-3 text-center text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">{{ $u->pengajuans_count }}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <x-btn-secondary size="sm"
                                onclick="openEditUnit({{ $u->id }}, '{{ addslashes($u->nama_unit) }}', '{{ $u->kode_unit }}')">
                                <i data-lucide="pencil" class="w-3 h-3"></i> Edit
                            </x-btn-secondary>
                            <form action="{{ route('kasium.settings.units.destroy', $u->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <x-btn-danger type="submit" size="sm"
                                    onclick="return confirm('Hapus unit {{ addslashes($u->nama_unit) }}?')">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                </x-btn-danger>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-sipbo-text-muted dark:text-light-text-muted">Belum ada unit kerja.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-sipbo-border dark:border-light-border">
        {{ $units->links() }}
    </div>
</div>

{{-- ============ MODAL CREATE ============ --}}
<x-modal id="unit-create" title="Tambah Unit Kerja" size="sm">
    <form action="{{ route('kasium.settings.units.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Nama Unit</label>
            <input type="text" name="nama_unit" value="{{ old('nama_unit') }}" required maxlength="100"
                class="sipbo-input" placeholder="Contoh: Unit Lalu Lintas">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Kode Unit</label>
            <input type="text" name="kode_unit" value="{{ old('kode_unit') }}" required maxlength="20"
                class="sipbo-input uppercase" placeholder="Contoh: LANTAS">
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <x-btn-secondary type="button" onclick="closeModal('unit-create')">Batal</x-btn-secondary>
            <x-btn-primary type="submit">Simpan</x-btn-primary>
        </div>
    </form>
</x-modal>

{{-- ============ MODAL EDIT ============ --}}
<x-modal id="unit-edit" title="Edit Unit Kerja" size="sm">
    <form id="form-edit-unit" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Nama Unit</label>
            <input type="text" name="nama_unit" id="edit-unit-nama" required maxlength="100" class="sipbo-input">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Kode Unit</label>
            <input type="text" name="kode_unit" id="edit-unit-kode" required maxlength="20" class="sipbo-input uppercase">
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <x-btn-secondary type="button" onclick="closeModal('unit-edit')">Batal</x-btn-secondary>
            <x-btn-primary type="submit">Simpan Perubahan</x-btn-primary>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>
    function openEditUnit(id, nama, kode) {
        document.getElementById('form-edit-unit').action = `/kasium/settings/units/${id}`;
        document.getElementById('edit-unit-nama').value = nama;
        document.getElementById('edit-unit-kode').value = kode;
        openModal('unit-edit');
    }
</script>
@endpush

@endsection