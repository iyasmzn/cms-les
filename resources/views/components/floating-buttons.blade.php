@php
    $buttons = \App\Models\FloatingButton::active()->get();
@endphp

@if($buttons->isNotEmpty())
<div id="fab-container" style="position:fixed;bottom:1.75rem;right:1.75rem;z-index:9000;display:flex;flex-direction:column-reverse;align-items:flex-end;gap:.65rem;">

    {{-- Single button: render langsung --}}
    @if($buttons->count() === 1)
    @php $btn = $buttons->first() @endphp
    <a href="{{ $btn->url }}"
       @if($btn->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
       title="{{ $btn->label }}"
       style="
           display:inline-flex;align-items:center;gap:.5rem;
           padding:.75rem 1.25rem;
           border-radius:9999px;
           background:{{ $btn->color }};
           color:#fff;
           font-size:.875rem;font-weight:700;
           box-shadow:0 4px 20px rgba(0,0,0,.25);
           text-decoration:none;
           transition:transform .2s,box-shadow .2s;
           white-space:nowrap;
       "
       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 28px rgba(0,0,0,.3)'"
       onmouseout="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(0,0,0,.25)'">
        @if($url = icon_url($btn->icon_image))
            <img src="{{ $url }}" alt="{{ $btn->label }}" style="width:1.25rem;height:1.25rem;object-fit:contain">
        @else
            <span style="font-size:1.125rem;line-height:1">{{ $btn->icon }}</span>
        @endif
        <span>{{ $btn->label }}</span>
    </a>

    {{-- Multiple buttons: expandable FAB --}}
    @else
    {{-- Child buttons (hidden by default) --}}
    <div id="fab-items"
         style="
             display:flex;flex-direction:column-reverse;align-items:flex-end;gap:.5rem;
             transition:opacity .2s,transform .2s;
             opacity:0;pointer-events:none;
             transform:translateY(.5rem) scale(.95);
             transform-origin:bottom right;
         ">
        @foreach($buttons as $btn)
        <div style="display:flex;align-items:center;gap:.65rem;justify-content:flex-end;">
            {{-- Label tooltip --}}
            <span style="
                background:rgba(0,0,0,.75);color:#fff;
                font-size:.75rem;font-weight:600;
                padding:.3rem .75rem;border-radius:.375rem;
                white-space:nowrap;
                pointer-events:none;
                backdrop-filter:blur(4px);
            ">{{ $btn->label }}</span>

            <a href="{{ $btn->url }}"
               @if($btn->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
               title="{{ $btn->label }}"
               style="
                   display:inline-flex;align-items:center;justify-content:center;
                   width:3rem;height:3rem;border-radius:9999px;
                   background:{{ $btn->color }};color:#fff;
                   font-size:1.25rem;text-decoration:none;flex-shrink:0;
                   box-shadow:0 3px 12px rgba(0,0,0,.25);
                   transition:transform .2s,box-shadow .2s;
               "
               onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.3)'"
               onmouseout="this.style.transform='';this.style.boxShadow='0 3px 12px rgba(0,0,0,.25)'">
                @if($url = icon_url($btn->icon_image))
                    <img src="{{ $url }}" alt="{{ $btn->label }}" style="width:1.5rem;height:1.5rem;object-fit:contain">
                @else
                    {{ $btn->icon }}
                @endif
            </a>
        </div>
        @endforeach
    </div>

    {{-- Main trigger button --}}
    <button id="fab-trigger"
            onclick="toggleFab()"
            aria-label="Buka menu"
            aria-expanded="false"
            style="
                width:3.5rem;height:3.5rem;border-radius:9999px;
                background:var(--primary, #08484A);color:#fff;
                border:none;cursor:pointer;
                display:flex;align-items:center;justify-content:center;
                box-shadow:0 4px 20px rgba(0,0,0,.3);
                transition:transform .25s,box-shadow .2s;
                position:relative;z-index:1;
            "
            onmouseover="this.style.boxShadow='0 8px 28px rgba(0,0,0,.35)'"
            onmouseout="this.style.boxShadow='0 4px 20px rgba(0,0,0,.3)'">
        <svg id="fab-icon-plus" style="width:1.5rem;height:1.5rem;transition:transform .25s,opacity .2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <svg id="fab-icon-close" style="width:1.5rem;height:1.5rem;position:absolute;transition:transform .25s,opacity .2s;opacity:0;transform:rotate(-90deg)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    @endif
</div>

@if($buttons->count() > 1)
<script>
(function () {
    var open = false;
    var items = document.getElementById('fab-items');
    var trigger = document.getElementById('fab-trigger');
    var iconPlus = document.getElementById('fab-icon-plus');
    var iconClose = document.getElementById('fab-icon-close');

    window.toggleFab = function () {
        open = !open;
        trigger.setAttribute('aria-expanded', open);

        if (open) {
            items.style.opacity = '1';
            items.style.pointerEvents = 'auto';
            items.style.transform = 'translateY(0) scale(1)';
            trigger.style.transform = 'rotate(0deg)';
            iconPlus.style.opacity = '0';
            iconPlus.style.transform = 'rotate(90deg)';
            iconClose.style.opacity = '1';
            iconClose.style.transform = 'rotate(0deg)';
        } else {
            items.style.opacity = '0';
            items.style.pointerEvents = 'none';
            items.style.transform = 'translateY(.5rem) scale(.95)';
            trigger.style.transform = '';
            iconPlus.style.opacity = '1';
            iconPlus.style.transform = '';
            iconClose.style.opacity = '0';
            iconClose.style.transform = 'rotate(-90deg)';
        }
    };

    // Close when clicking outside
    document.addEventListener('click', function (e) {
        if (open && !document.getElementById('fab-container').contains(e.target)) {
            toggleFab();
        }
    });
})();
</script>
@endif
@endif
