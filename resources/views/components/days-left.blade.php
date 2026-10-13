@props(['days', 'inline' => false])

@php
    $label = match (true) {
        $days === 0 => __('reminders.today'),
        $days === 1 => __('reminders.tomorrow'),
        default => trans_choice('reminders.in_days', $days),
    };
    $soon = $days <= 7;
@endphp

@if ($inline)
    {{-- Текстовий варіант для вузьких рядків списку --}}
    <span {{ $attributes->merge(['class' => $soon ? 'font-medium text-accent' : '']) }}>{{ $label }}</span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-footnote font-semibold whitespace-nowrap '.($soon ? 'bg-accent/12 text-accent' : 'bg-fill text-secondary')]) }}>
        {{ Str::ucfirst($label) }}
    </span>
@endif
