@once
<style>
    [class*="-hero"]::before,
    [class*="-hero"]::after {
        z-index: 2;
        pointer-events: none;
    }
</style>
@endonce

{{-- Islamic Geometric Background --}}
<div class="absolute inset-0 pointer-events-none overflow-hidden" style="z-index:1" aria-hidden="true">
    {{-- Repeating 8-pointed star tile --}}
    <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="hero-geo-pattern" x="5" y="5" width="60" height="60" patternUnits="userSpaceOnUse">
                {{-- Two overlapping squares = Islamic 8-pointed star --}}
                <polygon points="30,12 48,30 30,48 12,30" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.75"/>
                <polygon points="42.73,17.27 42.73,42.73 17.27,42.73 17.27,17.27" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.75"/>
                {{-- Short connector lines linking adjacent star tiles --}}
                <line x1="0" y1="30" x2="12" y2="30" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>
                <line x1="48" y1="30" x2="60" y2="30" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>
                <line x1="30" y1="0" x2="30" y2="12" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>
                <line x1="30" y1="48" x2="30" y2="60" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#hero-geo-pattern)"/>
    </svg>

    {{-- Large decorative 8-star ring – right side --}}
    <svg class="absolute top-1/2 -translate-y-1/2" style="right:-80px;opacity:0.1"
         width="320" height="320" viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg">
        <circle cx="160" cy="160" r="150" fill="none" stroke="white" stroke-width="1.2"/>
        <circle cx="160" cy="160" r="118" fill="none" stroke="white" stroke-width="0.8"/>
        <polygon points="160,50 181.05,109.19 237.78,82.22 210.81,138.95 270,160 210.81,181.05 237.78,237.78 181.05,210.81 160,270 138.95,210.81 82.22,237.78 109.19,181.05 50,160 109.19,138.95 82.22,82.22 138.95,109.19"
                 fill="none" stroke="white" stroke-width="1.5"/>
    </svg>

    {{-- Large decorative 8-star ring – left side --}}
    <svg class="absolute top-1/2 -translate-y-1/2" style="left:-70px;opacity:0.07"
         width="260" height="260" viewBox="0 0 260 260" xmlns="http://www.w3.org/2000/svg">
        <circle cx="130" cy="130" r="120" fill="none" stroke="white" stroke-width="1.2"/>
        <circle cx="130" cy="130" r="90" fill="none" stroke="white" stroke-width="0.8"/>
        <polygon points="130,30 149.14,83.81 200.71,59.29 176.19,110.86 230,130 176.19,149.14 200.71,200.71 149.14,176.19 130,230 110.86,176.19 59.29,200.71 83.81,149.14 30,130 83.81,110.86 59.29,59.29 110.86,83.81"
                 fill="none" stroke="white" stroke-width="1.5"/>
    </svg>
</div>
