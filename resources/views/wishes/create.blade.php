@extends('layouts.app')

@section('title', __('wishlist.add_wish'))

@section('content')
<div class="card" style="max-width: 700px; margin: 2rem auto;">
    <h2>{{ __('wishlist.add_wish') }}</h2>

    <form method="POST" action="{{ route('wishes.store') }}">
        @csrf

        <div class="form-group">
            <label for="title">{{ __('wishlist.wish_title') }} *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="description">{{ __('wishlist.wish_description') }}</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="url">{{ __('wishlist.wish_url') }}</label>
            <input type="url" id="url" name="url" value="{{ old('url') }}" placeholder="https://">
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="price">{{ __('wishlist.wish_price') }}</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0">
            </div>

            <div class="form-group">
                <label for="currency">{{ __('wishlist.wish_currency') }}</label>
                <select id="currency" name="currency">
                    <option value="UAH" {{ old('currency') == 'UAH' ? 'selected' : '' }}>UAH</option>
                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="priority">{{ __('wishlist.wish_priority') }}</label>
            <select id="priority" name="priority">
                <option value="0" {{ old('priority') == 0 ? 'selected' : '' }}>{{ __('wishlist.priority_low') }}</option>
                <option value="1" {{ old('priority') == 1 ? 'selected' : '' }}>{{ __('wishlist.priority_medium') }}</option>
                <option value="2" {{ old('priority') == 2 ? 'selected' : '' }}>{{ __('wishlist.priority_high') }}</option>
            </select>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">{{ __('wishlist.save') }}</button>
            <a href="{{ route('wishes.index') }}" class="btn btn-secondary">{{ __('wishlist.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
