<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Website Fonts
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the selectable website fonts. Consumed by the
    | public layouts (to load the chosen Google Font and apply the family) and
    | by the Filament ThemeSettings page (dropdown options + live preview).
    |
    | Each entry:
    | - label   : human-readable name shown in the admin dropdown.
    | - family  : full CSS font-family stack applied to the site.
    | - google  : Google Fonts "family=" query used to load the webfont.
    | - group   : dropdown optgroup the font is listed under.
    | - bundled : true when the font is already shipped locally (via @fonts),
    |             so the public pages skip loading it again from Google.
    |
    */

    // ── Sans-Serif Modern ──────────────────────────────────────────────────
    'instrument-sans' => [
        'label' => 'Instrument Sans (Default)',
        'family' => "'Instrument Sans', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Instrument+Sans:wght@400;500;600;700',
        'group' => 'Sans-Serif Modern',
        'bundled' => true,
    ],
    'inter' => [
        'label' => 'Inter — Clean & Modern',
        'family' => "'Inter', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Inter:wght@300;400;500;600;700;800;900',
        'group' => 'Sans-Serif Modern',
    ],
    'plus-jakarta-sans' => [
        'label' => 'Plus Jakarta Sans — Elegant',
        'family' => "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Plus+Jakarta+Sans:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],
    'outfit' => [
        'label' => 'Outfit — Geometric',
        'family' => "'Outfit', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Outfit:wght@300;400;500;600;700;800;900',
        'group' => 'Sans-Serif Modern',
    ],
    'dm-sans' => [
        'label' => 'DM Sans — Rounded & Friendly',
        'family' => "'DM Sans', ui-sans-serif, system-ui, sans-serif",
        'google' => 'DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400',
        'group' => 'Sans-Serif Modern',
    ],
    'nunito' => [
        'label' => 'Nunito — Soft & Rounded',
        'family' => "'Nunito', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Nunito:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],
    'poppins' => [
        'label' => 'Poppins — Bold & Geometric',
        'family' => "'Poppins', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Poppins:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],
    'sora' => [
        'label' => 'Sora — Modern Sans',
        'family' => "'Sora', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Sora:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],
    'manrope' => [
        'label' => 'Manrope — Minimal & Modern',
        'family' => "'Manrope', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Manrope:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],
    'work-sans' => [
        'label' => 'Work Sans — Versatile',
        'family' => "'Work Sans', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Work+Sans:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],
    'figtree' => [
        'label' => 'Figtree — Friendly',
        'family' => "'Figtree', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Figtree:wght@300;400;500;600;700;800;900',
        'group' => 'Sans-Serif Modern',
    ],
    'space-grotesk' => [
        'label' => 'Space Grotesk — Distinctive',
        'family' => "'Space Grotesk', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Space+Grotesk:wght@300;400;500;600;700',
        'group' => 'Sans-Serif Modern',
    ],
    'lexend' => [
        'label' => 'Lexend — High Readability',
        'family' => "'Lexend', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Lexend:wght@300;400;500;600;700;800',
        'group' => 'Sans-Serif Modern',
    ],

    // ── Serif Elegan ───────────────────────────────────────────────────────
    'playfair-display' => [
        'label' => 'Playfair Display — Classy Serif',
        'family' => "'Playfair Display', ui-serif, Georgia, serif",
        'google' => 'Playfair+Display:wght@400;500;600;700;800;900',
        'group' => 'Serif Elegan',
    ],
    'lora' => [
        'label' => 'Lora — Readable Serif',
        'family' => "'Lora', ui-serif, Georgia, serif",
        'google' => 'Lora:wght@400;500;600;700',
        'group' => 'Serif Elegan',
    ],
    'merriweather' => [
        'label' => 'Merriweather — Classic Serif',
        'family' => "'Merriweather', ui-serif, Georgia, serif",
        'google' => 'Merriweather:wght@300;400;700;900',
        'group' => 'Serif Elegan',
    ],

    // ── Arab + Latin (Islami) ──────────────────────────────────────────────
    'cairo' => [
        'label' => 'Cairo — Arab + Latin',
        'family' => "'Cairo', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Cairo:wght@300;400;500;600;700;800;900',
        'group' => 'Arab + Latin (Islami)',
    ],
    'tajawal' => [
        'label' => 'Tajawal — Arab + Latin',
        'family' => "'Tajawal', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Tajawal:wght@300;400;500;700;800',
        'group' => 'Arab + Latin (Islami)',
    ],
    'ibm-plex-sans-arabic' => [
        'label' => 'IBM Plex Sans Arabic — Arab + Latin',
        'family' => "'IBM Plex Sans Arabic', ui-sans-serif, system-ui, sans-serif",
        'google' => 'IBM+Plex+Sans+Arabic:wght@300;400;500;600;700',
        'group' => 'Arab + Latin (Islami)',
    ],
    'rubik' => [
        'label' => 'Rubik — Arab + Latin',
        'family' => "'Rubik', ui-sans-serif, system-ui, sans-serif",
        'google' => 'Rubik:wght@300;400;500;600;700;800;900',
        'group' => 'Arab + Latin (Islami)',
    ],
    'amiri' => [
        'label' => 'Amiri — Naskh Klasik (Arab)',
        'family' => "'Amiri', ui-serif, Georgia, serif",
        'google' => 'Amiri:wght@400;700',
        'group' => 'Arab + Latin (Islami)',
    ],

];
