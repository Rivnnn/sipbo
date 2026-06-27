@extends('layouts.app')
@section('title', 'Edit Unit Kerja')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl p-4 lg:p-6 max-w-lg">

    @if($errors->any())
    <div class="flash-error rounded-xl p-3 mb-4 text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('kasium.settings.units.update', $unit->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Nama Unit</label>
            <input type="text" name="nama_unit"
                value="{{ old('nama_unit', $unit->nama_unit) }}" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Kode Unit</label>
            <input type="text" name="kode_unit"
                value="{{ old('kode_unit', $unit->kode_unit) }}" required maxlength="20"
                style="text-transform:uppercase"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>
        <div class="flex gap-3 pt-1">
            <a href="{{ route('kasium.settings.units.index') }}"
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