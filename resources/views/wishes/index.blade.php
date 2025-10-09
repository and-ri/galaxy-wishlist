@extends('layouts.app')

@section('title', __('wishlist.my_wishes'))

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-8 px-2 md:px-0">
    <h1 class="text-2xl md:text-4xl font-bold text-gray-800">✨ {{ __('wishlist.my_wishes') }}</h1>
    <a href="{{ route('wishes.create') }}" class="w-full sm:w-auto px-4 md:px-6 py-2 md:py-3 text-sm md:text-base bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition transform hover:scale-105 shadow-lg text-center">
        ➕ {{ __('wishlist.add_wish') }}
    </a>
</div>

@if($wishes->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        @foreach($wishes as $wish)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300 {{ $wish->is_purchased ? 'opacity-60' : '' }}">
                @if($wish->image)
                    <div class="h-40 md:h-48 overflow-hidden">
                        <img src="{{ asset('storage/' . $wish->image) }}" alt="{{ $wish->title }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    </div>
                @else
                    <div class="h-40 md:h-48 bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center">
                        <span class="text-5xl md:text-6xl">🎁</span>
                    </div>
                @endif

                <div class="p-4 md:p-6">
                    <div class="flex items-start justify-between mb-3 gap-2">
                        <h3 class="text-lg md:text-xl font-bold text-gray-800 flex-1 {{ $wish->is_purchased ? 'line-through' : '' }}">{{ $wish->title }}</h3>
                        @if($wish->priority !== null)
                            <span class="ml-2 px-2 md:px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap shrink-0
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
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $wish->description }}</p>
                    @endif

                    @if($wish->price)
                        <div class="text-xl md:text-2xl font-bold text-purple-600 mb-3">
                            {{ number_format($wish->price, 2) }} {{ $wish->currency }}
                        </div>
                    @endif

                    @if($wish->url)
                        <a href="{{ $wish->url }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-800 mb-4 text-sm font-medium break-all">
                            🔗 <span class="hidden sm:inline">Посилання на товар</span><span class="sm:hidden">Посилання</span>
                            <svg class="w-4 h-4 ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    @endif

                    @if($wish->is_purchased)
                        <div class="flex items-center space-x-2 text-green-600 font-semibold mb-4 bg-green-50 px-3 py-2 rounded-lg text-sm">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('wishlist.is_purchased') }}</span>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t border-gray-200">
                        <a href="{{ route('wishes.edit', $wish) }}" class="flex-1 px-3 md:px-4 py-2 text-sm md:text-base bg-purple-100 text-purple-700 font-semibold rounded-lg hover:bg-purple-200 transition text-center">
                            ✏️ Редагувати
                        </a>
                        <form action="{{ route('wishes.destroy', $wish) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('wishlist.delete_confirmation') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 md:px-4 py-2 text-sm md:text-base bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition">
                                🗑️ Видалити
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 text-center">
        <div class="text-5xl md:text-6xl mb-4">🎁</div>
        <p class="text-xl md:text-2xl text-gray-600 mb-6">{{ __('wishlist.no_wishes') }}</p>
        <a href="{{ route('wishes.create') }}" class="inline-block px-6 md:px-8 py-3 md:py-4 text-sm md:text-base bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition transform hover:scale-105 shadow-lg">
            ➕ {{ __('wishlist.add_wish') }}
        </a>
    </div>
@endif
@endsection
