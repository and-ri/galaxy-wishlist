@extends('layouts.app')

@section('title', __('auth.admin_login'))

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-8 py-6">
            <h2 class="text-3xl font-bold text-white">🔐 {{ __('auth.admin_login') }}</h2>
            <p class="text-purple-100 mt-2">Тільки для локальної розробки</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.login.post') }}" class="p-8 space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Пароль</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="remember" class="text-sm text-gray-700 cursor-pointer">{{ __('auth.remember') }}</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition transform hover:scale-105">
                {{ __('auth.login') }}
            </button>
        </form>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                Дані для входу: <span class="font-mono text-purple-600">admin@example.com</span> / <span class="font-mono text-purple-600">password</span>
            </p>
        </div>
    </div>
</div>
@endsection
