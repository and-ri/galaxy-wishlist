@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div style="display: flex; align-items: center; gap: 1rem; margin: 2rem 0;">
    @if($user->avatar)
        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar" style="width: 80px; height: 80px;">
    @else
        <div class="avatar" style="width: 80px; height: 80px; background: #6a1b9a; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
    @endif
    
    <div>
        <h1>{{ $user->name }}</h1>
        <p style="color: #666;">{{ $user->email }}</p>
    </div>
</div>

<h2 style="margin: 2rem 0;">{{ __('wishlist.wishes_of', ['name' => $user->name]) }}</h2>

@if($wishes->count() > 0)
    <div class="grid">
        @foreach($wishes as $wish)
            <div class="wish-card {{ $wish->is_purchased ? 'purchased' : '' }}">
                <h3>{{ $wish->title }}</h3>
                
                @if($wish->priority !== null)
                    <span class="priority-badge priority-{{ $wish->priority }}">
                        @if($wish->priority == 0) {{ __('wishlist.priority_low') }}
                        @elseif($wish->priority == 1) {{ __('wishlist.priority_medium') }}
                        @else {{ __('wishlist.priority_high') }}
                        @endif
                    </span>
                @endif

                @if($wish->description)
                    <p style="margin: 0.5rem 0; color: #666;">{{ $wish->description }}</p>
                @endif

                @if($wish->price)
                    <div class="price">{{ number_format($wish->price, 2) }} {{ $wish->currency }}</div>
                @endif

                @if($wish->url)
                    <a href="{{ $wish->url }}" target="_blank" style="color: #6a1b9a;">🔗 Посилання</a>
                @endif

                @if($wish->is_purchased)
                    <p style="color: #4caf50; font-weight: bold; margin-top: 0.5rem;">✓ {{ __('wishlist.is_purchased') }}</p>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="card" style="text-align: center; padding: 3rem;">
        <p style="font-size: 1.2rem; color: #666;">{{ __('wishlist.no_wishes') }}</p>
    </div>
@endif
@endsection
