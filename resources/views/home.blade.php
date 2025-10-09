@extends('layouts.app')

@section('content')
<div class="text-center py-16">
    <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 mb-6">
        Galaxy Wishlist
    </h1>
    <p class="text-2xl text-gray-600 mb-8 max-w-2xl mx-auto">
        Створюйте та діліться своїми бажаннями з друзями і близькими
    </p>

    @auth
        <a href="{{ route('wishes.index') }}" class="inline-block px-10 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-xl font-semibold rounded-full hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-purple-300 transition transform hover:scale-105 shadow-2xl">
            ✨ Мої бажання
        </a>
    @else
        <a href="{{ route('login') }}" class="inline-block px-10 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-xl font-semibold rounded-full hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-purple-300 transition transform hover:scale-105 shadow-2xl">
            🚀 Увійти
        </a>
    @endauth
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2">
        <div class="text-6xl mb-4">✨</div>
        <h3 class="text-2xl font-bold text-purple-600 mb-3">Створюйте бажання</h3>
        <p class="text-gray-600 leading-relaxed">Додавайте свої мрії з описом, фото, посиланням та ціною. Автоматичне заповнення з URL!</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8 text-center hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2">
        <div class="text-6xl mb-4">👥</div>
        <h3 class="text-2xl font-bold text-purple-600 mb-3">Діліться з колегами</h3>
        <p class="text-gray-600 leading-relaxed">Переглядайте бажання інших користувачів і дізнавайтеся, що їм подарувати</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8 text-center hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2">
        <div class="text-6xl mb-4">🔒</div>
        <h3 class="text-2xl font-bold text-purple-600 mb-3">Безпечний вхід</h3>
        <p class="text-gray-600 leading-relaxed">Авторизація через Authentik SSO для захисту ваших даних</p>
    </div>
</div>

@auth
<div class="mt-16 bg-gradient-to-r from-purple-100 to-indigo-100 rounded-2xl p-8 text-center">
    <h2 class="text-3xl font-bold text-gray-800 mb-4">Готові почати?</h2>
    <p class="text-xl text-gray-600 mb-6">Додайте своє перше бажання прямо зараз!</p>
    <a href="{{ route('wishes.create') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition transform hover:scale-105 shadow-lg">
        ➕ Додати бажання
    </a>
</div>
@endauth
@endsection
