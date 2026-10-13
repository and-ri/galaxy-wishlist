<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Ціна для показу: «1 299 ₴», «49,90 $».
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->price === null) {
                return null;
            }

            $price = (float) $this->price;
            $decimals = floor($price) == $price ? 0 : 2;
            $symbol = ['UAH' => '₴', 'USD' => '$', 'EUR' => '€'][$this->currency] ?? $this->currency;

            return number_format($price, $decimals, ',', "\u{00A0}")."\u{00A0}".$symbol;
        });
    }

    /**
     * Get the user that owns the wish.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
