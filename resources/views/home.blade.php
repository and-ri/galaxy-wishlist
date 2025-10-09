@extends('layouts.app')

@section('content')
<div style="text-align: center; padding: 4rem 2rem;">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">🌌 Galaxy Wishlist</h1>
    <p style="font-size: 1.3rem; color: #666; margin-bottom: 2rem;">
        Створюйте та діліться своїми бажаннями з друзями
    </p>

    @auth
        <a href="{{ route('wishes.index') }}" class="btn" style="font-size: 1.2rem; padding: 1rem 2rem;">
            Мої бажання
        </a>
    @else
        <a href="{{ route('login') }}" class="btn" style="font-size: 1.2rem; padding: 1rem 2rem;">
            Увійти
        </a>
    @endauth
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 3rem 0;">
    <div class="card" style="text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">✨</div>
        <h3 style="color: #4a148c; margin-bottom: 0.5rem;">Створюйте бажання</h3>
        <p style="color: #666;">Додавайте свої мрії з описом, посиланням та ціною</p>
    </div>

    <div class="card" style="text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
        <h3 style="color: #4a148c; margin-bottom: 0.5rem;">Діліться з друзями</h3>
        <p style="color: #666;">Переглядайте бажання інших користувачів</p>
    </div>

    <div class="card" style="text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
        <h3 style="color: #4a148c; margin-bottom: 0.5rem;">Безпечний вхід</h3>
        <p style="color: #666;">Авторизація через Authentik SSO</p>
    </div>
</div>
@endsection
