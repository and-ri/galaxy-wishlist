<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#f5f5f7">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#000000">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title') @yield('title') · @endif {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh">
    @php
        $nav = [
            ['route' => 'home', 'match' => 'home', 'label' => 'Огляд', 'icon' => 'home'],
            ['route' => 'wishes.index', 'match' => 'wishes.*', 'label' => __('wishlist.my_wishes'), 'tab' => 'Бажання', 'icon' => 'gift'],
            ['route' => 'users.index', 'match' => 'users.*', 'label' => __('wishlist.all_users'), 'tab' => 'Люди', 'icon' => 'users'],
        ];
    @endphp

    <header class="topbar glass sticky top-0 z-40">
        <div class="mx-auto flex h-13 max-w-5xl items-center gap-6 px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="{{ config('app.name') }}, на головну">
                <span class="flex size-7 items-center justify-center rounded-[8px] bg-accent text-white">
                    <x-icon name="sparkles" :size="17" :stroke="1.9" />
                </span>
                <span class="text-headline">{{ config('app.name') }}</span>
            </a>

            @auth
                <nav class="hidden items-center gap-1 md:flex" aria-label="Основна навігація">
                    @foreach ($nav as $item)
                        <a href="{{ route($item['route']) }}" class="nav-link" @if (request()->routeIs($item['match'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="ml-auto hidden items-center gap-2 md:flex">
                    <a href="{{ route('profile.edit') }}" class="pressable flex items-center gap-2 rounded-full py-1 pr-3 pl-1 hover:bg-fill" @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                        <x-avatar :user="auth()->user()" :size="28" />
                        <span class="text-sm font-medium">{{ Str::before(auth()->user()->name, ' ') }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-icon" aria-label="{{ __('auth.logout') }}" title="{{ __('auth.logout') }}">
                            <x-icon name="logout" :size="16" :stroke="2" />
                        </button>
                    </form>
                </div>
            @else
                @unless (request()->routeIs('login'))
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm ml-auto">{{ __('auth.login') }}</a>
                @endunless
            @endauth
        </div>
    </header>

    {{-- Тости: успіх, помилка, невалідна форма --}}
    <div class="pointer-events-none fixed inset-x-0 z-50 flex flex-col items-center gap-2 px-4" style="top: calc(env(safe-area-inset-top) + 64px)" aria-live="polite">
        @foreach (array_filter([
            session('success') ? ['success', session('success')] : null,
            session('error') ? ['error', session('error')] : null,
            $errors->any() && ! session('error') && ! request()->routeIs('login') ? ['error', 'Не вдалося зберегти. Перевірте виділені поля.'] : null,
        ]) as [$type, $message])
            <div data-toast class="toast card pointer-events-auto flex max-w-md items-center gap-2.5 rounded-full py-2.5 pr-5 pl-3.5 text-[0.9375rem] font-medium" role="status">
                <x-icon :name="$type === 'success' ? 'check-circle' : 'exclamation-circle'" :size="22" :stroke="2" class="{{ $type === 'success' ? 'text-success' : 'text-danger' }}" />
                <span>{{ $message }}</span>
            </div>
        @endforeach
    </div>

    <main class="mx-auto max-w-5xl px-4 pt-6 pb-28 sm:px-6 md:pt-10 md:pb-16">
        @yield('content')
    </main>

    <footer class="hidden pb-10 text-center text-footnote text-tertiary md:block">
        © {{ date('Y') }} {{ config('app.name') }}
    </footer>

    @auth
        {{-- Таббар на телефоні: великі цілі під палець, завжди на місці --}}
        <nav class="tabbar glass fixed inset-x-0 bottom-0 z-40 flex md:hidden" aria-label="Основна навігація">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}" class="tab" @if (request()->routeIs($item['match'])) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" :size="26" :stroke="request()->routeIs($item['match']) ? 2 : 1.6" />
                    {{ $item['tab'] ?? $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('profile.edit') }}" class="tab" @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                <x-icon name="user" :size="26" :stroke="request()->routeIs('profile.*') ? 2 : 1.6" />
                {{ __('wishlist.profile') }}
            </a>
        </nav>
    @endauth
</body>
</html>
