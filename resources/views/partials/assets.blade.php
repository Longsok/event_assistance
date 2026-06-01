@php
    $hasHot      = file_exists(public_path('hot'));
    $manifestOk  = file_exists(public_path('build/manifest.json'))
                || file_exists(public_path('build/.vite/manifest.json'));
@endphp

@if($hasHot || $manifestOk)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <script src="https://cdn.tailwindcss.com"></script>
@endif