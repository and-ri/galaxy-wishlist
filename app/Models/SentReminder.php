<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentReminder extends Model
{
    public const MISSING_BIRTHDAY = 'missing_birthday';

    public const EMPTY_WISHLIST = 'empty_wishlist';

    public const UPCOMING_BIRTHDAY = 'upcoming_birthday';

    public const OWN_BIRTHDAY = 'own_birthday';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'reference',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the user (recipient) of the reminder.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
