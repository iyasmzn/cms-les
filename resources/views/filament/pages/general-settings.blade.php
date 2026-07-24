<x-filament-panels::page>
    {{-- Only the ThemeSettings page loads the font previews; this view is shared by several settings pages --}}
    @if (method_exists($this, 'googleFontsHref'))
        {{-- Load selectable fonts so the dropdown options and live preview render in their real typeface --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="{{ $this->googleFontsHref() }}">
    @endif

    {{ $this->form }}
</x-filament-panels::page>
