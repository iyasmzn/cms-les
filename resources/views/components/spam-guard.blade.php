{{-- Honeypot: off-screen field that only automated bots fill in. --}}
<div aria-hidden="true" style="position:absolute!important;left:-10000px!important;width:1px;height:1px;overflow:hidden">
    <label for="website_hp">Website</label>
    <input type="text" id="website_hp" name="website" tabindex="-1" autocomplete="off">
</div>

@if(\App\Support\Turnstile::enabled())
    <div class="cf-turnstile" data-sitekey="{{ \App\Support\Turnstile::siteKey() }}"></div>
    @once
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce
@endif
