@extends('layouts.app')

@section('title', $user->name)

@section('content')
@php
    $next = $user->nextBirthday(today());
    $daysLeft = $next ? (int) today()->diffInDays($next) : null;
    $isMe = $user->is(auth()->user());
@endphp

<a href="{{ route('users.index') }}" class="btn btn-plain -ml-2 mb-2">
    <x-icon name="chevron-left" :size="18" :stroke="2.25" />
    {{ __('wishlist.all_users') }}
</a>

<div class="grid items-start gap-8 lg:grid-cols-[18rem_1fr]">
    {{-- Профіль --}}
    <aside class="lg:sticky lg:top-24">
        <div class="flex flex-col items-center text-center">
            <x-avatar :user="$user" :size="104" />
            <h1 class="text-title mt-4">{{ $user->name }}</h1>
            @if ($daysLeft !== null && $daysLeft <= 30)
                <x-days-left :days="$daysLeft" class="mt-2" />
            @endif
        </div>

        <div class="list mt-6" style="--inset: 3.25rem">
            <div class="list-row">
                <x-icon name="cake" :size="22" class="text-secondary" />
                <span class="min-w-0 flex-1">
                    <span class="block text-footnote text-secondary">День народження</span>
                    <span class="block">{{ $user->birthday ? $user->birthday->translatedFormat('j F') : 'Не вказано' }}</span>
                </span>
            </div>
            @if ($user->delivery_address)
                <div class="list-row">
                    <x-icon name="map-pin" :size="22" class="text-secondary" />
                    <span class="min-w-0 flex-1">
                        <span class="block text-footnote text-secondary">Адреса доставки</span>
                        <span class="block break-words whitespace-pre-line">{{ $user->delivery_address }}</span>
                    </span>
                </div>
            @endif
            <a href="mailto:{{ $user->email }}" class="list-row">
                <x-icon name="envelope" :size="22" class="text-secondary" />
                <span class="min-w-0 flex-1">
                    <span class="block text-footnote text-secondary">Email</span>
                    <span class="block truncate text-accent">{{ $user->email }}</span>
                </span>
            </a>
        </div>
    </aside>

    {{-- Бажання --}}
    <section>
        <h2 class="section-header">{{ $isMe ? __('wishlist.my_wishes') : 'Бажання' }} · {{ $wishes->count() }}</h2>

        @if ($wishes->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 md:gap-5">
                @foreach ($wishes->sortBy('is_purchased') as $wish)
                    <x-wish-card :wish="$wish" :editable="$isMe" />
                @endforeach
            </div>
        @else
            <div class="card flex flex-col items-center px-6 py-14 text-center">
                <span class="text-tertiary"><x-icon name="gift" :size="40" :stroke="1.25" /></span>
                <p class="mt-3 text-secondary">{{ $isMe ? 'Ви ще не додали жодного бажання.' : 'Тут поки порожньо.' }}</p>
            </div>
        @endif
    </section>
</div>
@endsection
