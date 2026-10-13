<?php

namespace App\Notifications\Reminders;

use Illuminate\Notifications\Messages\MailMessage;

class EmptyWishlistReminder extends ReminderNotification
{
    public function toMail(object $notifiable): MailMessage
    {
        return $this->message()
            ->subject(__('reminders.empty_wishlist.subject'))
            ->greeting(__('reminders.empty_wishlist.greeting', ['name' => $notifiable->name]))
            ->line(__('reminders.empty_wishlist.line1'))
            ->line(__('reminders.empty_wishlist.line2'))
            ->action(__('reminders.empty_wishlist.action'), route('wishes.create'));
    }
}
