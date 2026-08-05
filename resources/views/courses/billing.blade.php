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
    @media print {
        header, footer, .no-print { display: none !important; }
        .course-hero { background: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $rupiah = fn ($n) => 'Rp'.number_format((float) $n, 0, ',', '.');

    $billStyles = [
        'unpaid' => ['label' => 'Unpaid', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'review' => ['label' => 'Awaiting verification', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'paid'   => ['label' => 'Paid',   'class' => 'bg-green-50 text-green-700 border-green-200'],
        'waived' => ['label' => 'Waived', 'class' => 'bg-gray-100 text-gray-500 border-gray-200'],
    ];

    $methodLabels = \App\Models\CoursePayment::methodOptions();

    $billCount = $billed->sum(fn ($registration) => $registration->payments->count());
    $unpaidCount = $billed->sum(fn ($registration) => $registration->payments->where('status', 'unpaid')->count());
@endphp

{{-- ═══════════════════════ HERO ═══════════════════════════════ --}}
<section class="course-hero -mt-17 pt-32 pb-12 sm:pt-36 sm:pb-14">
    <x-hero-geo />
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 z-10" data-aos="fade-up">
        <a href="{{ route('courses.mine') }}" class="no-print inline-flex items-center gap-1.5 text-sm font-semibold text-white/60 hover:text-white transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            My Courses
        </a>

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 mb-5">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-xs font-bold text-amber-300 uppercase tracking-widest">Billing</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">Bills &amp; Payments</h1>
                <p class="text-white/70 text-sm sm:text-base mt-2">
                    Every session fee charged to your registrations, and what you have settled so far.
                </p>
            </div>

            @if($billCount > 0)
                <button type="button" onclick="window.print()"
                        class="no-print inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-sm font-bold hover:bg-white/20 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </button>
            @endif
        </div>
    </div>
</section>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    @if(session('success'))
        <div class="no-print flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800" data-aos="fade-up">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($registrations->isEmpty())
        <div class="fi-card p-10 text-center" data-aos="fade-up">
            <div class="text-5xl mb-4">🧾</div>
            <h2 class="font-bold text-lg mb-2" style="color:var(--text)">Nothing to Pay Yet</h2>
            <p class="text-sm mb-6" style="color:var(--muted)">You haven't registered for any course, so there are no bills on your account.</p>
            <a href="{{ route('courses.index') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold">
                Browse Courses
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    @else

        {{-- ═══════════════════ SUMMARY ═══════════════════════════ --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4" data-aos="fade-up">
            @php
                $cards = [
                    ['label' => 'Total Billed', 'value' => $totals['billed'], 'color' => 'var(--text)'],
                    ['label' => 'Paid', 'value' => $totals['paid'], 'color' => '#16a34a'],
                    ['label' => 'Outstanding', 'value' => $totals['outstanding'], 'color' => $totals['outstanding'] > 0 ? '#d97706' : 'var(--muted)'],
                    ['label' => 'Waived', 'value' => $totals['waived'], 'color' => 'var(--muted)'],
                ];
            @endphp
            @foreach($cards as $card)
                @if($card['label'] !== 'Waived' || $totals['waived'] > 0)
                    <div class="fi-card border p-4 sm:p-5" style="border-color:var(--border)">
                        <div class="text-[11px] font-bold uppercase tracking-widest" style="color:var(--muted)">{{ $card['label'] }}</div>
                        <div class="text-lg sm:text-xl font-extrabold mt-1.5 tabular-nums" style="color:{{ $card['color'] }}">{{ $rupiah($card['value']) }}</div>
                    </div>
                @endif
            @endforeach
        </section>

        @if($totals['outstanding'] > 0)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 flex items-start gap-4" data-aos="fade-up">
                <div class="text-2xl leading-none">⏳</div>
                <div class="min-w-0">
                    <h2 class="font-bold text-amber-900">{{ $rupiah($totals['outstanding']) }} outstanding</h2>
                    <p class="text-sm text-amber-800 mt-1">
                        {{ $unpaidCount }} {{ Str::plural('bill', $unpaidCount) }} still unpaid@if($totals['review'] > 0), and {{ $rupiah($totals['review']) }} waiting for the admin to verify@endif.
                        Open a bill to pay by cash, bank transfer, or QRIS.
                    </p>
                </div>
            </div>
        @endif

        {{-- ═══════════════════ FILTER ════════════════════════════ --}}
        @if($registrations->count() > 1)
            <div class="no-print flex flex-wrap items-center gap-2" data-aos="fade-up">
                <a href="{{ route('courses.billing') }}"
                   class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border transition-colors {{ $selected === null ? 'bg-amber-500 text-slate-900 border-amber-500' : '' }}"
                   @style(['color:var(--muted); border-color:var(--border)' => $selected !== null])>
                    All registrations
                </a>
                @foreach($registrations as $registration)
                    <a href="{{ route('courses.billing', ['registration' => $registration->id]) }}"
                       class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border transition-colors {{ $selected?->is($registration) ? 'bg-amber-500 text-slate-900 border-amber-500' : '' }}"
                       @style(['color:var(--muted); border-color:var(--border)' => ! $selected?->is($registration)])>
                        {{ $registration->group?->name ?? 'Group removed' }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ═══════════════════ BILLS ═════════════════════════════ --}}
        @if($billCount === 0)
            <div class="fi-card p-10 text-center" data-aos="fade-up">
                <div class="text-5xl mb-4">✅</div>
                <h2 class="font-bold text-lg mb-2" style="color:var(--text)">No Bills Yet</h2>
                <p class="text-sm" style="color:var(--muted)">
                    Nothing has been charged to {{ $selected ? 'this registration' : 'your registrations' }} so far. Session fees appear here as soon as the admin issues them.
                </p>
            </div>
        @endif

        @foreach($billed as $registration)
            @continue($registration->payments->isEmpty())
            @php
                $group = $registration->group;
                $registrationTotals = $registration->paymentTotals();
            @endphp

            <section class="fi-card border overflow-hidden" style="border-color:var(--border)" data-aos="fade-up">
                {{-- Registration header --}}
                <div class="p-5 sm:p-6 border-b" style="border-color:var(--border)">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            @if($group?->institution)
                                <span class="text-xs font-bold uppercase tracking-widest text-amber-600">{{ $group->institution->name }}</span>
                            @endif
                            <h2 class="font-bold text-lg mt-0.5" style="color:var(--text)">{{ $group?->name ?? 'Group removed' }}</h2>
                            <p class="text-sm mt-1" style="color:var(--muted)">
                                {{ $registration->full_name }}
                                @if($registration->registration_number)
                                    · <span class="font-mono text-[13px]">{{ $registration->registration_number }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            @if($registrationTotals['outstanding'] > 0)
                                <div class="text-[11px] font-bold uppercase tracking-widest" style="color:var(--muted)">Outstanding</div>
                                <div class="text-lg font-extrabold text-amber-600 tabular-nums">{{ $rupiah($registrationTotals['outstanding']) }}</div>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-green-600">✅ Fully settled</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Bill rows --}}
                <div class="divide-y" style="border-color:var(--border)">
                    @foreach($registration->payments as $payment)
                    @php
                        $bill = $billStyles[$payment->status] ?? $billStyles['unpaid'];
                        $session = $payment->session;
                    @endphp
                    <div class="p-4 sm:px-6 flex items-start gap-4" style="border-color:var(--border)">
                        {{-- Date chip --}}
                        <div class="shrink-0 w-14 text-center rounded-xl border py-2" style="border-color:var(--border)">
                            @if($session)
                                <div class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">{{ $session->date->format('M') }}</div>
                                <div class="text-xl font-extrabold leading-none mt-0.5" style="color:var(--text)">{{ $session->date->format('j') }}</div>
                                <div class="text-[10px] font-semibold mt-0.5" style="color:var(--muted)">{{ $session->date->format('D') }}</div>
                            @else
                                <div class="text-xl leading-none py-1.5">🧾</div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-1">
                                <h3 class="font-bold" style="color:var(--text)">
                                    {{ $session?->topic ?: ($session ? 'Session fee' : 'Course fee') }}
                                </h3>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="font-extrabold tabular-nums {{ $payment->status === 'waived' ? 'line-through' : '' }}" style="color:var(--text)">{{ $rupiah($payment->amount) }}</span>
                                    <span class="inline-flex items-center text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $bill['class'] }}">{{ $bill['label'] }}</span>
                                </div>
                            </div>

                            <dl class="mt-1.5 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm" style="color:var(--muted)">
                                @if($session)
                                    <div class="flex items-center gap-1.5">🗓️ <span>{{ $session->date->translatedFormat('D, d M Y') }}</span></div>
                                    @if($session->timeLabel())
                                        <div class="flex items-center gap-1.5">🕒 <span>{{ $session->timeLabel() }}</span></div>
                                    @endif
                                @endif
                                @if($payment->status === 'paid' && $payment->paid_at)
                                    <div class="flex items-center gap-1.5 text-green-600 font-semibold">
                                        ✅ <span>Paid {{ $payment->paid_at->translatedFormat('d M Y') }}@if($payment->method) · {{ $methodLabels[$payment->method] ?? $payment->method }}@endif</span>
                                    </div>
                                @endif
                                @if($payment->isAwaitingVerification())
                                    <div class="flex items-center gap-1.5 text-blue-600 font-semibold">
                                        ⏱️ <span>
                                            Confirmed {{ $payment->submitted_at?->translatedFormat('d M Y') }}@if($payment->method) via {{ $methodLabels[$payment->method] ?? $payment->method }}@endif — waiting for the admin
                                        </span>
                                    </div>
                                @endif
                            </dl>

                            @if($payment->rejection_reason)
                                <p class="text-sm mt-1.5 text-red-600">
                                    <span class="font-semibold">Confirmation rejected:</span> {{ $payment->rejection_reason }} — please submit it again.
                                </p>
                            @endif

                            @if($payment->isPayable())
                                <a href="{{ route('courses.bills.pay', $payment) }}"
                                   class="no-print btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold mt-3">
                                    Pay this bill
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            @endif

                            @if($payment->notes)
                                <p class="text-sm mt-1.5" style="color:var(--muted)">{{ $payment->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Registration footer totals --}}
                <div class="px-5 sm:px-6 py-4 border-t flex flex-wrap items-center justify-end gap-x-6 gap-y-1 text-sm" style="border-color:var(--border)">
                    <span style="color:var(--muted)">Billed <span class="font-semibold tabular-nums" style="color:var(--text)">{{ $rupiah($registrationTotals['billed']) }}</span></span>
                    <span style="color:var(--muted)">Paid <span class="font-semibold text-green-600 tabular-nums">{{ $rupiah($registrationTotals['paid']) }}</span></span>
                    @if($registrationTotals['waived'] > 0)
                        <span style="color:var(--muted)">Waived <span class="font-semibold tabular-nums">{{ $rupiah($registrationTotals['waived']) }}</span></span>
                    @endif
                    <span style="color:var(--muted)">Outstanding <span class="font-semibold tabular-nums {{ $registrationTotals['outstanding'] > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $rupiah($registrationTotals['outstanding']) }}</span></span>
                </div>
            </section>
        @endforeach

        <p class="text-xs text-center" style="color:var(--muted)">
            Bills are issued and settled by the course admin — this page reflects what has been recorded. Contact the front desk if an amount looks wrong.
        </p>
    @endif
</div>
@endsection
