<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-slate-900 min-h-screen antialiased font-sans">
    <header class="w-full border-b border-gray-200 bg-white shadow-sm">
        <div class="mx-auto max-w-6xl px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-xl font-bold tracking-tight text-blue-600">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white shadow-md">
                    {{ strtoupper(substr(config('app.name', 'L'), 0, 1)) }}
                </span>
                <span class="text-slate-800">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <nav class="flex items-center gap-3">
                <x-button type="primary" href="{{ url('/tasks/create') }}" class="shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Task
                </x-button>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
