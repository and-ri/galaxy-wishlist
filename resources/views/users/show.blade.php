@extends('layouts.app')

@section('title', $user->name)

@section('content')
<!-- User Profile Header -->
<div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-8">
        <div class="flex items-center gap-6">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover ring-4 ring-white shadow-lg">
            @else
                <div class="w-24 h-24 rounded-full bg-white bg-opacity-30 backdrop-blur-sm flex items-center justify-center ring-4 ring-white shadow-lg">
                    <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
            @endif
            
            <div class="text-white">
                <h1 class="text-4xl font-bold">{{ $user->name }}</h1>
                <p class="text-purple-100 mt-1">{{ $user->email }}</p>
                @if($user->birthday)
                    <p class="text-purple-100 mt-2">🎂 День народження: {{ $user->birthday->format('d.m.Y') }}</p>
                @endif
                @if($user->delivery_address)
                    <p class="text-purple-100 mt-2">📦 {{ Str::limit($user->delivery_address, 60) }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<h2 class="text-3xl font-bold text-gray-800 mb-6">✨ {{ __('wishlist.wishes_of', ['name' => $user->name]) }}</h2>

@if($wishes->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($wishes as $wish)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300 {{ $wish->is_purchased ? 'opacity-60' : '' }}">
                @if($wish->image)
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('storage/' . $wish->image) }}" alt="{{ $wish->title }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    </div>
                @else
                    <div class="h-48 bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center">
                        <span class="text-6xl">🎁</span>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-xl font-bold text-gray-800 flex-1 {{ $wish->is_purchased ? 'line-through' : '' }}">{{ $wish->title }}</h3>
                        @if($wish->priority !== null)
                            <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                                @if($wish->priority == 0) bg-blue-100 text-blue-700
                                @elseif($wish->priority == 1) bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif">
                                @if($wish->priority == 0) {{ __('wishlist.priority_low') }}
                                @elseif($wish->priority == 1) {{ __('wishlist.priority_medium') }}
                                @else {{ __('wishlist.priority_high') }}
                                @endif
                            </span>
                        @endif
                    </div>

                    @if($wish->description)
                        <p class="text-gray-600 text-sm mb-4">{{ $wish->description }}</p>
                    @endif

                    @if($wish->price)
                        <div class="text-2xl font-bold text-purple-600 mb-3">
                            {{ number_format($wish->price, 2) }} {{ $wish->currency }}
                        </div>
                    @endif

                    @if($wish->url)
                        <a href="{{ $wish->url }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-800 mb-4 text-sm font-medium">
                            🔗 Посилання на товар
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    @endif

                    @if($wish->is_purchased)
                        <div class="flex items-center space-x-2 text-green-600 font-semibold bg-green-50 px-3 py-2 rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('wishlist.is_purchased') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
        <div class="text-6xl mb-4">🎁</div>
        <p class="text-2xl text-gray-600">{{ __('wishlist.no_wishes') }}</p>
    </div>
@endif

<div class="mt-8">
    <a href="{{ route('users.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
        ← Повернутися до списку користувачів
    </a>
</div>
@endsection
