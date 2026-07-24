@extends('layouts.public')

@section('content')

<section class="min-h-screen flex items-center justify-center -mt-17 py-20 px-4"
         style="background:linear-gradient(135deg,#082828 0%,#08484A 60%,#0a6060 100%)">

    <div class="w-full max-w-md" data-aos="fade-up">

        {{-- Logo --}}
        <div class="text-center mb-8">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/'.setting('site_logo')) }}"
                     alt="{{ setting('site_name') }}"
                     class="w-16 h-16 rounded-2xl object-contain mx-auto mb-4">
            @else
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-extrabold text-2xl">{{ strtoupper(substr(setting('site_name', 'Q'), 0, 1)) }}</span>
                </div>
            @endif
            <h1 class="text-2xl font-extrabold text-white">Buat Akun</h1>
            <p class="text-sm mt-1" style="color:rgba(255,255,255,.65)">
                Daftar untuk mulai berbelanja buku
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-2xl p-8">

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-red-700 bg-red-50 border border-red-200">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Ahmad Fauzi"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                           style="border-color:var(--border);background:var(--bg);color:var(--text)">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="nama@email.com"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                           style="border-color:var(--border);background:var(--bg);color:var(--text)">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Kata Sandi</label>
                    <input type="password" name="password" required
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                           style="border-color:var(--border);background:var(--bg);color:var(--text)">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Ulangi kata sandi"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                           style="border-color:var(--border);background:var(--bg);color:var(--text)">
                </div>

                <button type="submit" class="btn-primary w-full justify-center py-3">
                    Daftar Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

            @if(setting('oauth_google_enabled'))
            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px" style="background:var(--border)"></div>
                <span class="text-xs font-medium" style="color:var(--muted)">atau daftar dengan</span>
                <div class="flex-1 h-px" style="background:var(--border)"></div>
            </div>

            {{-- Google Register --}}
            <a href="{{ route('auth.google') }}"
               class="flex items-center justify-center gap-3 w-full px-4 py-3 rounded-xl border text-sm font-semibold transition-all hover:bg-gray-50"
               style="border-color:var(--border);color:var(--text)">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </a>
            @endif

            <p class="text-center text-sm mt-6" style="color:var(--muted)">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold hover:opacity-75 transition-opacity"
                   style="color:var(--primary)">Masuk di sini</a>
            </p>

            <p class="text-center text-sm mt-2" style="color:var(--muted)">
                <a href="{{ route('home') }}" class="hover:opacity-75 transition-opacity">← Kembali ke Beranda</a>
            </p>
        </div>
    </div>
</section>

@endsection
