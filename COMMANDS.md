# 📝 Galaxy Wishlist - Швидка довідка команд

## 🚀 Development

### Запуск локального сервера
```bash
# Повний запуск (сервер + queue + logs + vite)
composer run dev

# Або окремо
php artisan serve           # Тільки сервер
npm run dev                 # Тільки Vite
php artisan queue:work      # Тільки queue
php artisan pail            # Тільки логи
```

### База даних
```bash
php artisan migrate              # Запустити всі міграції
php artisan migrate:fresh        # Видалити всі таблиці та створити заново
```

## Очистка кешу
```bash
php artisan cache:clear          # Очистити кеш
php artisan config:clear         # Очистити кеш конфігурації
php artisan route:clear          # Очистити кеш маршрутів
php artisan view:clear           # Очистити кеш view
php artisan optimize:clear       # Очистити все
```

## Додати нового користувача (через tinker)
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Тестовий Користувач',
    'email' => 'test@example.com',
    'password' => Hash::make('password'),
]);
```

## E-mail нагадування
Команда `reminders:send` запускається планувальником щодня о `REMINDERS_SEND_AT`
(на сервері потрібен cron з `php artisan schedule:run`, див. DEPLOYMENT.md, крок 9).

| Нагадування | Кому | Коли |
|---|---|---|
| Вказати дату народження | хто не вказав | раз на `REMINDERS_PROFILE_REPEAT_DAYS` днів |
| Заповнити вішліст | хто не додав жодного бажання | раз на `REMINDERS_PROFILE_REPEAT_DAYS` днів |
| Про чужий день народження | всім, крім іменинника | за `REMINDERS_BIRTHDAY_DAYS_BEFORE` днів |
| Переглянути свій вішліст | імениннику | за `REMINDERS_OWNER_DAYS_BEFORE` днів |

Адміни (`is_admin`) нагадувань не отримують. Користувачам, зареєстрованим менше доби тому, нагадування про профіль не надсилаються.
Надіслані нагадування зберігаються в таблиці `sent_reminders`, тому повторний запуск не створює дублікатів.

```bash
php artisan reminders:send --dry-run   # Показати, кому що буде надіслано
php artisan reminders:send             # Надіслати зараз
php artisan schedule:list              # Перевірити розклад
```

## Перевірка маршрутів
```bash
php artisan route:list           # Список всіх маршрутів
```

## Перевірка міграцій
```bash
php artisan migrate:status       # Статус міграцій
```
