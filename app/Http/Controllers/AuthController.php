<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Перенаправлення на Authentik для авторизації
     */
    public function redirectToAuthentik()
    {
        return Socialite::driver('authentik')->redirect();
    }

    /**
     * Обробка callback від Authentik
     */
    public function handleAuthentikCallback()
    {
        try {
            $authentikUser = Socialite::driver('authentik')->user();
            
            $user = User::updateOrCreate(
                ['authentik_id' => $authentikUser->getId()],
                [
                    'name' => $authentikUser->getName(),
                    'email' => $authentikUser->getEmail(),
                    'avatar' => $authentikUser->getAvatar(),
                ]
            );

            Auth::login($user);

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Помилка авторизації через Authentik']);
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
