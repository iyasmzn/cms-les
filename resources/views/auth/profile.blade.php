@extends('layouts.public')

@push('head')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')

@php
    $authorRequest = $user->authorRequest;
@endphp

<section class="min-h-screen -mt-17 pt-28 pb-16 px-4"
         style="background:linear-gradient(135deg,#082828 0%,#08484A 60%,#0a6060 100%)">

    <div class="w-full max-w-2xl mx-auto" data-aos="fade-up"
         x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'akun' }">

        {{-- Header --}}
        <div class="text-center mb-8">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 class="w-20 h-20 rounded-full object-cover mx-auto mb-4 ring-4 ring-white/20 bg-white/10">
            <h1 class="text-2xl font-extrabold text-white">Pengaturan Profil</h1>
            <p class="text-sm mt-1" style="color:rgba(255,255,255,.65)">
                Kelola informasi akun & aktivitas Anda
            </p>
        </div>

        {{-- ── Tabs ───────────────────────────────────────────── --}}
        <div class="flex flex-wrap gap-1 p-1 mb-6 rounded-2xl bg-white/10 backdrop-blur">
            @php
                $tabs = [
                    'akun' => 'Akun',
                    'pertanyaan' => 'Pertanyaan Saya ('.$myQuestions->count().')',
                    'komentar' => 'Komentar Saya ('.$myComments->count().')',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        class="flex-1 min-w-max px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
                        :class="tab === '{{ $key }}' ? 'bg-white text-gray-900 shadow' : 'text-white/80 hover:text-white'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- ── Tab: Akun ──────────────────────────────────────── --}}
        <div x-show="tab === 'akun'" x-cloak class="space-y-6">

            {{-- ── CTA / Status Jadi Author ────────────────────── --}}
            <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8">
                @if($user->isAuthor())
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-bold" style="color:var(--text)">Anda seorang Author</p>
                            <p class="text-sm mt-1" style="color:var(--muted)">
                                Anda dapat menulis dan menerbitkan konten. Terima kasih atas kontribusi Anda!
                            </p>
                        </div>
                    </div>
                @elseif($user->hasPendingAuthorRequest())
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-base font-bold" style="color:var(--text)">Permintaan Author Sedang Ditinjau</p>
                            <p class="text-sm mt-1" style="color:var(--muted)">
                                Admin akan meninjau permintaan Anda dalam 1–3 hari kerja.
                            </p>
                            <a href="{{ route('author-request.show') }}"
                               class="inline-flex items-center gap-1.5 text-sm font-semibold mt-2" style="color:var(--primary)">
                                Lihat status permintaan
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0"
                             style="background:color-mix(in oklab,var(--primary) 12%,white)">
                            <svg class="w-6 h-6" style="color:var(--primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-base font-bold" style="color:var(--text)">Ingin menjadi Author?</p>
                            <p class="text-sm mt-1" style="color:var(--muted)">
                                Bagikan tulisan dan ide Anda di {{ setting('site_name') }}.
                                @if($authorRequest && $authorRequest->status === 'rejected')
                                    Permintaan sebelumnya ditolak — Anda dapat mengajukan kembali.
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('author-request.show') }}"
                           class="btn-primary text-sm py-2.5 px-5 justify-center shrink-0">
                            Jadi Author
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                @endif
            </div>

            {{-- ── Informasi Profil ────────────────────────────── --}}
            <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8">
                <h2 class="text-lg font-bold mb-1" style="color:var(--text)">Informasi Akun</h2>
                <p class="text-sm mb-6" style="color:var(--muted)">Perbarui nama dan alamat email Anda.</p>

                @if(session('success'))
                    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium text-green-800 bg-green-50 border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->updateProfile->any())
                    <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-red-700 bg-red-50 border border-red-200">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->updateProfile->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    {{-- Foto profil --}}
                    <div x-data="{ preview: '{{ $user->avatar_url }}' }">
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Foto Profil
                        </label>
                        <div class="flex items-center gap-4">
                            <img :src="preview" alt="Foto profil"
                                 class="w-16 h-16 rounded-full object-cover ring-1 shrink-0"
                                 style="--tw-ring-color:var(--border)">
                            <div class="flex-1">
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif"
                                       @change="const f=$event.target.files[0]; if(f) preview=URL.createObjectURL(f)"
                                       class="block w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:cursor-pointer file:text-white file:bg-(--primary) hover:file:opacity-90 transition"
                                       style="color:var(--muted)">
                                <p class="text-xs mt-1.5" style="color:var(--muted)">JPG, PNG, WEBP, atau GIF. Maks 8MB.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                               style="border-color:var(--border);background:var(--bg);color:var(--text)">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                               style="border-color:var(--border);background:var(--bg);color:var(--text)">
                    </div>

                    <button type="submit" class="btn-primary justify-center py-3 px-6">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            {{-- ── Ganti Kata Sandi ────────────────────────────── --}}
            <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8">
                <h2 class="text-lg font-bold mb-1" style="color:var(--text)">Kata Sandi</h2>
                <p class="text-sm mb-6" style="color:var(--muted)">
                    @if($user->password)
                        Pastikan menggunakan kata sandi yang aman.
                    @else
                        Akun Anda login melalui Google. Buat kata sandi untuk dapat login manual.
                    @endif
                </p>

                @if(session('password_success'))
                    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium text-green-800 bg-green-50 border border-green-200">
                        {{ session('password_success') }}
                    </div>
                @endif

                @if($errors->updatePassword->any())
                    <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-red-700 bg-red-50 border border-red-200">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @if($user->password)
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                                Kata Sandi Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="current_password" required autocomplete="current-password"
                                   class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color:var(--border);background:var(--bg);color:var(--text)">
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Kata Sandi Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required minlength="8" autocomplete="new-password"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                               style="border-color:var(--border);background:var(--bg);color:var(--text)">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                               style="border-color:var(--border);background:var(--bg);color:var(--text)">
                    </div>

                    <button type="submit" class="btn-primary justify-center py-3 px-6">
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Tab: Pertanyaan Saya ───────────────────────────── --}}
        <div x-show="tab === 'pertanyaan'" x-cloak class="space-y-4">
            @forelse($myQuestions as $question)
                <div class="bg-white rounded-2xl shadow-xl p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        @if($question->is_answered)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                                ✓ Dijawab
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                Menunggu jawaban
                            </span>
                        @endif
                        @if($question->is_anonymous)
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">Anonim</span>
                        @endif
                        @if($question->post)
                            <a href="{{ route('blog.show', $question->post->slug) }}"
                               class="text-[11px] font-semibold text-amber-600 hover:underline truncate max-w-[16rem]">
                                {{ $question->post->title }}
                            </a>
                        @endif
                        <span class="text-[11px] text-gray-400 ml-auto">{{ $question->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-800 mb-2">{{ $question->question }}</p>
                    @if($question->answer)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs font-bold text-amber-700 mb-1">Jawaban {{ setting('site_name', config('app.name')) }}</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($question->answer)) !!}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                    <p class="text-sm text-gray-500 mb-4">Anda belum pernah mengirim pertanyaan.</p>
                    <a href="{{ route('questions.index') }}" class="btn-primary text-sm py-2.5 px-5 justify-center inline-flex">
                        Ajukan Pertanyaan
                    </a>
                </div>
            @endforelse
        </div>

        {{-- ── Tab: Komentar Saya ─────────────────────────────── --}}
        <div x-show="tab === 'komentar'" x-cloak class="space-y-4">
            @forelse($myComments as $comment)
                <div class="bg-white rounded-2xl shadow-xl p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        @if($comment->is_approved)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                                Tampil
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                Menunggu persetujuan
                            </span>
                        @endif
                        @if($comment->post)
                            <a href="{{ route('blog.show', $comment->post->slug) }}#komentar"
                               class="text-[11px] font-semibold text-amber-600 hover:underline truncate max-w-[16rem]">
                                {{ $comment->post->title }}
                            </a>
                        @endif
                        <span class="text-[11px] text-gray-400 ml-auto">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-800">{{ $comment->body }}</p>
                    @if($comment->admin_reply)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs font-bold text-amber-700 mb-1">Balasan Admin</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($comment->admin_reply)) !!}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                    <p class="text-sm text-gray-500">Anda belum pernah menulis komentar.</p>
                </div>
            @endforelse
        </div>

        <p class="text-center text-sm mt-6" style="color:rgba(255,255,255,.65)">
            <a href="{{ route('home') }}" class="hover:opacity-75 transition-opacity">← Kembali ke Beranda</a>
        </p>
    </div>
</section>

@endsection
