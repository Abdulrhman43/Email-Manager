<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmailManager</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-white text-slate-800 h-screen overflow-hidden">

<div class="flex h-screen">
    @include('partials.header')
    <main class="flex-1 flex flex-col overflow-hidden bg-white">
        @yield('content')
    </main>
</div>

@yield('scripts')

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/API_Ops.js') }}"></script>

{{-- Laravel Echo + Reverb real-time connection ──────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
<script>
    // Boot Echo using Reverb (Pusher-compatible protocol)
    window.Echo = new (class {
        constructor() {
            this._pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                wsHost     : '{{ env('REVERB_HOST', '127.0.0.1') }}',
                wsPort     : {{ env('REVERB_PORT', 8080) }},
                wssPort    : {{ env('REVERB_PORT', 8080) }},
                forceTLS   : false,
                enabledTransports: ['ws'],
                cluster    : 'mt1',
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }
            });
        }

        private(channel) {
            return this._pusher.subscribe('private-' + channel);
        }
    })();

    // Listen on the current user's private channel
    const userId = {{ Auth::id() }};
    const userChannel = window.Echo.private('user.' + userId);

    userChannel.bind('new.email', function(data) {
        // New message arrived in real time — reload the inbox table
        reloadEmails();

        // If the conversation modal is open for this thread, update it too
        if (typeof activeThreadId !== 'undefined' && activeThreadId == data.threadId) {
            const current = messageMap.get(String(data.messageId));
            if (current) {
                renderThreadMessages(current);
            }
        }

        // Flash the inbox badge briefly to notify the user
        const badge = document.getElementById('inboxBadge');
        if (badge) {
            badge.classList.add('bg-red-500');
            setTimeout(() => badge.classList.remove('bg-red-500'), 2000);
        }
    });
</script>

</body>
</html>