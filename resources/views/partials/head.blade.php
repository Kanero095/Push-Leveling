<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@fluxAppearance

@php
    $bgImage = \App\Models\AppSetting::get('app_bg_image');
@endphp
@if($bgImage)
    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -10;
            background-image: url('{{ asset('storage/' . $bgImage) }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.15;
            pointer-events: none;
            filter: blur(1px);
        }
        html.dark body::before {
            opacity: 0.12;
            filter: blur(1px) brightness(0.8);
        }
    </style>
@endif
