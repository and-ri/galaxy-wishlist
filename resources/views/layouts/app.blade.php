<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-pink-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-900 via-purple-800 to-indigo-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="text-3xl">🌌</span>
                    <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
                </a>
                
                <ul class="flex items-center space-x-6">
                    @auth
                        <li><a href="{{ route('wishes.index') }}" class="hover:text-purple-200 transition duration-200">{{ __('wishlist.my_wishes') }}</a></li>
                        <li><a href="{{ route('users.index') }}" class="hover:text-purple-200 transition duration-200">{{ __('wishlist.all_users') }}</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-purple-200 transition duration-200 flex items-center space-x-2">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ __('wishlist.profile') }}</span>
                        </a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-purple-700 hover:bg-purple-600 rounded-lg transition duration-200">{{ __('auth.logout') }}</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="px-4 py-2 bg-purple-700 hover:bg-purple-600 rounded-lg transition duration-200">{{ __('auth.login') }}</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto px-4 py-8 min-h-[calc(100vh-160px)]">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-md flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-md flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-md">
                <div class="font-bold mb-2">Помилки валідації:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-16 bg-purple-900 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-purple-200">© {{ date('Y') }} {{ config('app.name') }}. Всі права захищені.</p>
        </div>
    </footer>
</body>
</html>
