@extends('layouts.app')

@section('title', __('wishlist.edit_wish'))

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('wishes.index') }}" class="btn btn-plain -ml-2 mb-2">
        <x-icon name="chevron-left" :size="18" :stroke="2.25" />
        {{ __('wishlist.my_wishes') }}
    </a>
    <h1 class="text-large-title mb-6">{{ __('wishlist.edit_wish') }}</h1>

    <form method="POST" action="{{ route('wishes.update', $wish) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('wishes._form', ['wish' => $wish])
    </form>
</div>
@endsection
