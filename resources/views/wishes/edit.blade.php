@extends('layouts.app')

@section('title', __('wishlist.edit_wish'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-8 py-6">
            <h2 class="text-3xl font-bold text-white">✏️ {{ __('wishlist.edit_wish') }}</h2>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('wishes.update', $wish) }}" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- URL Field with Auto-Parse Button -->
            <div>
                <label for="wish-url" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center space-x-2">
                        <span>🔗</span>
                        <span>{{ __('wishlist.wish_url') }}</span>
                    </span>
                </label>
                <div class="flex gap-2">
                    <input type="url" id="wish-url" name="url" value="{{ old('url', $wish->url) }}" placeholder="https://example.com/product" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    <button type="button" id="parse-url-btn" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition whitespace-nowrap">
                        🔍 Автозаповнення
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-500">Вставте посилання на товар і натисніть "Автозаповнення"</p>
                <div id="parsing-loading" class="hidden mt-2 flex items-center space-x-2 text-blue-600">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Завантаження даних...</span>
                </div>
            </div>

            <!-- Hidden field for image path from URL parser -->
            <input type="hidden" id="wish-image-path" name="image_path" value="">

            <!-- Title -->
            <div>
                <label for="wish-title" class="block text-sm font-medium text-gray-700 mb-2">{{ __('wishlist.wish_title') }} <span class="text-red-500">*</span></label>
                <input type="text" id="wish-title" name="title" value="{{ old('title', $wish->title) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
            </div>

            <!-- Description -->
            <div>
                <label for="wish-description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('wishlist.wish_description') }}</label>
                <textarea id="wish-description" name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition resize-none">{{ old('description', $wish->description) }}</textarea>
            </div>

            <!-- Current Image -->
            @if($wish->image)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Поточне фото</label>
                <img src="{{ asset('storage/' . $wish->image) }}" alt="{{ $wish->title }}" class="w-full max-w-md h-auto rounded-lg shadow-lg">
            </div>
            @endif

            <!-- Image Upload -->
            <div>
                <label for="wish-image" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center space-x-2">
                        <span>🖼️</span>
                        <span>{{ $wish->image ? 'Змінити фото' : 'Додати фото' }}</span>
                    </span>
                </label>
                <input type="file" id="wish-image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition">
                <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF, WEBP до 5MB</p>
                
                <!-- Image Preview -->
                <div id="image-preview-container" class="hidden mt-4">
                    <img id="image-preview" src="" alt="Preview" class="w-full max-w-md h-auto rounded-lg shadow-lg">
                </div>
            </div>

            <!-- Price and Currency -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="wish-price" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center space-x-2">
                            <span>💰</span>
                            <span>{{ __('wishlist.wish_price') }}</span>
                        </span>
                    </label>
                    <input type="number" id="wish-price" name="price" value="{{ old('price', $wish->price) }}" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="wish-currency" class="block text-sm font-medium text-gray-700 mb-2">{{ __('wishlist.wish_currency') }}</label>
                    <select id="wish-currency" name="currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                        <option value="UAH" {{ old('currency', $wish->currency) == 'UAH' ? 'selected' : '' }}>UAH ₴</option>
                        <option value="USD" {{ old('currency', $wish->currency) == 'USD' ? 'selected' : '' }}>USD $</option>
                        <option value="EUR" {{ old('currency', $wish->currency) == 'EUR' ? 'selected' : '' }}>EUR €</option>
                    </select>
                </div>
            </div>

            <!-- Priority -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center space-x-2">
                        <span>⭐</span>
                        <span>{{ __('wishlist.wish_priority') }}</span>
                    </span>
                </label>
                <select id="priority" name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    <option value="0" {{ old('priority', $wish->priority) == 0 ? 'selected' : '' }}>{{ __('wishlist.priority_low') }}</option>
                    <option value="1" {{ old('priority', $wish->priority) == 1 ? 'selected' : '' }}>{{ __('wishlist.priority_medium') }}</option>
                    <option value="2" {{ old('priority', $wish->priority) == 2 ? 'selected' : '' }}>{{ __('wishlist.priority_high') }}</option>
                </select>
            </div>

            <!-- Is Purchased -->
            <div class="flex items-center space-x-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                <input type="checkbox" id="is_purchased" name="is_purchased" value="1" {{ old('is_purchased', $wish->is_purchased) ? 'checked' : '' }} class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <label for="is_purchased" class="text-sm font-medium text-gray-700 cursor-pointer">
                    ✅ {{ __('wishlist.is_purchased') }}
                </label>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition transform hover:scale-105">
                    {{ __('wishlist.save') }}
                </button>
                <a href="{{ route('wishes.index') }}" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition text-center">
                    {{ __('wishlist.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
