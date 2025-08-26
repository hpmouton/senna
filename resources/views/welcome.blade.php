<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Senna - Take Control of Your Finances</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-zinc-50 dark:bg-rich_black text-rich_black dark:text-nyanza">
    <div
        class="absolute top-0 left-0 -z-10 h-1/3 w-full bg-gradient-to-b from-nyanza/30 to-transparent dark:from-polynesian_blue/20">
    </div>

    <div class="relative min-h-screen flex flex-col items-center">

        <header class="w-full max-w-6xl mx-auto p-6 lg:p-8">
            <nav class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    <span class="text-black"> {{ config('app.name', 'Laravel') }}</span>

                </a>

                <div class="flex items-center space-x-2 text-sm font-semibold">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">Log
                                in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-4 py-2 bg-imperial_red text-white rounded-md hover:opacity-90 transition-opacity">
                                    <span>Sign Up</span>
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        <main class="flex-grow w-full max-w-6xl mx-auto flex flex-col items-center justify-center p-6 lg:p-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold tracking-tighter leading-tight">
                        Financial Control, <br> Beautifully <span class="text-imperial_red">Simple</span>.
                    </h1>
                    <p class="mt-6 text-lg text-gray-600 dark:text-gray-300 max-w-md mx-auto lg:mx-0">
                        Senna helps you visualize your spending, master your budgets, and build a healthier financial
                        future with confidence.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('register') }}"
                            class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-lg hover:scale-105 duration-300 ease-in-out transition-transform">
                            Get Started for Free
                        </a>
                    </div>
                </div>

                <div class="hidden lg:block">
                    <div
                        class="relative p-2 border rounded-xl shadow-2xl bg-white/50 dark:bg-rich_black/50 dark:border-gray-700 backdrop-blur-sm">
                        <img src="{{ asset('assets/sennaui.png') }}"
                            alt="Senna Application Screenshot" class="rounded-lg">
                        <div
                            class="absolute -bottom-8 -left-12 p-4 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl border dark:border-gray-700">
                            <p class="font-semibold text-sm">Monthly Budget</p>
                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                <span>Spent</span>
                                <span>$754.30 / $1,200.00</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-2 rounded-full bg-cyan-500" style="width: 62%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <footer class="w-full max-w-6xl mx-auto p-6 lg:p-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Senna. All rights reserved by HPMouton.
        </footer>
    </div>
</body>

</html>
