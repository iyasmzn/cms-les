@extends('layouts.public')

@push('head')
<style>
    .course-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0a1628 100%);
        position: relative;
        overflow: hidden;
    }
    .course-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 70% at 10% 50%, rgba(217,119,6,.25) 0%, transparent 55%),
            radial-gradient(ellipse 50% 50% at 90% 10%, rgba(251,191,36,.12) 0%, transparent 50%);
    }
    .course-input {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: .75rem;
        padding: .625rem .875rem;
        font-size: .875rem;
        background: var(--bg, #fff);
        color: var(--text);
        transition: border-color .15s, box-shadow .15s;
    }
    .course-input:focus {
        outline: none;
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217,119,6,.15);
    }
    .course-label { display:block; font-size:.8125rem; font-weight:600; margin-bottom:.375rem; color:var(--text); }
    .course-hint { font-size:.75rem; }
    .channel-card { display:block; border:1px solid var(--border); border-radius:1rem; padding:1rem; cursor:pointer; transition:border-color .15s, background-color .15s; }
    .channel-card.is-active { border-color:#d97706; background:rgba(217,119,6,.06); }
    .proof-dropzone {
        display:block; width:100%; padding:1rem; cursor:pointer;
        border:1px dashed var(--border); border-radius:1rem;
        transition:border-color .15s, background-color .15s;
    }
    .proof-dropzone:hover, .proof-dropzone.is-dragging { border-color:#d97706; background:rgba(217,119,6,.06); }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    $rupiah = fn ($n) => 'Rp'.number_format((float) $n, 0, ',', '.');

    $registration = $payment->member;
    $group = $registration?->group;
    $session = $payment->session;

    $channels = collect(['cash'])
        ->when($bankAccounts->isNotEmpty(), fn ($channels) => $channels->push('transfer'))
        ->when($qrisAccounts->isNotEmpty(), fn ($channels) => $channels->push('qris'));

    $defaultMethod = old('method', $channels->contains('transfer') ? 'transfer' : $channels->first());
    $defaultAccount = old('payment_account_id', $defaultMethod === 'qris'
        ? $qrisAccounts->first()?->id
        : $bankAccounts->first()?->id);

    // Switching channel must move the selection to a destination of that type,
    // otherwise a QRIS payment could arrive tagged with a bank account.
    $resetAccount = sprintf(
        "account = method === 'qris' ? '%s' : (method === 'transfer' ? '%s' : '')",
        $qrisAccounts->first()?->id,
        $bankAccounts->first()?->id,
    );
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-12 sm:pt-36 sm:pb-14">
    <x-hero-geo />
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 z-10" data-aos="fade-up">
        <a href="{{ route('courses.billing', ['registration' => $payment->group_member_id]) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-white/60 hover:text-white transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Bills &amp; payments
        </a>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">Settle This Bill</h1>
        <p class="text-white/70 text-sm sm:text-base mt-2">
            Choose how you are paying, then tell us — an admin verifies it before the bill turns green.
        </p>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

    @if($errors->any())
        <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800" data-aos="fade-up">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══════════════════ BILL SUMMARY ══════════════════════ --}}
    <div class="fi-card border p-5 sm:p-6" style="border-color:var(--border)" data-aos="fade-up">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                @if($group?->institution)
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600">{{ $group->institution->name }}</span>
                @endif
                <h2 class="font-bold text-lg mt-0.5" style="color:var(--text)">
                    {{ $session?->topic ?: ($session ? 'Session fee' : 'Course fee') }}
                </h2>
                <dl class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm" style="color:var(--muted)">
                    @if($group)
                        <div class="flex items-center gap-1.5">🏊 <span>{{ $group->name }}</span></div>
                    @endif
                    @if($session)
                        <div class="flex items-center gap-1.5">🗓️ <span>{{ $session->date->translatedFormat('D, d M Y') }}</span></div>
                    @endif
                    <div class="flex items-center gap-1.5">👤 <span>{{ $registration?->full_name }}</span></div>
                </dl>
            </div>
            <div class="text-right shrink-0">
                <div class="text-[11px] font-bold uppercase tracking-widest" style="color:var(--muted)">Amount due</div>
                <div class="text-2xl font-extrabold tabular-nums" style="color:var(--text)">{{ $rupiah($payment->amount) }}</div>
            </div>
        </div>

        @if($payment->rejection_reason)
            <div class="mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
                <span class="font-bold">Previous confirmation rejected:</span> {{ $payment->rejection_reason }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════ PAYMENT FORM ══════════════════════ --}}
    <form method="POST" action="{{ route('courses.bills.pay.store', $payment) }}" enctype="multipart/form-data"
          x-data="{ method: '{{ $defaultMethod }}', account: '{{ $defaultAccount }}', busy: false }" class="space-y-6" data-aos="fade-up">
        @csrf

        {{-- Channel picker --}}
        <div class="fi-card border p-5 sm:p-6" style="border-color:var(--border)">
            <h3 class="font-bold mb-1" style="color:var(--text)">1. How are you paying?</h3>
            <p class="text-sm mb-4" style="color:var(--muted)">Pick one channel.</p>

            <div class="grid gap-3 {{ $channels->count() === 1 ? 'sm:grid-cols-1' : ($channels->count() === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-3') }}">
                @if($channels->contains('cash'))
                    <label class="channel-card" :class="{ 'is-active': method === 'cash' }">
                        <input type="radio" name="method" value="cash" x-model="method" @change="{{ $resetAccount }}" class="sr-only">
                        <div class="text-2xl">💵</div>
                        <div class="font-bold mt-1.5" style="color:var(--text)">Cash</div>
                        <p class="text-xs mt-0.5" style="color:var(--muted)">Pay in person at the front desk.</p>
                    </label>
                @endif

                @if($channels->contains('transfer'))
                    <label class="channel-card" :class="{ 'is-active': method === 'transfer' }">
                        <input type="radio" name="method" value="transfer" x-model="method" @change="{{ $resetAccount }}" class="sr-only">
                        <div class="text-2xl">🏦</div>
                        <div class="font-bold mt-1.5" style="color:var(--text)">Bank Transfer</div>
                        <p class="text-xs mt-0.5" style="color:var(--muted)">Send to one of our accounts.</p>
                    </label>
                @endif

                @if($channels->contains('qris'))
                    <label class="channel-card" :class="{ 'is-active': method === 'qris' }">
                        <input type="radio" name="method" value="qris" x-model="method" @change="{{ $resetAccount }}" class="sr-only">
                        <div class="text-2xl">📱</div>
                        <div class="font-bold mt-1.5" style="color:var(--text)">QRIS</div>
                        <p class="text-xs mt-0.5" style="color:var(--muted)">Scan with any e-wallet or m-banking.</p>
                    </label>
                @endif
            </div>

            @if($bankAccounts->isEmpty() && $qrisAccounts->isEmpty())
                <p class="text-sm mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                    No transfer or QRIS destination has been published yet, so cash is the only option right now.
                </p>
            @endif
        </div>

        {{-- Cash instructions --}}
        <div x-show="method === 'cash'" x-cloak class="fi-card border p-5 sm:p-6" style="border-color:var(--border)">
            <h3 class="font-bold mb-1" style="color:var(--text)">2. Pay at the front desk</h3>
            <p class="text-sm" style="color:var(--muted)">
                Hand {{ $rupiah($payment->amount) }} to the course admin and mention your name
                @if($registration?->registration_number)
                    or registration number <span class="font-mono font-semibold">{{ $registration->registration_number }}</span>
                @endif.
                Submitting this form tells the admin to expect you — the bill is marked paid once they confirm receipt.
            </p>
        </div>

        {{-- Bank accounts --}}
        @if($bankAccounts->isNotEmpty())
            <div x-show="method === 'transfer'" x-cloak class="fi-card border p-5 sm:p-6" style="border-color:var(--border)">
                <h3 class="font-bold mb-1" style="color:var(--text)">2. Transfer to one of these accounts</h3>
                <p class="text-sm mb-4" style="color:var(--muted)">Transfer exactly {{ $rupiah($payment->amount) }}, then pick the account you used.</p>

                <div class="space-y-3">
                    @foreach($bankAccounts as $account)
                        <label class="channel-card" :class="{ 'is-active': account === '{{ $account->id }}' }">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="payment_account_id" value="{{ $account->id }}"
                                       x-model="account" class="mt-1 accent-amber-600">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold" style="color:var(--text)">{{ $account->displayName() }}</div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="font-mono text-lg font-extrabold tracking-wide" style="color:var(--text)">{{ $account->account_number }}</span>
                                        <button type="button"
                                                x-data="{ copied: false }"
                                                @click.prevent="navigator.clipboard.writeText('{{ $account->account_number }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                                class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color:var(--border); color:var(--muted)">
                                            <span x-show="! copied">Copy</span>
                                            <span x-show="copied" x-cloak class="text-green-600">Copied</span>
                                        </button>
                                    </div>
                                    <div class="text-sm mt-0.5" style="color:var(--muted)">a.n. {{ $account->account_holder }}</div>
                                    @if($account->instructions)
                                        <p class="text-xs mt-1.5" style="color:var(--muted)">{{ $account->instructions }}</p>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- QRIS --}}
        @if($qrisAccounts->isNotEmpty())
            <div x-show="method === 'qris'" x-cloak class="fi-card border p-5 sm:p-6" style="border-color:var(--border)">
                <h3 class="font-bold mb-1" style="color:var(--text)">2. Scan this QRIS</h3>
                <p class="text-sm mb-4" style="color:var(--muted)">Pay exactly {{ $rupiah($payment->amount) }} with any QRIS-capable app.</p>

                <div class="space-y-4">
                    @foreach($qrisAccounts as $account)
                        <label class="channel-card" :class="{ 'is-active': account === '{{ $account->id }}' }">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="payment_account_id" value="{{ $account->id }}"
                                       x-model="account" class="mt-1 accent-amber-600">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold" style="color:var(--text)">{{ $account->displayName() }}</div>
                                    @if($account->instructions)
                                        <p class="text-xs mt-1" style="color:var(--muted)">{{ $account->instructions }}</p>
                                    @endif
                                    <img src="{{ $account->qrisUrl() }}" alt="QRIS {{ $account->displayName() }}"
                                         class="mt-3 w-full max-w-xs rounded-xl border" style="border-color:var(--border)">
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Proof + note --}}
        <div class="fi-card border p-5 sm:p-6 space-y-4" style="border-color:var(--border)">
            <div>
                <h3 class="font-bold mb-1" style="color:var(--text)">3. Confirm your payment</h3>
                <p class="text-sm" style="color:var(--muted)">
                    <span x-show="method === 'cash'" x-cloak>A receipt photo is optional for cash.</span>
                    <span x-show="method !== 'cash'" x-cloak>Attach a screenshot or photo of your transfer receipt.</span>
                </p>
            </div>

            <div x-data="proofUpload()">
                <label for="proof" class="course-label">
                    Proof of payment
                    <span x-show="method !== 'cash'" x-cloak class="text-red-500">*</span>
                    <span x-show="method === 'cash'" x-cloak style="color:var(--muted)">(optional)</span>
                </label>

                {{-- Drop zone doubles as the file picker; the input itself stays
                     in the DOM so the browser still posts the file. --}}
                <label for="proof"
                       class="proof-dropzone"
                       :class="{ 'is-dragging': dragging }"
                       @dragover.prevent="dragging = true"
                       @dragleave.prevent="dragging = false"
                       @drop.prevent="dragging = false; take($event.dataTransfer.files[0])">
                    <template x-if="! preview && ! busy">
                        <div class="text-center py-2">
                            <div class="text-3xl">🧾</div>
                            <p class="text-sm font-semibold mt-2" style="color:var(--text)">Choose an image or drop it here</p>
                            <p class="course-hint mt-0.5" style="color:var(--muted)">JPG, PNG, or WebP · shrunk automatically before upload.</p>
                        </div>
                    </template>

                    <template x-if="busy">
                        <div class="text-center py-4">
                            <div class="text-3xl animate-pulse">⏳</div>
                            <p class="text-sm font-semibold mt-2" style="color:var(--text)">Compressing image…</p>
                        </div>
                    </template>

                    <template x-if="preview && ! busy">
                        <div class="flex items-start gap-4">
                            <img :src="preview" alt="Proof preview"
                                 class="w-28 h-28 object-cover rounded-xl border shrink-0" style="border-color:var(--border)">
                            <div class="min-w-0 flex-1 text-left">
                                <p class="text-sm font-bold truncate" style="color:var(--text)" x-text="name"></p>
                                <p class="course-hint mt-0.5" style="color:var(--muted)" x-text="size"></p>
                                <p class="course-hint mt-1 text-green-600" x-show="savedPercent > 0" x-cloak>
                                    Compressed <span x-text="savedPercent"></span>% smaller before upload.
                                </p>
                                <p class="course-hint mt-2 text-red-600" x-show="tooLarge" x-cloak>
                                    Still over 4 MB — please pick a smaller image.
                                </p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs font-bold" style="color:var(--primary)">Replace</span>
                                    <button type="button" @click.prevent="clear()" class="text-xs font-bold text-red-600">Remove</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </label>

                <input type="file" id="proof" name="proof" accept="image/jpeg,image/png,image/webp"
                       class="sr-only" x-ref="input" @change="take($event.target.files[0])">

                @error('proof')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="payer_note" class="course-label">Note for the admin <span style="color:var(--muted)">(optional)</span></label>
                <textarea id="payer_note" name="payer_note" rows="2" maxlength="500"
                          class="course-input @error('payer_note') border-red-400 @enderror"
                          placeholder="e.g. Transferred from my mother's account">{{ old('payer_note') }}</textarea>
                @error('payer_note')<p class="course-hint text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" :disabled="busy" :class="{ 'opacity-50 cursor-not-allowed': busy }"
                    class="btn-primary inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold">
                <span x-text="busy ? 'Preparing image…' : 'Send confirmation'">Send confirmation</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
            <a href="{{ route('courses.billing', ['registration' => $payment->group_member_id]) }}"
               class="text-sm font-semibold" style="color:var(--muted)">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Keeps the real file input as the single source of truth (so the form
     * still posts normally) while showing a live preview, and shrinks the image
     * in the browser first — receipts straight from a phone camera are often
     * 4–8 MB, which the upload limit would otherwise reject.
     */
    function proofUpload() {
        const MAX_EDGE = 1600;
        const QUALITY = 0.8;

        return {
            preview: null,
            name: null,
            size: null,
            savedPercent: 0,
            tooLarge: false,
            dragging: false,
            // `busy` deliberately lives on the form scope so the submit button
            // can stay disabled until the compressed file is in the input.

            async take(file) {
                if (! file || ! file.type.startsWith('image/')) {
                    return;
                }

                this.busy = true;

                const original = file.size;
                const processed = await this.compress(file);

                // A dropped or re-encoded file never reaches the input by itself.
                const transfer = new DataTransfer();
                transfer.items.add(processed);
                this.$refs.input.files = transfer.files;

                URL.revokeObjectURL(this.preview);

                this.preview = URL.createObjectURL(processed);
                this.name = processed.name;
                this.size = (processed.size / 1024 / 1024).toFixed(2) + ' MB';
                this.savedPercent = processed.size < original
                    ? Math.round((1 - processed.size / original) * 100)
                    : 0;
                this.tooLarge = processed.size > 4 * 1024 * 1024;
                this.busy = false;
            },

            /**
             * Downscale to fit MAX_EDGE and re-encode as JPEG. Returns the
             * original file whenever the browser can't decode it or the result
             * would not actually be smaller.
             */
            async compress(file) {
                try {
                    const bitmap = await createImageBitmap(file);
                    const scale = Math.min(1, MAX_EDGE / Math.max(bitmap.width, bitmap.height));

                    if (scale === 1 && file.size <= 800 * 1024) {
                        return file;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(bitmap.width * scale);
                    canvas.height = Math.round(bitmap.height * scale);
                    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

                    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', QUALITY));

                    if (! blob || blob.size >= file.size) {
                        return file;
                    }

                    return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });
                } catch (error) {
                    return file;
                }
            },

            clear() {
                URL.revokeObjectURL(this.preview);

                this.$refs.input.value = '';
                this.preview = null;
                this.name = null;
                this.size = null;
                this.savedPercent = 0;
                this.tooLarge = false;
            },
        };
    }
</script>
@endpush
