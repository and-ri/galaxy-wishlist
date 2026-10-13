<?php

namespace App\Notifications\Reminders;

use Carbon\CarbonInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class ReminderNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    abstract public function toMail(object $notifiable): MailMessage;

    /**
     * Лист із підписом застосунку.
     */
    protected function message(): MailMessage
    {
        return (new MailMessage)
            ->salutation(__('reminders.signature', ['app' => config('app.name')]));
    }

    /**
     * «сьогодні», «завтра» або «через N днів».
     */
    protected function when(int $days): string
    {
        return match ($days) {
            0 => __('reminders.today'),
            1 => __('reminders.tomorrow'),
            default => trans_choice('reminders.in_days', $days),
        };
    }

    /**
     * Дата у форматі «20 жовтня».
     */
    protected function date(CarbonInterface $date): string
    {
        return $date->copy()->locale(app()->getLocale())->translatedFormat('j F');
    }
}
