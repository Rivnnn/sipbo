@extends('layouts.app')
@section('title', 'Permission Manager - Per User')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border">
        <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Permission Per User</h2>
        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
            Override permission untuk user tertentu, di luar bawaan role-nya.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted
                           border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3 font-medium">User</th>
                    <th class="text-left p-3 font-medium">Role</th>
                    <th class="text-left p-3 font-medium hidden md:table-cell">Unit</th>
                    <th class="text-center p-3 font-medium">Override Aktif</th>
                    <th class="text-left p-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition
                           {{ $u->id === auth()->id() ? 'opacity-50' : '' }}">
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-sipbo-gold flex items-center justify-center
                                        font-bold text-sipbo-bg text-xs flex-shrink-0">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-sipbo-text dark:text-light-text">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="p-3">
                        @php
                        $rc = match($u->role_label) {
                        'KASIUM' => 'bg-sipbo-gold/20 text-sipbo-gold border border-sipbo-gold/30',
                        'Pimpinan' => 'bg-purple-900/30 dark:bg-purple-100 text-purple-400 dark:text-purple-700 border border-purple-700/40 dark:border-purple-300',
                        default => 'bg-blue-900/30 dark:bg-blue-100 text-blue-400 dark:text-blue-700 border border-blue-700/40 dark:border-blue-300',
                        };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $rc }}">
                            {{ $u->role_label }}
                        </span>
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">
                        {{ $u->unitKerja->nama_unit ?? '-' }}
                    </td>
                    <td class="p-3 text-center">
                        @php $dc = $u->getDirectPermissions()->count(); @endphp
                        @if($dc > 0)
                        <span class="bg-sipbo-gold/20 text-sipbo-gold border border-sipbo-gold/30
                                     px-2 py-0.5 rounded-full text-[10px] font-medium">
                            {{ $dc }} override
                        </span>
                        @else
                        <span class="text-sipbo-text-muted dark:text-light-text-muted text-xs">Default role</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($u->id !== auth()->id())
                        <a href="{{ route('kasium.settings.permissions.user.show', $u->id) }}"
                            class="text-sipbo-gold hover:underline">Kelola</a>
                        @else
                        <span class="text-sipbo-text-muted dark:text-light-text-muted text-xs">Akun Anda</span>
                        @endif
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