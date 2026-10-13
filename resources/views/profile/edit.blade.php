@extends('layouts.app')

@section('title', __('wishlist.profile'))

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-large-title mb-6">{{ __('wishlist.profile') }}</h1>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <section class="card flex flex-col items-center p-6 text-center">
            <div class="relative">
                <x-avatar :user="$user" :size="96" />
                <img id="avatar-preview" alt="" class="fade-in absolute inset-0 hidden size-24 rounded-full object-cover">
            </div>
            <label for="avatar" class="btn btn-plain btn-sm mt-3 cursor-pointer">Змінити фото</label>
            <input type="file" id="avatar" name="avatar" accept="image/*" class="sr-only" data-preview="avatar-preview">
            <p class="text-footnote text-tertiary">JPG, PNG або GIF, до 2 МБ</p>
            @error('avatar') <p class="field-error">{{ $message }}</p> @enderror
        </section>

        <section class="card space-y-5 p-5">
            <div>
                <label for="name" class="field-label">{{ __('auth.name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="field" @error('name') aria-invalid="true" @enderror>
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="field-label">{{ __('auth.email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                    autocapitalize="none" spellcheck="false" class="field" @error('email') aria-invalid="true" @enderror>
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="card space-y-5 p-5">
            <div>
                <label for="birthday" class="field-label">День народження</label>
                <input type="date" id="birthday" name="birthday" value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}"
                    class="field" @error('birthday') aria-invalid="true" @enderror>
                @error('birthday')
                    <p class="field-error">{{ $message }}</p>
                @else
                    <p class="field-hint">Колеги бачать лише день і місяць, а за тиждень до свята отримають нагадування.</p>
                @enderror
            </div>

            <div>
                <label for="delivery_address" class="field-label">Адреса для доставки подарунків</label>
                <textarea id="delivery_address" name="delivery_address" rows="3" autocomplete="street-address"
                    placeholder="Місто, відділення Нової пошти або вулиця і будинок"
                    class="field" @error('delivery_address') aria-invalid="true" @enderror>{{ old('delivery_address', $user->delivery_address) }}</textarea>
                @error('delivery_address') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="flex justify-end pt-1">
            <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto sm:min-w-40">{{ __('wishlist.save') }}</button>
        </div>
    </form>

    <form action="{{ route('logout') }}" method="POST" class="mt-10">
        @csrf
        <button type="submit" class="list w-full px-4 py-3.5 text-center text-[1.0625rem] font-medium text-danger active:bg-fill">
            {{ __('auth.logout') }}
        </button>
    </form>
</div>
@endsection
