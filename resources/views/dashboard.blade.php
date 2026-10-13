@extends('layouts.app')

@section('title', 'Огляд')

@section('content')
<header class="mb-8">
    <p class="text-footnote font-semibold tracking-[0.04em] text-secondary uppercase">{{ Str::ucfirst(now()->translatedFormat('l, j F')) }}</p>
    <h1 class="text-large-title mt-1">Привіт, {{ Str::before($user->name, ' ') }}</h1>
</header>

{{-- Що варто доповнити в профілі --}}
@if (! $user->birthday || $wishesCount === 0)
    <div class="mb-8 grid gap-3 sm:grid-cols-2">
        @unless ($user->birthday)
            <a href="{{ route('profile.edit') }}" class="card pressable flex items-center gap-4 p-4">
                <span class="flex size-11 items-center justify-center rounded-full bg-warning/15 text-warning">
                    <x-icon name="cake" :size="22" />
                </span>
                <span class="min-w-0 flex-1">
                    <span class="text-headline block">Вкажіть дату народження</span>
                    <span class="block text-[0.9375rem] text-secondary">Щоб колеги не пропустили ваше свято</span>
                </span>
                <x-icon name="chevron-right" :size="18" :stroke="2" class="text-tertiary" />
            </a>
        @endunless

        @if ($wishesCount === 0)
            <a href="{{ route('wishes.create') }}" class="card pressable flex items-center gap-4 p-4">
                <span class="flex size-11 items-center justify-center rounded-full bg-accent/12 text-accent">
                    <x-icon name="plus" :size="22" :stroke="2" />
                </span>
                <span class="min-w-0 flex-1">
                    <span class="text-headline block">Додайте перше бажання</span>
                    <span class="block text-[0.9375rem] text-secondary">Вішліст поки порожній</span>
                </span>
                <x-icon name="chevron-right" :size="18" :stroke="2" class="text-tertiary" />
            </a>
        @endif
    </div>
@endif

<div class="grid items-start gap-8 lg:grid-cols-[1fr_20rem]">
    <section>
        <h2 class="section-header">Найближчі дні народження</h2>

        @if ($upcomingBirthdays->isNotEmpty())
            <div class="list" style="--inset: 4.5rem">
                @foreach ($upcomingBirthdays as $friend)
                    <a href="{{ route('users.show', $friend) }}" class="list-row">
                        <x-avatar :user="$friend" :size="44" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-[1.0625rem] font-medium">{{ $friend->name }}</span>
                            <span class="block truncate text-[0.9375rem] text-secondary">
                                {{ $friend->next_birthday->translatedFormat('j F') }}
                                <span class="sm:hidden">· <x-days-left :days="$friend->days_left" inline /></span>
                                <span class="hidden sm:inline">· {{ trans_choice(':count бажання|:count бажання|:count бажань', $friend->active_wishes_count) }}</span>
                            </span>
                        </span>
                        <span class="hidden sm:block"><x-days-left :days="$friend->days_left" /></span>
                        <x-icon name="chevron-right" :size="16" :stroke="2.25" class="text-tertiary" />
                    </a>
                @endforeach
            </div>
        @else
            <div class="list px-4 py-10 text-center text-secondary">
                Поки ніхто з колег не вказав дату народження.
            </div>
        @endif
    </section>

    <section>
        <h2 class="section-header">{{ __('wishlist.my_wishes') }}</h2>
        <div class="card p-5">
            <p class="text-[3rem] leading-none font-bold tracking-[-0.03em] tabular-nums">{{ $activeWishesCount }}</p>
            <p class="mt-1 text-secondary">{{ trans_choice('активне бажання|активні бажання|активних бажань', $activeWishesCount) }}</p>
            <div class="mt-5 flex gap-2">
                <a href="{{ route('wishes.create') }}" class="btn btn-primary btn-sm flex-1">
                    <x-icon name="plus" :size="16" :stroke="2.25" />
                    Додати
                </a>
                <a href="{{ route('wishes.index') }}" class="btn btn-secondary btn-sm flex-1">Переглянути</a>
            </div>
        </div>
    </section>
</div>
@endsection
