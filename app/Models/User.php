<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'is_admin',
        'birthday',
        'delivery_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'birthday' => 'date',
        ];
    }

    /**
     * Повний URL аватара.
     *
     * Аватар може бути або зовнішнім посиланням (Google), або шляхом
     * у storage/public (завантажений користувачем файл).
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->avatar) {
                return null;
            }

            if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
                return $this->avatar;
            }

            return asset('storage/'.$this->avatar);
        });
    }

    /**
     * Чи є аватар зовнішнім посиланням (не файлом у storage).
     */
    public function hasExternalAvatar(): bool
    {
        return $this->avatar && Str::startsWith($this->avatar, ['http://', 'https://']);
    }

    /**
     * Get the wishes for the user.
     */
    public function wishes()
    {
        return $this->hasMany(Wish::class);
    }
}
