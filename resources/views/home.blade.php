@extends('layouts.app')

@section('content')
<section class="flex min-h-[64svh] flex-col items-center justify-center py-10 text-center">
    <span class="mb-8 flex size-18 items-center justify-center rounded-[22px] bg-accent text-white shadow-[0_12px_32px_-8px_var(--color-accent)]">
        <x-icon name="gift" :size="38" :stroke="1.5" />
    </span>

    <h1 class="max-w-3xl text-[clamp(2.5rem,7vw,4.5rem)] leading-[1.04] font-bold tracking-[-0.035em]">
        Подарунки, які&nbsp;справді хочуть&nbsp;отримати.
    </h1>

    <p class="mt-5 max-w-xl text-[1.1875rem] leading-relaxed text-secondary">
        Складіть свій вішліст за хвилину. Колеги побачать, що вам подарувати, і&nbsp;отримають нагадування перед днем народження.
    </p>

    <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-9">Почати</a>
</section>

<section class="mx-auto grid max-w-4xl gap-8 border-t border-separator pt-12 sm:grid-cols-3 sm:gap-10">
    @foreach ([
        ['icon' => 'link', 'title' => 'Вставте посилання', 'text' => 'Назва, ціна і фото товару підтягнуться самі.'],
        ['icon' => 'bell', 'title' => 'Нагадаємо вчасно', 'text' => 'Лист за тиждень до свята — і вам, і колегам.'],
        ['icon' => 'gift', 'title' => 'Усе в одному місці', 'text' => 'Ціни, посилання і фото: колегам не доведеться гадати.'],
    ] as $feature)
        <div>
            <span class="flex size-10 items-center justify-center rounded-xl bg-accent/10 text-accent">
                <x-icon :name="$feature['icon']" :size="22" />
            </span>
            <h2 class="text-headline mt-4">{{ $feature['title'] }}</h2>
            <p class="mt-1 text-[0.9375rem] leading-relaxed text-secondary">{{ $feature['text'] }}</p>
        </div>
    @endforeach
</section>
@endsection
