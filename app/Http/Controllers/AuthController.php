<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Показати форму локального входу для адміна
     */
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    /**
     * Локальний вхід для адміна (тільки для розробки)
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Перенаправлення на Google для авторизації
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Обробка callback від Google
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('=== Google Callback Started ===');

            $googleUser = Socialite::driver('google')->user();

            Log::info('Google user data retrieved', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            if (! $googleUser->getEmail()) {
                return redirect()->route('login')->withErrors([
                    'error' => __('auth.google_no_email'),
                ]);
            }

            // Спершу шукаємо за google_id, потім за email,
            // і лише після цього створюємо нового користувача
            $user = User::where('google_id', $googleUser->getId())->first();

            if (! $user) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            if ($user) {
                // Не перезаписуємо ім'я/аватар, якщо користувач уже існує
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'email' => $googleUser->getEmail(),
                ]);
            } else {
                $user = User::create([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)), // Random password для OAuth користувачів
                ]);
            }

            Log::info('User created/updated', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            Auth::login($user, true);
            $request->session()->regenerate();

            Log::info('User logged in successfully');

            return redirect()->intended('/');
        } catch (\Exception $e) {
            Log::error('Google callback error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->withErrors([
                'error' => __('auth.google_failed').' '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Показати форму входу
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Вихід з системи
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
