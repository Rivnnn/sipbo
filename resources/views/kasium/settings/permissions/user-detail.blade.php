@extends('layouts.app')
@section('title', 'Permission User: ' . $user->name)

@section('content')
<div class="space-y-4 lg:space-y-5">

    <!-- INFO USER -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5
                flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-sipbo-gold flex items-center justify-center
                        font-bold text-sipbo-bg text-lg flex-shrink-0">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="font-semibold text-sipbo-text dark:text-light-text text-sm lg:text-base">
                    {{ $user->name }}
                </h2>
                <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted">
                    {{ $user->username }} &bull; {{ $user->role_label }}
                    &bull; {{ $user->unitKerja->nama_unit ?? 'Tanpa Unit' }}
                </p>
            </div>
        </div>
        <form id="form-reset-permission" action="{{ route('kasium.settings.permissions.user.reset', $user->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        <button type="button"
            onclick="confirmAction({message: 'Reset semua override permission user ini ke default role?', formId: 'form-reset-permission', danger: true, confirmLabel: 'Ya, Reset'})"
            class="border border-red-500/50 dark:border-red-400/50
                       text-red-400 dark:text-red-600
                       px-4 py-2 rounded-xl text-sm font-medium
                       hover:bg-red-900/20 dark:hover:bg-red-50 transition">
                Reset ke Default Role
            </button>
    </div>

    <!-- LEGEND -->
    <div class="flex flex-wrap gap-4 text-xs">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-sm bg-sipbo-gold/20 border border-sipbo-gold/40"></div>
            <span class="text-sipbo-text-muted dark:text-light-text-muted">Dari Role (bawaan)</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-sm bg-blue-900/40 dark:bg-blue-100 border border-blue-700/50 dark:border-blue-300"></div>
            <span class="text-sipbo-text-muted dark:text-light-text-muted">Override aktif (langsung ke user)</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-sm bg-sipbo-bg dark:bg-light-panel-light border border-sipbo-border dark:border-light-border"></div>
            <span class="text-sipbo-text-muted dark:text-light-text-muted">Tidak aktif</span>
        </div>
    </div>

    <!-- FORM -->
    <form action="{{ route('kasium.settings.permissions.user.update', $user->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="space-y-4">
            @foreach($allPermissions as $group => $perms)
            <div class="bg-sipbo-panel dark:bg-light-panel
                        border border-sipbo-border dark:border-light-border
                        dark:shadow-sm rounded-2xl overflow-hidden">
                <div class="px-4 lg:px-5 py-3 border-b border-sipbo-border dark:border-light-border">
                    <p class="text-xs text-sipbo-gold uppercase tracking-wider font-semibold">{{ $group }}</p>
                </div>
                <div class="p-4 lg:p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($perms as $perm)
                        @php
                        $fromRole = in_array($perm->name, $rolePermissions);
                        $directOverride= in_array($perm->name, $directPermissions);
                        $isActive = $fromRole || $directOverride;
                        @endphp
                        <label class="flex items-start gap-3 p-2.5 rounded-xl cursor-pointer transition
                                      hover:border-sipbo-gold/50
                                      @if($directOverride) bg-blue-900/25 dark:bg-blue-50 border border-blue-700/40 dark:border-blue-300
                                      @elseif($fromRole)   bg-sipbo-gold/10 border border-sipbo-gold/20
                                      @else                bg-sipbo-bg dark:bg-light-panel-light border border-sipbo-border dark:border-light-border @endif">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                {{ $isActive ? 'checked' : '' }}
                                class="rounded border-sipbo-border bg-sipbo-bg text-sipbo-gold
                                       focus:ring-sipbo-gold focus:ring-offset-0 mt-0.5">
                            <div>
                                <p class="text-xs text-sipbo-text dark:text-light-text">{{ $perm->name }}</p>
                                @if($directOverride)
                                <p class="text-[10px] text-blue-400 dark:text-blue-600 mt-0.5">override aktif</p>
                                @elseif($fromRole)
                                <p class="text-[10px] text-sipbo-gold/70 mt-0.5">dari role</p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex gap-3">
            <a href="{{ route('kasium.settings.permissions.user') }}"
                class="border border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted
                       px-4 py-2 rounded-xl text-sm
                       hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                Kembali
            </a>
            <button type="submit"
                class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                       px-4 py-2 rounded-xl text-sm font-semibold transition">
                Simpan Permission User
            </button>
        </div>
    </form>
</div>
@endsection