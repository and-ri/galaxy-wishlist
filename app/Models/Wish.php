<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wish extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'url',
        'price',
        'currency',
        'is_purchased',
        'priority',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'is_purchased' => 'boolean',
            'price' => 'decimal:2',
            'priority' => 'integer',
        ];
    }

    /**
     * Get the user that owns the wish.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
