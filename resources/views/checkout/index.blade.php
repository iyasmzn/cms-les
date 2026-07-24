@extends('layouts.public')

@php
$seo = [
    'title' => 'Checkout | '.setting('site_name', config('app.name')),
    'description' => 'Lengkapi data pengiriman untuk menyelesaikan pesanan Anda.',
    'canonical' => route('checkout.index'),
];
@endphp

@section('content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#082828 0%,#08484A 100%)" class="-mt-17 pt-30 pb-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-1.5 rounded-full mb-4"
             style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.2)">
            ✅ Checkout
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white">Selesaikan Pesanan</h1>
        <p class="text-sm mt-2" style="color:rgba(255,255,255,.7)">
            Isi data pengiriman, pesanan akan dikirim via WhatsApp
        </p>
    </div>
</div>

<section class="py-14" style="background:var(--bg)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('error'))
            <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium text-red-700 bg-red-50 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-5 gap-8">

            {{-- Checkout form --}}
            <div class="lg:col-span-3">
                <div class="fi-card p-6 sm:p-8" data-aos="fade-up">
                    <h2 class="font-extrabold text-xl mb-6" style="color:var(--text)">Data Penerima</h2>

                    <form method="POST" action="{{ route('checkout.process') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name', Auth::user()->name ?? '') }}" required
                                   placeholder="Ahmad Fauzi"
                                   class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color:var(--border);background:var(--bg-alt);color:var(--text)">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                                Nomor WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone"
                                   value="{{ old('phone') }}" required
                                   placeholder="0812-3456-7890"
                                   class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                                   style="border-color:var(--border);background:var(--bg-alt);color:var(--text)">
                            @error('phone')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                                Alamat Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" rows="3" required
                                      placeholder="Jl. Contoh No. 10, Kota, Provinsi, Kode Pos"
                                      class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2 resize-none"
                                      style="border-color:var(--border);background:var(--bg-alt);color:var(--text)">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">
                                Catatan (opsional)
                            </label>
                            <textarea name="notes" rows="2"
                                      placeholder="Pesan tambahan, warna sampul, dll."
                                      class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition-all focus:ring-2 resize-none"
                                      style="border-color:var(--border);background:var(--bg-alt);color:var(--text)">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Info WhatsApp --}}
                        <div class="flex items-start gap-3 p-4 rounded-xl"
                             style="background:var(--primary-50);border:1px solid var(--primary-200)">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24"
                                 style="color:var(--primary)">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <p class="text-sm" style="color:var(--primary-800)">
                                Setelah klik <strong>Kirim via WhatsApp</strong>, Anda akan diarahkan ke WhatsApp
                                dengan pesan yang sudah terisi otomatis. Konfirmasi dan kirim pesan tersebut.
                            </p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <a href="{{ route('cart.index') }}" class="btn-outline">← Kembali</a>
                            <button type="submit" class="btn-primary flex-1 justify-center">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Kirim via WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="lg:col-span-2">
                <div class="fi-card p-6 sticky top-24" data-aos="fade-up" data-aos-delay="60">
                    <h2 class="font-extrabold text-lg mb-5" style="color:var(--text)">Pesanan Anda</h2>

                    <div class="space-y-4 mb-5">
                        @foreach($items as $item)
                        @php /** @var \App\Models\Product $product */ $product = $item['product']; @endphp
                        <div class="flex gap-3">
                            <div class="w-12 shrink-0 rounded-lg overflow-hidden" style="aspect-ratio:1/1">
                                <img src="{{ $product->cover_url }}" alt="{{ $product->title }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold line-clamp-2 leading-snug" style="color:var(--text)">{{ $product->title }}</p>
                                <p class="text-xs mt-0.5" style="color:var(--muted)">{{ $item['qty'] }} × {{ $product->formatted_price }}</p>
                            </div>
                            <p class="shrink-0 text-sm font-bold" style="color:var(--primary)">
                                Rp {{ number_format($product->price * $item['qty'], 0, ',', '.') }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t flex justify-between font-extrabold text-lg"
                         style="border-color:var(--border)">
                        <span style="color:var(--text)">Total</span>
                        <span style="color:var(--primary)">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <p class="text-xs mt-4 text-center" style="color:var(--muted)">
                        * Ongkos kirim akan disepakati via WhatsApp
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
