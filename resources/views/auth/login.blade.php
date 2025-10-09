@extends('layouts.app')

@section('title', __('auth.login'))

@section('content')
<div class="card" style="max-width: 500px; margin: 3rem auto;">
    <h2>{{ __('auth.login') }}</h2>
    
    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ route('auth.authentik') }}" class="btn" style="font-size: 1.1rem; padding: 1rem 2rem;">
            🔐 {{ __('auth.login_with_authentik') }}
        </a>
    </div>

    <hr style="margin: 2rem 0;">

    <p style="text-align: center; color: #666;">
        <a href="{{ route('admin.login') }}">{{ __('auth.admin_login') }}</a>
    </p>
</div>
@endsection
