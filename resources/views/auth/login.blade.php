<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIPBO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="bg-sipbo-bg min-h-screen flex items-center justify-center px-4 font-sans antialiased relative overflow-hidden">

    {{-- Background dot grid --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true"
        style="background-image: radial-gradient(rgba(212,175,55,0.07) 1px, transparent 1px);
                background-size: 26px 26px;">
    </div>

    {{-- Glow bawah-kiri --}}
    <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(212,175,55,0.08) 0%, transparent 70%);"
        aria-hidden="true"></div>

    {{-- Glow atas-kanan --}}
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(59,130,246,0.06) 0%, transparent 70%);"
        aria-hidden="true"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo area --}}
        <div class="text-center mb-7">
            <div class="w-16 h-16 rounded-full border-2 border-sipbo-gold
                        flex items-center justify-center mx-auto mb-3"
                style="box-shadow: 0 0 28px rgba(212,175,55,0.18);">
                <span class="text-sipbo-gold text-2xl font-bold">SP</span>
            </div>
            <h1 class="text-xl font-bold text-sipbo-gold tracking-wide">SIPBO</h1>
            <p class="text-sm text-sipbo-text-muted mt-1">
                Sistem Informasi Pengelolaan Belanja Operasional
            </p>
            <p class="text-xs text-sipbo-text-muted mt-0.5">Polsek Bojongloa Kidul</p>
        </div>

        {{-- Card form --}}
        <div class="bg-sipbo-panel border border-sipbo-border rounded-2xl p-6 lg:p-8"
            style="box-shadow: 0 8px 40px rgba(0,0,0,0.45);">

            <h2 class="text-base font-semibold text-sipbo-text mb-0.5">Masuk ke Akun Anda</h2>
            <p class="text-xs text-sipbo-text-muted mb-5">
                Gunakan username dan password yang telah diberikan.
            </p>

            @if(session('success'))
            <div class="flash-success px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="flash-error px-4 py-3 rounded-xl mb-4 text-sm">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-sipbo-text mb-1.5" for="username">
                        Username
                    </label>
                    <input type="text" id="username" name="username"
                        value="{{ old('username') }}" required autofocus
                        placeholder="Masukkan username"
                        class="w-full bg-sipbo-bg border border-sipbo-border rounded-xl
                                  p-2.5 text-sm text-sipbo-text placeholder-sipbo-text-muted
                                  focus:ring-2 focus:ring-sipbo-gold focus:border-sipbo-gold
                                  transition">
                </div>

                <div>
                    <label class="block text-sm text-sipbo-text mb-1.5" for="password">
                        Password
                    </label>
                    <input type="password" id="password" name="password" required
                        placeholder="Masukkan password"
                        class="w-full bg-sipbo-bg border border-sipbo-border rounded-xl
                                  p-2.5 text-sm text-sipbo-text placeholder-sipbo-text-muted
                                  focus:ring-2 focus:ring-sipbo-gold focus:border-sipbo-gold
                                  transition">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="rounded border-sipbo-border bg-sipbo-bg
                                  text-sipbo-gold focus:ring-sipbo-gold">
                    <label for="remember" class="text-sm text-sipbo-text-muted cursor-pointer">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-sipbo-gold hover:bg-sipbo-gold-light
                           text-sipbo-bg py-2.5 rounded-xl text-sm font-semibold
                           transition active:scale-[0.98]">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-sipbo-text-muted mt-5">
            &copy; {{ date('Y') }} SIPBO &mdash; Polsek Bojongloa Kidul.
            Hak akses terbatas.
        </p>
    </div>

</body>

</html>