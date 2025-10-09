@extends('layouts.app')

@section('title', __('wishlist.my_wishes'))

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin: 2rem 0;">
    <h1>{{ __('wishlist.my_wishes') }}</h1>
    <a href="{{ route('wishes.create') }}" class="btn">➕ {{ __('wishlist.add_wish') }}</a>
</div>

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
                    <p style="margin: 0.5rem 0; color: #666;">{{ Str::limit($wish->description, 100) }}</p>
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

                <div class="actions">
                    <a href="{{ route('wishes.edit', $wish) }}" class="btn">✏️ Редагувати</a>
                    <form action="{{ route('wishes.destroy', $wish) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ __('wishlist.delete_confirmation') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑️ Видалити</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card" style="text-align: center; padding: 3rem;">
        <p style="font-size: 1.2rem; color: #666;">{{ __('wishlist.no_wishes') }}</p>
        <a href="{{ route('wishes.create') }}" class="btn" style="margin-top: 1rem;">➕ {{ __('wishlist.add_wish') }}</a>
    </div>
@endif
@endsection
