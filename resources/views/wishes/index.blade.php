@extends('layouts.app')

@section('title', __('wishlist.my_wishes'))

@section('content')
<header class="mb-6 flex items-end justify-between gap-4 md:mb-8">
    <div>
        <h1 class="text-large-title">{{ __('wishlist.my_wishes') }}</h1>
        @if ($wishes->isNotEmpty())
            @php $active = $wishes->where('is_purchased', false)->count(); @endphp
            <p class="mt-1 text-secondary">
                {{ trans_choice(':count активне|:count активні|:count активних', $active) }}
                @if ($wishes->count() > $active) · {{ $wishes->count() - $active }} куплено @endif
            </p>
        @endif
    </div>

    @if ($wishes->isNotEmpty())
        <a href="{{ route('wishes.create') }}" class="btn btn-primary shrink-0">
            <x-icon name="plus" :size="18" :stroke="2.25" />
            <span class="hidden sm:inline">{{ __('wishlist.add_wish') }}</span>
            <span class="sm:hidden">Додати</span>
        </a>
    @endif
</header>

@if ($wishes->isNotEmpty())
    <div class="grid gap-4 sm:grid-cols-2 md:gap-5 lg:grid-cols-3">
        @foreach ($wishes->sortBy('is_purchased') as $wish)
            <x-wish-card :wish="$wish" editable />
        @endforeach
    </div>
@else
    <div class="card flex flex-col items-center px-6 py-16 text-center">
        <span class="flex size-16 items-center justify-center rounded-full bg-accent/10 text-accent">
            <x-icon name="gift" :size="32" :stroke="1.5" />
        </span>
        <h2 class="text-title mt-5">Вішліст поки порожній</h2>
        <p class="mt-2 max-w-sm text-secondary">Додайте те, що хотіли б отримати. Найпростіше — вставити посилання на товар.</p>
        <a href="{{ route('wishes.create') }}" class="btn btn-primary mt-7">
            <x-icon name="plus" :size="18" :stroke="2.25" />
            {{ __('wishlist.add_wish') }}
        </a>
    </div>
@endif
@endsection
