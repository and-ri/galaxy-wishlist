<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Показати список всіх користувачів
     */
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Показати профіль користувача та його бажання
     */
    public function show(User $user)
    {
        $wishes = $user->wishes()->latest()->get();
        return view('users.show', compact('user', 'wishes'));
    }
}
