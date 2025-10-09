@extends('layouts.app')

@section('title', __('auth.admin_login'))

@section('content')
<div class="card" style="max-width: 500px; margin: 3rem auto;">
    <h2>{{ __('auth.admin_login') }}</h2>
    <p style="color: #666; margin-bottom: 1.5rem;">Тільки для локальної розробки</p>

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div class="form-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                {{ __('auth.remember') }}
            </label>
        </div>

        <button type="submit" class="btn">{{ __('auth.login') }}</button>
    </form>
</div>
@endsection
