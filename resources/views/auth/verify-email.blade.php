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
            <h1 class="text-2xl font-extrabold text-white">Verifikasi Email</h1>
            <p class="text-sm mt-1" style="color:rgba(255,255,255,.65)">
                Satu langkah lagi untuk mengaktifkan akun
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-2xl p-8">

            @if(session('success'))
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-green-700 bg-green-50 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                     style="background:rgba(8,72,74,.1)">
                    <svg class="w-7 h-7" fill="none" stroke="var(--primary)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm" style="color:var(--muted)">
                    Kami telah mengirim link verifikasi ke
                    <span class="font-semibold" style="color:var(--text)">{{ auth()->user()->email }}</span>.
                    Silakan buka email Anda dan klik link tersebut untuk melanjutkan.
                </p>
                <p class="text-xs mt-3" style="color:var(--muted)">
                    Tidak menerima email? Cek folder spam, atau kirim ulang di bawah ini.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary w-full justify-center py-3">
                    Kirim Ulang Link Verifikasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit"
                        class="w-full text-center text-sm hover:opacity-75 transition-opacity"
                        style="color:var(--muted)">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</section>

@endsection
