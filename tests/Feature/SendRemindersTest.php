<?php

use App\Models\SentReminder;
use App\Models\User;
use App\Models\Wish;
use App\Notifications\Reminders\EmptyWishlistReminder;
use App\Notifications\Reminders\MissingBirthdayReminder;
use App\Notifications\Reminders\OwnBirthdayReminder;
use App\Notifications\Reminders\UpcomingBirthdayReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
    Carbon::setTestNow('2026-10-13 10:00:00');

    config([
        'reminders.enabled' => true,
        'reminders.birthday_days_before' => 7,
        'reminders.owner_days_before' => 14,
        'reminders.profile_repeat_days' => 7,
    ]);
});

function member(array $attributes = [], int $wishes = 1): User
{
    $user = User::factory()->create($attributes + [
        'birthday' => '1990-01-01',
        'created_at' => now()->subMonth(),
    ]);

    for ($i = 0; $i < $wishes; $i++) {
        Wish::create(['user_id' => $user->id, 'title' => "Бажання {$i}"]);
    }

    return $user;
}

test('нагадує тим, хто не вказав дату народження', function () {
    $without = member(['birthday' => null]);
    $with = member();

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertSentTo($without, MissingBirthdayReminder::class);
    Notification::assertNotSentTo($with, MissingBirthdayReminder::class);
});

test('нагадує тим, хто не додав жодного бажання', function () {
    $empty = member(wishes: 0);
    $filled = member();

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertSentTo($empty, EmptyWishlistReminder::class);
    Notification::assertNotSentTo($filled, EmptyWishlistReminder::class);
});

test('не нагадує новим користувачам та адмінам', function () {
    $fresh = member(['birthday' => null, 'created_at' => now()->subHour()], wishes: 0);
    $admin = member(['birthday' => null, 'is_admin' => true], wishes: 0);

    $this->artisan('reminders:send')->assertSuccessful();

    Notification::assertNothingSentTo($fresh);
    Notification::assertNothingSentTo($admin);
});

test('повторює нагадування про профіль не частіше ніж раз на N днів', function () {
    $user = member(['birthday' => null]);

    $this->artisan('reminders:send');
    Carbon::setTestNow(now()->addDays(3));
    $this->artisan('reminders:send');

    Notification::assertSentToTimes($user, MissingBirthdayReminder::class, 1);

    Carbon::setTestNow(now()->addDays(4)->addMinute());
    $this->artisan('reminders:send');

    Notification::assertSentToTimes($user, MissingBirthdayReminder::class, 2);
});

test('за N днів нагадує всім, крім іменинника', function () {
    $celebrant = member(['birthday' => '1995-10-20']); // через 7 днів
    $friend = member();
    $anotherFriend = member(['birthday' => null]);

    $this->artisan('reminders:send')->assertSuccessful();

    foreach ([$friend, $anotherFriend] as $user) {
        Notification::assertSentTo(
            $user,
            UpcomingBirthdayReminder::class,
            fn ($n) => $n->celebrant->is($celebrant) && $n->daysLeft === 7,
        );
    }
    Notification::assertNotSentTo($celebrant, UpcomingBirthdayReminder::class);
});

test('не нагадує гостям раніше ніж за N днів і не дублює', function () {
    member(['birthday' => '1995-10-21']); // через 8 днів
    $friend = member();

    $this->artisan('reminders:send');
    Notification::assertNotSentTo($friend, UpcomingBirthdayReminder::class);

    Carbon::setTestNow(now()->addDay());
    $this->artisan('reminders:send');
    Carbon::setTestNow(now()->addDay());
    $this->artisan('reminders:send');

    Notification::assertSentToTimes($friend, UpcomingBirthdayReminder::class, 1);
});

test('надолужує пропущений день', function () {
    member(['birthday' => '1995-10-18']); // через 5 днів, хоча N = 7
    $friend = member();

    $this->artisan('reminders:send');

    Notification::assertSentTo($friend, UpcomingBirthdayReminder::class, fn ($n) => $n->daysLeft === 5);
});

test('за N днів нагадує імениннику переглянути свій вішліст', function () {
    $celebrant = member(['birthday' => '1995-10-27']); // через 14 днів
    $friend = member();

    $this->artisan('reminders:send');
    $this->artisan('reminders:send');

    Notification::assertSentToTimes($celebrant, OwnBirthdayReminder::class, 1);
    Notification::assertNotSentTo($friend, OwnBirthdayReminder::class);
    Notification::assertNotSentTo($friend, UpcomingBirthdayReminder::class);
});

test('рахує 29 лютого як 28 лютого в невисокосний рік', function () {
    Carbon::setTestNow('2027-02-21 10:00:00');
    member(['birthday' => '2000-02-29']);
    $friend = member();

    $this->artisan('reminders:send');

    Notification::assertSentTo($friend, UpcomingBirthdayReminder::class, fn ($n) => $n->birthday->toDateString() === '2027-02-28');
});

test('dry-run нічого не надсилає', function () {
    member(['birthday' => null], wishes: 0);

    $this->artisan('reminders:send --dry-run')->assertSuccessful();

    Notification::assertNothingSent();
    expect(SentReminder::count())->toBe(0);
});

test('лист рендериться українською', function () {
    $celebrant = member(['name' => 'Олена', 'birthday' => '1995-10-20'], wishes: 2);
    $friend = member(['name' => 'Андрій']);

    $mail = (new UpcomingBirthdayReminder($celebrant, Carbon::parse('2026-10-20'), 7))->toMail($friend);

    expect($mail->subject)->toBe('Олена святкує день народження через 7 днів')
        ->and($mail->introLines)->toContain('Олена святкує день народження через 7 днів (20 жовтня).')
        ->and($mail->introLines)->toContain('У вішлісті 2 бажання, які ще не куплено.')
        ->and($mail->actionUrl)->toBe(route('users.show', $celebrant));
});
