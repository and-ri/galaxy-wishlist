@extends('layouts.app')

@section('title', __('wishlist.all_users'))

@section('content')
<h1 style="margin: 2rem 0;">{{ __('wishlist.all_users') }}</h1>

<div class="user-grid">
    @foreach($users as $user)
        <div class="user-card">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar">
            @else
                <div class="avatar" style="background: #6a1b9a; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            
            <h3>{{ $user->name }}</h3>
            <p style="color: #666; margin: 0.5rem 0;">{{ $user->wishes->count() }} {{ __('wishlist.my_wishes') }}</p>
            
            <a href="{{ route('users.show', $user) }}" class="btn" style="margin-top: 1rem;">Переглянути бажання</a>
        </div>
    @endforeach
</div>
@endsection
