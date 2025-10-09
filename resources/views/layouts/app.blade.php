<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        nav { background: #4a148c; color: white; padding: 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        nav .container { display: flex; justify-content: space-between; align-items: center; }
        nav h1 { font-size: 1.5rem; margin: 0; }
        nav ul { list-style: none; display: flex; gap: 1.5rem; align-items: center; }
        nav a { color: white; text-decoration: none; transition: opacity 0.3s; }
        nav a:hover { opacity: 0.8; }
        .btn { display: inline-block; padding: 0.6rem 1.2rem; background: #6a1b9a; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; transition: background 0.3s; }
        .btn:hover { background: #8e24aa; }
        .btn-secondary { background: #757575; }
        .btn-secondary:hover { background: #9e9e9e; }
        .btn-danger { background: #d32f2f; }
        .btn-danger:hover { background: #f44336; }
        .alert { padding: 1rem; margin: 1rem 0; border-radius: 4px; }
        .alert-success { background: #c8e6c9; color: #2e7d32; }
        .alert-error { background: #ffcdd2; color: #c62828; }
        .card { background: white; padding: 1.5rem; margin: 1rem 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 1rem; color: #4a148c; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin: 1.5rem 0; }
        .wish-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .wish-card:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .wish-card h3 { color: #4a148c; margin-bottom: 0.5rem; }
        .wish-card .price { font-size: 1.2rem; font-weight: bold; color: #6a1b9a; margin: 0.5rem 0; }
        .wish-card .actions { display: flex; gap: 0.5rem; margin-top: 1rem; }
        .avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .user-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .user-card { background: white; padding: 1.5rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .user-card .avatar { width: 80px; height: 80px; margin-bottom: 1rem; }
        .priority-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 500; }
        .priority-0 { background: #e3f2fd; color: #1976d2; }
        .priority-1 { background: #fff3e0; color: #f57c00; }
        .priority-2 { background: #ffebee; color: #c62828; }
        .purchased { opacity: 0.6; text-decoration: line-through; }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <h1>🌌 {{ config('app.name') }}</h1>
            <ul>
                @auth
                    <li><a href="{{ route('wishes.index') }}">{{ __('wishlist.my_wishes') }}</a></li>
                    <li><a href="{{ route('users.index') }}">{{ __('wishlist.all_users') }}</a></li>
                    <li><a href="{{ route('profile.edit') }}">{{ __('wishlist.profile') }}</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary">{{ __('auth.logout') }}</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">{{ __('auth.login') }}</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
