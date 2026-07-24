@extends('layouts.public')

@section('content')

<section class="min-h-screen flex items-center justify-center -mt-17 py-20 px-4"
         style="background:linear-gradient(135deg,#082828 0%,#08484A 60%,#0a6060 100%)">

    <div class="w-full max-w-lg" data-aos="fade-up">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Jadi Author</h1>
            <p class="text-sm mt-1" style="color:rgba(255,255,255,.65)">
                Ajukan permintaan untuk menulis konten di {{ setting('site_name') }}
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-8">

            @if(session('success'))
                <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium text-green-800 bg-green-50 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium text-blue-800 bg-blue-50 border border-blue-200">
                    {{ session('info') }}
                </div>
            @endif

            {{-- Status permintaan yang sudah ada --}}
            @if($existingRequest)
                <div class="mb-6 rounded-2xl p-5 border-2
                    @if($existingRequest->status === 'approved') border-green-200 bg-green-50
                    @elseif($existingRequest->status === 'rejected') border-red-200 bg-red-50
                    @else border-yellow-200 bg-yellow-50 @endif">

                    <div class="flex items-start gap-3">
                        @if($existingRequest->status === 'approved')
                            <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-green-800">Permintaan Disetujui!</p>
                                <p class="text-sm text-green-700 mt-1">Selamat! Anda sekarang sudah menjadi author.</p>
                            </div>
                        @elseif($existingRequest->status === 'rejected')
                            <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-red-800">Permintaan Ditolak</p>
                                @if($existingRequest->admin_notes)
                                    <p class="text-sm text-red-700 mt-1">Catatan admin: {{ $existingRequest->admin_notes }}</p>
                                @endif
                                <p class="text-sm text-red-600 mt-2">Anda dapat mengajukan kembali dengan melengkapi form di bawah.</p>
                            </div>
                        @else
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-yellow-800">Sedang Ditinjau</p>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Permintaan Anda dikirim pada {{ $existingRequest->created_at->translatedFormat('d F Y') }}.
                                    Admin akan menghubungi via email dalam 1–3 hari kerja.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Form hanya tampil jika belum pending atau sudah rejected --}}
            @if(!$existingRequest || $existingRequest->status === 'rejected')
                @if($errors->any())
                    <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-red-700 bg-red-50 border border-red-200">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('author-request.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Mengapa Anda ingin menjadi author?
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" rows="5" required minlength="50"
                                  placeholder="Ceritakan pengalaman menulis Anda, topik yang akan Anda tulis, dan mengapa Anda ingin berkontribusi... (minimal 50 karakter)"
                                  class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all resize-none focus:ring-2"
                                  style="border-color:var(--border);background:var(--bg);color:var(--text)">{{ old('reason', $existingRequest?->reason) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                            Link Portofolio / Blog
                            <span class="text-xs font-normal" style="color:var(--muted)">(opsional)</span>
                        </label>
                        <input type="url" name="portfolio_url"
                               value="{{ old('portfolio_url', $existingRequest?->portfolio_url) }}"
                               placeholder="https://blog-saya.com"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                               style="border-color:var(--border);background:var(--bg);color:var(--text)">
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-3">
                        Kirim Permintaan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>
            @endif

            <p class="text-center text-sm mt-6" style="color:var(--muted)">
                <a href="{{ route('home') }}" class="hover:opacity-75 transition-opacity">← Kembali ke Beranda</a>
            </p>
        </div>
    </div>
</section>

@endsection
