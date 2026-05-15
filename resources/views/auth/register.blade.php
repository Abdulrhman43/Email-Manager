<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - EmailManager</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">

    <div class="px-8 pt-10 pb-8 h-full flex flex-col">

        <div class="mb-8 text-center flex-1">

            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-900 text-white mb-4 shadow-lg">

                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4v16m8-8H4"/>

                </svg>

            </div>

            <h1 class="text-2xl font-semibold text-slate-900 mb-2">
                Create Account
            </h1>

            <p class="text-sm text-slate-500">
                Sign up to start managing your emails.
            </p>

        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-600 border border-red-200 text-sm rounded-xl px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">

            @csrf

            <div>

                <label for="name"
                       class="block text-sm font-medium text-slate-700 mb-1.5">

                    Full Name

                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    required
                    autocomplete="name"

                    value="{{ old('name') }}"

                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100 transition-all placeholder-slate-400"

                    placeholder="John Doe"
                >

            </div>

            <div>

                <label for="email"
                       class="block text-sm font-medium text-slate-700 mb-1.5">

                    Email address

                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                    autocomplete="email"

                    value="{{ old('email') }}"

                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100 transition-all placeholder-slate-400"

                    placeholder="hello@example.com"
                >

            </div>

            <div>

                <label for="password"
                       class="block text-sm font-medium text-slate-700 mb-1.5">

                    Password

                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="new-password"

                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100 transition-all placeholder-slate-400"

                    placeholder="Create a strong password"
                >

            </div>

            <div>

                <label for="password_confirmation"
                       class="block text-sm font-medium text-slate-700 mb-1.5">

                    Confirm Password

                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required

                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100 transition-all placeholder-slate-400"

                    placeholder="Confirm password"
                >

            </div>

            <div class="pt-2">

                <button
                    type="submit"

                    class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-md hover:bg-slate-800 transition-colors mt-2"
                >

                    Sign up

                </button>

            </div>

        </form>

        <p class="mt-8 text-center text-sm text-slate-500">

            Already have an account?

            <a href="{{ route('login') }}"
               class="font-medium text-slate-900 hover:underline focus:outline-none">

                Log in

            </a>

        </p>

    </div>

</div>

</body>
</html>