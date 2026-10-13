@extends('layouts.app')

@section('title', __('wishlist.all_users'))

@section('content')
<div class="mx-auto max-w-2xl">
    <header class="mb-6 md:mb-8">
        <h1 class="text-large-title">{{ __('wishlist.all_users') }}</h1>
        <p class="mt-1 text-secondary">{{ trans_choice(':count людина|:count людини|:count людей', $users->count()) }}</p>
    </header>

    <div class="list" style="--inset: 4.75rem">
        @foreach ($users as $user)
            @php
                $next = $user->nextBirthday(today());
                $daysLeft = $next ? (int) today()->diffInDays($next) : null;
            @endphp
            <a href="{{ route('users.show', $user) }}" class="list-row py-3">
                <x-avatar :user="$user" :size="46" />
                <span class="min-w-0 flex-1">
                    <span class="flex items-center gap-2">
                        <span class="truncate text-[1.0625rem] font-medium">{{ $user->name }}</span>
                        @if ($user->is(auth()->user()))
                            <span class="shrink-0 rounded-full bg-fill px-2 py-px text-footnote font-medium text-secondary">ви</span>
                        @endif
                    </span>
                    <span class="block truncate text-[0.9375rem] text-secondary">
                        {{ $user->birthday ? $user->birthday->translatedFormat('j F') : 'Дата не вказана' }}
                        @if ($daysLeft !== null && $daysLeft <= 30)
                            <span class="sm:hidden">· <x-days-left :days="$daysLeft" inline /></span>
                            <span class="hidden sm:inline">· {{ trans_choice(':count бажання|:count бажання|:count бажань', $user->wishes_count) }}</span>
                        @else
                            · {{ trans_choice(':count бажання|:count бажання|:count бажань', $user->wishes_count) }}
                        @endif
                    </span>
                </span>
                @if ($daysLeft !== null && $daysLeft <= 30)
                    <span class="hidden sm:block"><x-days-left :days="$daysLeft" /></span>
                @endif
                <x-icon name="chevron-right" :size="16" :stroke="2.25" class="text-tertiary" />
            </a>
        @endforeach
    </div>
</div>
@endsection
