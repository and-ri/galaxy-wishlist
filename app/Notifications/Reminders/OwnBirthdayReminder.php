<?php

namespace App\Notifications\Reminders;

use Carbon\CarbonInterface;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Нагадування імениннику переглянути свій вішліст перед днем народження.
 */
class OwnBirthdayReminder extends ReminderNotification
{
    public function __construct(
        public CarbonInterface $birthday,
        public int $daysLeft,
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        $when = $this->when($this->daysLeft);
        $activeWishes = $notifiable->wishes()->where('is_purchased', false)->count();

        return $this->message()
            ->subject(__('reminders.own_birthday.subject', ['when' => $when]))
            ->greeting(__('reminders.own_birthday.greeting', ['name' => $notifiable->name]))
            ->line(__('reminders.own_birthday.line1', [
                'when' => $when,
                'date' => $this->date($this->birthday),
            ]))
            ->line(__('reminders.own_birthday.line2'))
            ->line($activeWishes > 0
                ? trans_choice('reminders.own_birthday.line_count', $activeWishes)
                : __('reminders.own_birthday.line_empty'))
            ->action(__('reminders.own_birthday.action'), route('wishes.index'));
    }
}
