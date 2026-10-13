@props(['user', 'size' => 40])

@php
    // Монограма як у Контактах: перші літери імені та прізвища (з підтримкою кирилиці)
    $initials = collect(preg_split('/\s+/u', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
@endphp

@if ($user->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="" width="{{ $size }}" height="{{ $size }}" referrerpolicy="no-referrer" loading="lazy"
        {{ $attributes->merge(['class' => 'shrink-0 rounded-full object-cover bg-fill']) }}
        style="width: {{ $size }}px; height: {{ $size }}px">
@else
    <span aria-hidden="true"
        {{ $attributes->merge(['class' => 'shrink-0 inline-flex items-center justify-center rounded-full font-semibold text-white select-none']) }}
        style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.4) }}px; letter-spacing: -0.01em; background: linear-gradient(180deg, #a5abb8, #858994)">{{ $initials }}</span>
@endif
