<!-- resources/views/kasium/settings/users/index.blade.php -->
@extends('layouts.app')
@section('title', 'Settings - Users')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel border border-sipbo-border dark:border-light-border rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border flex justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar User</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">Kelola akun pengguna sistem.</p>
        </div>
        <x-btn-primary size="sm" onclick="openModal('user-create')">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Tambah User
        </x-btn-primary>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3">Nama</th>
                    <th class="text-left p-3">Username</th>
                    <th class="text-left p-3 hidden md:table-cell">Unit Kerja</th>
                    <th class="text-left p-3">Role</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="border-b border-sipbo-border/50 dark:border-light-border/50 hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light">
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-sipbo-gold flex items-center justify-center font-bold text-sipbo-bg text-xs flex-shrink-0">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-sipbo-text dark:text-light-text">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted">{{ $u->username }}</td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">{{ $u->unitKerja->nama_unit ?? '-' }}</td>
                    <td class="p-3">
                        @php
                        $roleColor = match($u->role_label) {
                        'KASIUM' => 'bg-sipbo-gold/20 text-sipbo-gold dark:bg-amber-100 dark:text-amber-800',
                        'Pimpinan' => 'bg-purple-900/40 text-purple-400 dark:bg-purple-100 dark:text-purple-800',
                        default => 'bg-blue-900/40 text-blue-400 dark:bg-blue-100 dark:text-blue-800',
                        };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $roleColor }}">{{ $u->role_label }}</span>
                    </td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            {{-- Tombol Edit: kirim data ke modal via JS --}}
                            <x-btn-secondary size="sm"
                                onclick="openEditUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->username }}', '{{ $u->unit_kerja_id }}', '{{ $u->roles->first()?->name }}')">
                                <i data-lucide="pencil" class="w-3 h-3"></i> Edit
                            </x-btn-secondary>

                            @if($u->id !== auth()->id())
                            <form action="{{ route('kasium.settings.users.destroy', $u->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <x-btn-danger type="submit" size="sm"
                                    onclick="return confirm('Hapus user {{ addslashes($u->name) }}?')">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                </x-btn-danger>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-sipbo-text-muted dark:text-light-text-muted">Belum ada user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-sipbo-border dark:border-light-border">
        {{ $users->links() }}
    </div>
</div>

{{-- ============ MODAL CREATE ============ --}}
<x-modal id="user-create" title="Tambah User Baru" size="md">
    <form action="{{ route('kasium.settings.users.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                class="sipbo-input" placeholder="Contoh: Bripka Andi Saputra">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Username</label>
            <input type="text" name="username" value="{{ old('username') }}" required maxlength="30"
                class="sipbo-input" placeholder="Contoh: andi.saputra">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Password</label>
            <input type="password" name="password" required minlength="6"
                class="sipbo-input" placeholder="Minimal 6 karakter">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Unit Kerja</label>
            <select name="unit_kerja_id" class="sipbo-input">
                <option value="">-- Tanpa Unit --</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ old('unit_kerja_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Role</label>
            <select name="role" required class="sipbo-input">
                <option value="">-- Pilih Role --</option>
                @foreach($roles as $key => $label)
                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <x-btn-secondary type="button" onclick="closeModal('user-create')">Batal</x-btn-secondary>
            <x-btn-primary type="submit">Simpan</x-btn-primary>
        </div>
    </form>
</x-modal>

{{-- ============ MODAL EDIT ============ --}}
<x-modal id="user-edit" title="Edit User" size="md">
    <form id="form-edit-user" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="edit-user-name" required maxlength="100" class="sipbo-input">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Username</label>
            <input type="text" name="username" id="edit-user-username" required maxlength="30" class="sipbo-input">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Password Baru</label>
            <input type="password" name="password" minlength="6"
                class="sipbo-input" placeholder="Kosongkan jika tidak ingin mengubah">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Unit Kerja</label>
            <select name="unit_kerja_id" id="edit-user-unit" class="sipbo-input">
                <option value="">-- Tanpa Unit --</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">Role</label>
            <select name="role" id="edit-user-role" required class="sipbo-input">
                @foreach($roles as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <x-btn-secondary type="button" onclick="closeModal('user-edit')">Batal</x-btn-secondary>
            <x-btn-primary type="submit">Simpan Perubahan</x-btn-primary>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>
    function openEditUser(id, name, username, unitId, role) {
        document.getElementById('form-edit-user').action = `/kasium/settings/users/${id}`;
        document.getElementById('edit-user-name').value = name;
        document.getElementById('edit-user-username').value = username;
        document.getElementById('edit-user-unit').value = unitId ?? '';
        document.getElementById('edit-user-role').value = role ?? '';
        openModal('user-edit');
    }
</script>
@endpush

@endsection