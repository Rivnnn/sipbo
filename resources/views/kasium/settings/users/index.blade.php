@extends('layouts.app')
@section('title', 'Settings - Users')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border
                flex justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar User</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                Kelola akun pengguna sistem.
            </p>
        </div>
        <a href="{{ route('kasium.settings.users.create') }}"
            class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                   px-3 lg:px-4 py-2 rounded-xl text-xs lg:text-sm font-semibold transition
                   flex items-center gap-2 flex-shrink-0">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Tambah User</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted
                           border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3 font-medium">Nama</th>
                    <th class="text-left p-3 font-medium">Username</th>
                    <th class="text-left p-3 font-medium hidden md:table-cell">Unit Kerja</th>
                    <th class="text-left p-3 font-medium">Role</th>
                    <th class="text-left p-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-sipbo-gold flex items-center justify-center
                                        font-bold text-sipbo-bg text-xs flex-shrink-0">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-sipbo-text dark:text-light-text">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted">{{ $u->username }}</td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">
                        {{ $u->unitKerja->nama_unit ?? '-' }}
                    </td>
                    <td class="p-3">
                        @php
                        $roleColor = match($u->role_label) {
                        'KASIUM' => 'bg-sipbo-gold/20 text-sipbo-gold border border-sipbo-gold/30',
                        'Pimpinan' => 'bg-purple-900/30 dark:bg-purple-100 text-purple-400 dark:text-purple-700 border border-purple-700/40 dark:border-purple-300',
                        default => 'bg-blue-900/30 dark:bg-blue-100 text-blue-400 dark:text-blue-700 border border-blue-700/40 dark:border-blue-300',
                        };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $roleColor }}">
                            {{ $u->role_label }}
                        </span>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-3">
                            <a href="{{ route('kasium.settings.users.edit', $u->id) }}"
                                class="text-sipbo-gold hover:underline">Edit</a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('kasium.settings.users.destroy', $u->id) }}" method="POST"
                                onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-400 dark:text-red-600 hover:underline">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-sm
                                           text-sipbo-text-muted dark:text-light-text-muted">
                        Belum ada user.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-sipbo-border dark:border-light-border">
        {{ $users->links() }}
    </div>
</div>
@endsection