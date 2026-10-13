<?php

namespace App\Notifications\Reminders;

use Illuminate\Notifications\Messages\MailMessage;

class MissingBirthdayReminder extends ReminderNotification
{
    public function toMail(object $notifiable): MailMessage
    {
        return $this->message()
            ->subject(__('reminders.missing_birthday.subject'))
            ->greeting(__('reminders.missing_birthday.greeting', ['name' => $notifiable->name]))
            ->line(__('reminders.missing_birthday.line1'))
            ->line(__('reminders.missing_birthday.line2'))
            ->action(__('reminders.missing_birthday.action'), route('profile.edit'));
    }
}
