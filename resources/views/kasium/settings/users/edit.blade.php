@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl p-4 lg:p-6 max-w-lg">

    @if($errors->any())
    <div class="flash-error rounded-xl p-3 mb-4 text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('kasium.settings.users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Username</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                required maxlength="30"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Password Baru</label>
            <input type="password" name="password" minlength="6"
                placeholder="Kosongkan jika tidak ingin mengubah"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       placeholder-sipbo-text-muted focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Unit Kerja</label>
            <select name="unit_kerja_id"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
                <option value="">-- Tanpa Unit --</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}"
                    {{ old('unit_kerja_id', $user->unit_kerja_id) == $u->id ? 'selected' : '' }}>
                    {{ $u->nama_unit }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Role</label>
            <select name="role" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
                @foreach($roles as $key => $label)
                <option value="{{ $key }}"
                    {{ old('role', $user->roles->first()?->name) == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3 pt-1">
            <a href="{{ route('kasium.settings.users.index') }}"
                class="border border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted
                       px-4 py-2 rounded-xl text-sm
                       hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">Batal</a>
            <button type="submit"
                class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                       px-4 py-2 rounded-xl text-sm font-semibold transition">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection