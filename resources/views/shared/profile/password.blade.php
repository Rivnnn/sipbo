@extends('layouts.app')
@section('title', 'Ubah Password')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl p-4 lg:p-6 max-w-lg">

    <h3 class="font-semibold text-sipbo-gold text-sm lg:text-base mb-1">Ubah Password Akun Saya</h3>
    <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mb-4">
        Hanya Anda yang bisa mengubah password akun Anda sendiri di sini &mdash; admin/KASIUM tidak bisa mengubahkannya untuk Anda.
    </p>

    @if($errors->any())
    <div class="flash-error rounded-xl p-3 mb-4 text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Password Saat Ini</label>
            <input type="password" name="current_password" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Password Baru</label>
            <input type="password" name="password" minlength="6" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" minlength="6" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit"
                class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                       px-4 py-2 rounded-xl text-sm font-semibold transition">
                Simpan Password Baru
            </button>
        </div>
    </form>
</div>
@endsection
