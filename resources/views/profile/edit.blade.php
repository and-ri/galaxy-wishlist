@extends('layouts.app')

@section('title', __('wishlist.edit_profile'))

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-8 py-6">
            <h2 class="text-3xl font-bold text-white">{{ __('wishlist.edit_profile') }}</h2>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Avatar Section -->
            <div class="flex flex-col items-center space-y-4">
                <div class="relative">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover ring-4 ring-purple-200">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center ring-4 ring-purple-200">
                            <span class="text-5xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="w-full max-w-md">
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">{{ __('wishlist.upload_avatar') }}</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition">
                    <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF до 2MB</p>
                </div>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.name') }} <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('auth.email') }} <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
            </div>

            <!-- Birthday -->
            <div>
                <label for="birthday" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center space-x-2">
                        <span>🎂</span>
                        <span>День народження</span>
                    </span>
                </label>
                <input type="date" id="birthday" name="birthday" value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
            </div>

            <!-- Delivery Address -->
            <div>
                <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center space-x-2">
                        <span>📦</span>
                        <span>Адреса для доставки подарунків</span>
                    </span>
                </label>
                <textarea id="delivery_address" name="delivery_address" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition resize-none" placeholder="Введіть вашу адресу (вулиця, будинок, квартира, місто, поштовий індекс)">{{ old('delivery_address', $user->delivery_address) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Ця інформація допоможе друзям надіслати вам подарунок</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition transform hover:scale-105">
                    {{ __('wishlist.save') }}
                </button>
                <a href="{{ route('wishes.index') }}" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition text-center">
                    {{ __('wishlist.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
