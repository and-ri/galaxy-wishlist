{{-- Спільна форма для створення та редагування бажання. $wish — null при створенні. --}}
@php
    $wish ??= null;
    $currency = old('currency', $wish?->currency ?? 'UAH');
    $priority = (int) old('priority', $wish?->priority ?? 1);
@endphp

<div class="space-y-5">
    {{-- Автозаповнення з посилання --}}
    <section class="card p-5">
        <label for="wish-url" class="field-label">{{ __('wishlist.wish_url') }}</label>
        <div class="flex flex-col gap-2 sm:flex-row">
            <input type="url" id="wish-url" name="url" value="{{ old('url', $wish?->url) }}" placeholder="https://"
                inputmode="url" autocapitalize="none" autocorrect="off" spellcheck="false"
                class="field flex-1" @error('url') aria-invalid="true" @enderror>
            <button type="button" id="parse-url-btn" class="btn btn-tinted h-auto min-h-11">
                <x-icon name="sparkles" :size="18" />
                <span data-label>Заповнити</span>
            </button>
        </div>
        @error('url') <p class="field-error">{{ $message }}</p> @enderror

        <p id="parse-status" class="field-hint" aria-live="polite">Вставте посилання — назва, ціна і фото підтягнуться самі.</p>
    </section>

    <input type="hidden" id="wish-image-path" name="image_path" value="">

    <section class="card space-y-5 p-5">
        <div>
            <label for="wish-title" class="field-label">{{ __('wishlist.wish_title') }}</label>
            <input type="text" id="wish-title" name="title" value="{{ old('title', $wish?->title) }}" required maxlength="255"
                class="field" @error('title') aria-invalid="true" @enderror>
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="wish-description" class="field-label">{{ __('wishlist.wish_description') }} <span class="text-tertiary">· необов'язково</span></label>
            <textarea id="wish-description" name="description" rows="3" placeholder="Розмір, колір, модель…"
                class="field" @error('description') aria-invalid="true" @enderror>{{ old('description', $wish?->description) }}</textarea>
            @error('description') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </section>

    {{-- Фото --}}
    <section class="card p-5">
        <span class="field-label">Фото</span>
        <div class="flex items-center gap-4">
            <div class="relative size-24 shrink-0 overflow-hidden rounded-2xl bg-fill">
                <div class="flex h-full items-center justify-center text-tertiary">
                    <x-icon name="photo" :size="32" :stroke="1.25" />
                </div>
                <div id="image-preview-container" class="{{ $wish?->image ? '' : 'hidden' }} fade-in absolute inset-0">
                    <img id="image-preview" src="{{ $wish?->image ? asset('storage/'.$wish->image) : '' }}" alt="" class="h-full w-full object-cover">
                </div>
            </div>
            <div class="min-w-0">
                <label for="wish-image" class="btn btn-secondary btn-sm cursor-pointer">{{ $wish?->image ? 'Змінити фото' : 'Вибрати фото' }}</label>
                <input type="file" id="wish-image" name="image" accept="image/*" class="sr-only">
                <p class="field-hint">JPG, PNG, GIF або WEBP, до 5 МБ</p>
            </div>
        </div>
        @error('image') <p class="field-error">{{ $message }}</p> @enderror
    </section>

    <section class="card space-y-5 p-5">
        <div class="grid gap-5 sm:grid-cols-[1fr_auto]">
            <div>
                <label for="wish-price" class="field-label">{{ __('wishlist.wish_price') }}</label>
                <input type="number" id="wish-price" name="price" value="{{ old('price', $wish?->price) }}" step="0.01" min="0"
                    inputmode="decimal" placeholder="0" class="field tabular-nums" @error('price') aria-invalid="true" @enderror>
                @error('price') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <fieldset>
                <legend class="field-label">{{ __('wishlist.wish_currency') }}</legend>
                <div class="segmented sm:w-48" style="--segments: 3">
                    @foreach (['UAH' => '₴', 'USD' => '$', 'EUR' => '€'] as $code => $symbol)
                        <input type="radio" name="currency" id="currency-{{ $code }}" value="{{ $code }}" @checked($currency === $code)>
                        <label for="currency-{{ $code }}" class="py-2.5!" title="{{ $code }}">{{ $symbol }}</label>
                    @endforeach
                </div>
            </fieldset>
        </div>

        <fieldset>
            <legend class="field-label">Наскільки хочеться</legend>
            <div class="segmented" style="--segments: 3">
                @foreach ([0 => 'Можна потім', 1 => 'Хочу', 2 => 'Дуже хочу'] as $value => $label)
                    <input type="radio" name="priority" id="priority-{{ $value }}" value="{{ $value }}" @checked($priority === $value)>
                    <label for="priority-{{ $value }}" class="py-2.5!">{{ $label }}</label>
                @endforeach
            </div>
        </fieldset>
    </section>

    @if ($wish)
        <label class="card flex cursor-pointer items-center gap-4 p-5">
            <span class="min-w-0 flex-1">
                <span class="text-headline block">{{ __('wishlist.is_purchased') }}</span>
                <span class="block text-[0.9375rem] text-secondary">Позначте, коли подарунок уже отримали</span>
            </span>
            {{-- Без прихованого поля знята галочка не доходить до сервера --}}
            <input type="hidden" name="is_purchased" value="0">
            <input type="checkbox" name="is_purchased" value="1" class="switch" @checked(old('is_purchased', $wish->is_purchased))>
        </label>
    @endif

    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
        <a href="{{ route('wishes.index') }}" class="btn btn-secondary btn-lg">{{ __('wishlist.cancel') }}</a>
        <button type="submit" class="btn btn-primary btn-lg sm:min-w-40">{{ __('wishlist.save') }}</button>
    </div>
</div>
