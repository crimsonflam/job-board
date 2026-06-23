<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF token: read by axios (and available as a fallback header) so the
         React SPA can perform POST/PUT/DELETE writes against the web-group API. --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Base path the app is served from (e.g. "/job_board/public" on XAMPP, or
         "" when served at the domain root). Derived from APP_URL so React Router
         (basename) and axios (baseURL) resolve URLs correctly under a subfolder. --}}
    <meta name="base-path" content="{{ rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/') }}">

    <title>JobBoard</title>

    {{-- Poppins web font (300–800). The crimson theme + Poppins typography are
         compiled locally via Vite/Tailwind v4 now (see resources/css/app.css),
         but the font itself still loads from Google Fonts. Using preconnect +
         a parallel stylesheet <link> keeps it from blocking the CSS parser. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/main.jsx'])
</head>
{{-- Neutral background = cream (#FAFAFA), body text = charcoal (#2C2C2C),
     matching the original layout exactly. --}}
<body class="min-h-screen bg-[#FAFAFA] text-[#2C2C2C] antialiased">
    <div id="root"></div>
</body>
</html>
