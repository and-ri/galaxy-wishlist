<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Лендинг для гостей, огляд для авторизованих
     */
    public function __invoke()
    {
        $user = Auth::user();

        if (! $user) {
            return view('home');
        }

        $today = Carbon::today();

        $upcomingBirthdays = User::whereKeyNot($user->id)
            ->where('is_admin', false)
            ->whereNotNull('birthday')
            ->withCount(['wishes as active_wishes_count' => fn ($query) => $query->where('is_purchased', false)])
            ->get()
            ->map(function (User $friend) use ($today) {
                $friend->next_birthday = $friend->nextBirthday($today);
                $friend->days_left = (int) $today->diffInDays($friend->next_birthday);

                return $friend;
            })
            ->sortBy('days_left')
            ->take(5)
            ->values();

        return view('dashboard', [
            'user' => $user,
            'upcomingBirthdays' => $upcomingBirthdays,
            'activeWishesCount' => $user->wishes()->where('is_purchased', false)->count(),
            'wishesCount' => $user->wishes()->count(),
        ]);
    }
}
