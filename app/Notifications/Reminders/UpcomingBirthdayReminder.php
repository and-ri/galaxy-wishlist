<?php

namespace App\Notifications\Reminders;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Нагадування іншим користувачам про день народження іменинника.
 */
class UpcomingBirthdayReminder extends ReminderNotification
{
    public function __construct(
        public User $celebrant,
        public CarbonInterface $birthday,
        public int $daysLeft,
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        $when = $this->when($this->daysLeft);
        $activeWishes = $this->celebrant->wishes()->where('is_purchased', false)->count();

        return $this->message()
            ->subject(__('reminders.upcoming_birthday.subject', [
                'celebrant' => $this->celebrant->name,
                'when' => $when,
            ]))
            ->greeting(__('reminders.upcoming_birthday.greeting', ['name' => $notifiable->name]))
            ->line(__('reminders.upcoming_birthday.line1', [
                'celebrant' => $this->celebrant->name,
                'when' => $when,
                'date' => $this->date($this->birthday),
            ]))
            ->line($activeWishes > 0
                ? trans_choice('reminders.upcoming_birthday.line_wishes', $activeWishes)
                : __('reminders.upcoming_birthday.line_no_wishes'))
            ->action(__('reminders.upcoming_birthday.action'), route('users.show', $this->celebrant));
    }
}
