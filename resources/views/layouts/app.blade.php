<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'E-Aduan') — E-Aduan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    <nav class="bg-white shadow mb-8">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center gap-6">
            <a href="/" class="font-bold text-lg text-blue-600">E-Aduan</a>
            <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 hover:text-blue-600">Kategori</a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 pb-12">
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
    <footer class="footer footer-center p-5 bg-base-300 text-base-content text-xs">
        <div>
            <p>© {{ date('Y') }} E-Aduan - Built with Laravel and Claude ❤️</p>
        </div>
    </footer>
</body>
</html>
