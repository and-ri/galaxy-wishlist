@props(['wish', 'editable' => false])

<article class="card flex flex-col overflow-hidden">
    <div class="relative aspect-[4/3] bg-fill">
        @if ($wish->image)
            <img src="{{ asset('storage/'.$wish->image) }}" alt="" loading="lazy"
                class="h-full w-full object-cover {{ $wish->is_purchased ? 'opacity-50 grayscale' : '' }}">
        @else
            <div class="flex h-full items-center justify-center text-tertiary">
                <x-icon name="gift" :size="44" :stroke="1.25" />
            </div>
        @endif

        @if ($wish->is_purchased)
            <span class="glass absolute top-3 left-3 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-footnote font-semibold text-success">
                <x-icon name="check" :size="14" :stroke="2.5" />
                {{ __('wishlist.is_purchased') }}
            </span>
        @elseif ($wish->priority === 2)
            <span class="glass absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-footnote font-semibold">
                <span class="size-1.5 rounded-full bg-warning"></span>
                Дуже хочу
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4 {{ $wish->is_purchased ? 'opacity-60' : '' }}">
        <h3 class="text-headline line-clamp-2 {{ $wish->is_purchased ? 'line-through decoration-1' : '' }}">{{ $wish->title }}</h3>

        @if ($wish->description)
            <p class="mt-1 line-clamp-2 text-[0.9375rem] leading-snug text-secondary">{{ $wish->description }}</p>
        @endif

        <div class="mt-auto flex items-center gap-2 pt-4">
            @if ($wish->formatted_price)
                <span class="text-headline tabular-nums">{{ $wish->formatted_price }}</span>
            @endif

            <div class="ml-auto flex items-center gap-1.5">
                @if ($wish->url)
                    <a href="{{ $wish->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-tinted btn-sm">
                        Відкрити
                        <x-icon name="arrow-up-right" :size="14" :stroke="2.25" />
                    </a>
                @endif

                @if ($editable)
                    <a href="{{ route('wishes.edit', $wish) }}" class="btn btn-secondary btn-icon" aria-label="Редагувати «{{ $wish->title }}»">
                        <x-icon name="pencil" :size="16" :stroke="2" />
                    </a>
                    <form action="{{ route('wishes.destroy', $wish) }}" method="POST" onsubmit="return confirm(@js(__('wishlist.delete_confirmation')))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-icon" aria-label="Видалити «{{ $wish->title }}»">
                            <x-icon name="trash" :size="16" :stroke="2" />
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</article>
