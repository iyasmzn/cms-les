<x-filament-widgets::widget>
    <div
        style="
            position:relative;
            overflow:hidden;
            border-radius:.75rem;
            padding:1.5rem;
            color:#fff;
            background:linear-gradient(120deg,#08484A 0%,#0a5f61 55%,#12857f 100%);
            box-shadow:0 10px 25px -12px rgba(8,72,74,.55);
        "
    >
        {{-- Decorative glow --}}
        <div style="position:absolute;top:-40%;right:-10%;width:20rem;height:20rem;border-radius:9999px;background:rgba(255,255,255,.08);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-60%;right:20%;width:14rem;height:14rem;border-radius:9999px;background:rgba(255,255,255,.05);pointer-events:none;"></div>

        <div style="position:relative;display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
            @if ($avatarUrl)
                <img
                    src="{{ $avatarUrl }}"
                    alt="{{ $name }}"
                    style="width:4.5rem;height:4.5rem;border-radius:9999px;object-fit:cover;flex-shrink:0;box-shadow:0 0 0 3px rgba(255,255,255,.35);"
                />
            @endif

            <div style="flex:1;min-width:14rem;">
                <p style="display:flex;align-items:center;gap:.375rem;font-size:.875rem;font-weight:500;margin:0;opacity:.9;">
                    @svg($greetingIcon, ['style' => 'width:1rem;height:1rem;'])
                    <span>{{ $greeting }},</span>
                </p>

                <h2 style="font-size:1.6rem;font-weight:700;line-height:1.2;margin:.25rem 0 .625rem;">
                    {{ $name }}
                </h2>

                <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;">
                    @foreach ($roles as $role)
                        <span style="display:inline-block;font-size:.7rem;font-weight:600;letter-spacing:.02em;padding:.15rem .55rem;border-radius:9999px;background:rgba(255,255,255,.18);text-transform:capitalize;">
                            {{ str_replace('_', ' ', $role) }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Date & current time (WIB) --}}
            <div style="text-align:right;min-width:9rem;">
                <div style="font-size:.8125rem;opacity:.85;">{{ $date }}</div>
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:.375rem;margin-top:.25rem;">
                    @svg('heroicon-o-clock', ['style' => 'width:1rem;height:1rem;'])
                    <span style="font-size:1.125rem;font-weight:700;font-variant-numeric:tabular-nums;">{{ $time }}</span>
                </div>
                <div style="font-size:.7rem;opacity:.7;margin-top:.375rem;">{{ $siteName }}</div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
