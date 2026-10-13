<?php

namespace App\Console\Commands;

use App\Models\SentReminder;
use App\Models\User;
use App\Notifications\Reminders\EmptyWishlistReminder;
use App\Notifications\Reminders\MissingBirthdayReminder;
use App\Notifications\Reminders\OwnBirthdayReminder;
use App\Notifications\Reminders\ReminderNotification;
use App\Notifications\Reminders\UpcomingBirthdayReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SendReminders extends Command
{
    protected $signature = 'reminders:send
                            {--dry-run : Лише показати, кому що буде надіслано}';

    protected $description = 'Надіслати E-mail нагадування (дата народження, порожній вішліст, найближчі дні народження)';

    private int $sent = 0;

    private int $failed = 0;

    public function handle(): int
    {
        if (! config('reminders.enabled')) {
            $this->warn('Нагадування вимкнено (REMINDERS_ENABLED=false).');

            return self::SUCCESS;
        }

        $today = Carbon::today();
        $recipients = User::reminderRecipients()->withCount('wishes')->get();

        $this->sendMissingBirthdayReminders($recipients);
        $this->sendEmptyWishlistReminders($recipients);
        $this->sendBirthdayReminders($recipients, $today);

        $this->info(($this->option('dry-run') ? 'Буде надіслано' : 'Надіслано').": {$this->sent}, помилок: {$this->failed}.");

        return $this->failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * 1) Тим, хто не вказав дату народження.
     */
    private function sendMissingBirthdayReminders(Collection $recipients): void
    {
        $recipients
            ->filter(fn (User $user) => ! $user->birthday && $this->isPastGracePeriod($user))
            ->reject(fn (User $user) => $this->sentRecently($user, SentReminder::MISSING_BIRTHDAY))
            ->each(fn (User $user) => $this->deliver($user, new MissingBirthdayReminder, SentReminder::MISSING_BIRTHDAY));
    }

    /**
     * 3) Тим, хто не додав жодного бажання.
     */
    private function sendEmptyWishlistReminders(Collection $recipients): void
    {
        $recipients
            ->filter(fn (User $user) => $user->wishes_count === 0 && $this->isPastGracePeriod($user))
            ->reject(fn (User $user) => $this->sentRecently($user, SentReminder::EMPTY_WISHLIST))
            ->each(fn (User $user) => $this->deliver($user, new EmptyWishlistReminder, SentReminder::EMPTY_WISHLIST));
    }

    /**
     * 2) Всім, крім іменинника, за N днів до дня народження.
     * 4) Імениннику за N днів — переглянути свій вішліст.
     *
     * Використовується «не пізніше ніж за N днів», а не точний збіг, щоб
     * нагадування не загубилось, якщо планувальник пропустив день або
     * користувач вказав дату народження, коли до неї лишилось менше N днів.
     * Дублікати відсікаються за reference (іменинник + дата свята).
     */
    private function sendBirthdayReminders(Collection $recipients, Carbon $today): void
    {
        $guestDays = (int) config('reminders.birthday_days_before');
        $ownerDays = (int) config('reminders.owner_days_before');

        foreach ($recipients->filter(fn (User $user) => $user->birthday) as $celebrant) {
            $birthday = $celebrant->nextBirthday($today);
            $daysLeft = (int) $today->diffInDays($birthday);
            $reference = $celebrant->id.':'.$birthday->toDateString();

            if ($ownerDays > 0 && $daysLeft <= $ownerDays
                && ! $this->alreadySent($celebrant, SentReminder::OWN_BIRTHDAY, $reference)) {
                $this->deliver($celebrant, new OwnBirthdayReminder($birthday, $daysLeft), SentReminder::OWN_BIRTHDAY, $reference);
            }

            if ($guestDays > 0 && $daysLeft <= $guestDays) {
                $recipients
                    ->reject(fn (User $user) => $user->is($celebrant))
                    ->reject(fn (User $user) => $this->alreadySent($user, SentReminder::UPCOMING_BIRTHDAY, $reference))
                    ->each(fn (User $user) => $this->deliver(
                        $user,
                        new UpcomingBirthdayReminder($celebrant, $birthday, $daysLeft),
                        SentReminder::UPCOMING_BIRTHDAY,
                        $reference,
                    ));
            }
        }
    }

    private function deliver(User $user, ReminderNotification $notification, string $type, ?string $reference = null): void
    {
        $label = $type.($reference ? " [{$reference}]" : '')." → {$user->email}";

        if ($this->option('dry-run')) {
            $this->line("  {$label}");
            $this->sent++;

            return;
        }

        try {
            $user->notify($notification);

            SentReminder::create([
                'user_id' => $user->id,
                'type' => $type,
                'reference' => $reference,
                'sent_at' => now(),
            ]);

            $this->line("  ✓ {$label}");
            $this->sent++;
        } catch (Throwable $e) {
            report($e);
            $this->error("  ✗ {$label}: {$e->getMessage()}");
            $this->failed++;
        }
    }

    /**
     * Не нагадувати про незаповнений профіль у перший день після реєстрації.
     */
    private function isPastGracePeriod(User $user): bool
    {
        return $user->created_at === null || $user->created_at->lte(now()->subDay());
    }

    private function sentRecently(User $user, string $type): bool
    {
        $repeatDays = max(1, (int) config('reminders.profile_repeat_days'));

        return SentReminder::where('user_id', $user->id)
            ->where('type', $type)
            // інтервал мінус година — запас, щоб щоденний запуск о тій самій порі не зсувався на добу
            ->where('sent_at', '>', now()->subDays($repeatDays)->addHour())
            ->exists();
    }

    private function alreadySent(User $user, string $type, string $reference): bool
    {
        return SentReminder::where('user_id', $user->id)
            ->where('type', $type)
            ->where('reference', $reference)
            ->exists();
    }
}
