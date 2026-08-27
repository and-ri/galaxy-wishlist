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
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Google Login Button -->
            <div class="text-center mb-6">
                <a href="{{ route('auth.google') }}" class="inline-flex items-center justify-center gap-3 w-full px-8 py-4 bg-white border border-gray-300 text-gray-700 text-lg font-semibold rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-purple-200 transition transform hover:scale-105 shadow-lg">
                    <svg class="w-6 h-6" viewBox="0 0 48 48" aria-hidden="true">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    {{ __('auth.login_with_google') }}
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
                Використовуйте ваш обліковий запис Google для входу
            </p>
        </div>
    </div>
</div>
@endsection
