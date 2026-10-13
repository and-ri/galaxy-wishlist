<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
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

    /**
     * Найближчий день народження, починаючи з $from (включно).
     *
     * Для народжених 29 лютого в невисокосні роки повертає 28 лютого.
     */
    public function nextBirthday(CarbonInterface $from): ?Carbon
    {
        if (! $this->birthday) {
            return null;
        }

        $from = Carbon::instance($from)->startOfDay();
        $next = $this->birthdayInYear($from->year);

        return $next->lt($from) ? $this->birthdayInYear($from->year + 1) : $next;
    }

    private function birthdayInYear(int $year): Carbon
    {
        $month = $this->birthday->month;
        $day = $this->birthday->day;

        if ($month === 2 && $day === 29 && ! Carbon::create($year)->isLeapYear()) {
            $day = 28;
        }

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    /**
     * Користувачі, яким надсилаються нагадування (без службових адмінів).
     */
    public function scopeReminderRecipients(Builder $query): void
    {
        $query->where('is_admin', false)->whereNotNull('email');
    }
}
