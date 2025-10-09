@extends('layouts.app')

@section('title', __('auth.login'))

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-8 py-6">
            <h2 class="text-3xl font-bold text-white text-center">🌌 {{ __('auth.login') }}</h2>
        </div>

        <!-- Content -->
        <div class="p-8">
            <!-- Main Login Button -->
            <div class="text-center mb-6">
                <a href="{{ route('auth.authentik') }}" class="inline-block w-full px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-lg font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-purple-300 transition transform hover:scale-105 shadow-lg">
                    🔐 {{ __('auth.login_with_authentik') }}
                </a>
            </div>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">або</span>
                </div>
            </div>

            <!-- Admin Login Link -->
            <div class="text-center">
                <a href="{{ route('admin.login') }}" class="text-purple-600 hover:text-purple-800 font-medium transition">
                    {{ __('auth.admin_login') }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-500">
                Використовуйте ваш обліковий запис Authentik для входу
            </p>
        </div>
    </div>
</div>
@endsection
