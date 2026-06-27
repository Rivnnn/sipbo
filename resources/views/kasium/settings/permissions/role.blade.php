@extends('layouts.app')
@section('title', 'Permission Manager - Per Role')

@section('content')
<div class="space-y-5">

    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5">
        <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Permission Per Role</h2>
        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
            Atur hak akses untuk setiap role.
            Role <span class="text-sipbo-gold font-medium">KASIUM</span> dikunci dan tidak dapat diubah.
        </p>
    </div>

    @foreach($roles as $role)
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl overflow-hidden
                {{ $role->name === 'kasium' ? 'opacity-60' : '' }}">

        <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border
                    flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h3 class="font-semibold text-sipbo-text dark:text-light-text text-sm lg:text-base">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </h3>
                @if($role->name === 'kasium')
                <span class="text-[10px] bg-red-900/30 dark:bg-red-50 text-red-400 dark:text-red-700
                             border border-red-700/40 dark:border-red-200
                             px-2 py-0.5 rounded-full">🔒 Terkunci</span>
                @endif
            </div>
            <span class="text-xs text-sipbo-text-muted dark:text-light-text-muted">
                {{ $role->permissions->count() }} permission aktif
            </span>
        </div>

        @if($role->name !== 'kasium')
        <form action="{{ route('kasium.settings.permissions.role.update', $role->name) }}" method="POST">
            @csrf @method('PUT')

            <div class="p-4 lg:p-5 space-y-4">
                @foreach($permissions as $group => $perms)
                <div>
                    <p class="text-xs text-sipbo-gold uppercase tracking-wider mb-2 font-semibold">{{ $group }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($perms as $perm)
                        @php $isActive = $role->hasPermissionTo($perm->name); @endphp
                        <label class="flex items-center gap-3 p-2.5 rounded-xl cursor-pointer transition
                                      {{ $isActive
                                           ? 'bg-sipbo-gold/10 border border-sipbo-gold/30'
                                           : 'bg-sipbo-bg dark:bg-light-panel-light border border-sipbo-border dark:border-light-border' }}
                                      hover:border-sipbo-gold/50">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                {{ $isActive ? 'checked' : '' }}
                                class="rounded border-sipbo-border bg-sipbo-bg text-sipbo-gold
                                       focus:ring-sipbo-gold focus:ring-offset-0">
                            <span class="text-xs text-sipbo-text dark:text-light-text">{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="px-4 pb-4 lg:px-5 lg:pb-5">
                <button type="submit"
                    class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                           px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Simpan Permission {{ ucfirst(str_replace('_',' ', $role->name)) }}
                </button>
            </div>
        </form>

        @else
        <div class="p-4 lg:p-5 space-y-4">
            @foreach($permissions as $group => $perms)
            <div>
                <p class="text-xs text-sipbo-gold uppercase tracking-wider mb-2 font-semibold">{{ $group }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($perms as $perm)
                    @php $isActive = $role->hasPermissionTo($perm->name); @endphp
                    <div class="flex items-center gap-3 p-2.5 rounded-xl
                                {{ $isActive
                                     ? 'bg-sipbo-gold/10 border border-sipbo-gold/20'
                                     : 'bg-sipbo-bg dark:bg-light-panel-light border border-sipbo-border dark:border-light-border' }}">
                        <span class="text-sipbo-gold text-sm">{{ $isActive ? '✓' : '✕' }}</span>
                        <span class="text-xs text-sipbo-text-muted dark:text-light-text-muted">{{ $perm->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>
@endsection