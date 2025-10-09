@extends('layouts.app')

@section('title', __('wishlist.edit_profile'))

@section('content')
<div class="card" style="max-width: 700px; margin: 2rem auto;">
    <h2>{{ __('wishlist.edit_profile') }}</h2>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="text-align: center; margin-bottom: 2rem;">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar" style="width: 100px; height: 100px;">
            @else
                <div class="avatar" style="width: 100px; height: 100px; background: #6a1b9a; color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        <div class="form-group">
            <label for="name">{{ __('auth.name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label for="avatar">{{ __('wishlist.upload_avatar') }}</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">
            <small style="color: #666;">JPG, PNG, GIF до 2MB</small>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">{{ __('wishlist.save') }}</button>
            <a href="{{ route('wishes.index') }}" class="btn btn-secondary">{{ __('wishlist.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
