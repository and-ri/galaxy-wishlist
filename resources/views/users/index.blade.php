@extends('layouts.app')

@section('title', __('wishlist.all_users'))

@section('content')
<h1 class="text-2xl md:text-4xl font-bold text-gray-800 mb-6 md:mb-8 px-2 md:px-0">👥 {{ __('wishlist.all_users') }}</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
    @foreach($users as $user)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-4 md:p-6 text-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-full mx-auto object-cover ring-4 ring-white shadow-lg">
                @else
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-white bg-opacity-30 backdrop-blur-sm mx-auto flex items-center justify-center ring-4 ring-white shadow-lg">
                        <span class="text-3xl md:text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            
            <div class="p-4 md:p-6 text-center">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">{{ $user->name }}</h3>
                <p class="text-sm md:text-base text-gray-600 mb-2">✨ {{ $user->wishes->count() }} бажань</p>
                
                @if($user->birthday)
                    <p class="text-xs md:text-sm text-gray-500 mb-4">
                        🎂 {{ $user->birthday->format('d.m.Y') }}
                    </p>
                @endif
                
                <a href="{{ route('users.show', $user) }}" class="inline-block px-4 md:px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm md:text-base font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition transform hover:scale-105">
                    Переглянути бажання
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
