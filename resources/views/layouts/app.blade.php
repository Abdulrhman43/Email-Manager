<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmailManager</title>

    {{-- CSRF token for AJAX requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-white text-slate-800 h-screen overflow-hidden">

<div class="flex h-screen">

    {{-- Sidebar --}}
    @include('partials.header')

    {{-- Main content --}}
    <main class="flex-1 flex flex-col overflow-hidden bg-white">
        @yield('content')
    </main>

</div>

{{-- Pass PHP data to JavaScript --}}
@yield('scripts')

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/API_Ops.js') }}"></script>

</body>
</html>