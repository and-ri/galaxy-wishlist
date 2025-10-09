# Galaxy Wishlist - Швидкі команди

## Запуск проекту
```bash
php artisan serve                # Запуск dev-сервера на http://localhost:8000
npm run dev                      # Запуск Vite (якщо використовуєте)
```

## База даних
```bash
php artisan migrate              # Запустити всі міграції
php artisan migrate:fresh        # Видалити всі таблиці та створити заново
php artisan db:seed --class=AdminUserSeeder  # Створити адмін користувача
```

## Очистка кешу
```bash
php artisan cache:clear          # Очистити кеш
php artisan config:clear         # Очистити кеш конфігурації
php artisan route:clear          # Очистити кеш маршрутів
php artisan view:clear           # Очистити кеш view
php artisan optimize:clear       # Очистити все
```

## Логін дані для тестування
- URL: http://localhost:8000/admin/login
- Email: admin@example.com
- Password: password

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

## Перевірка маршрутів
```bash
php artisan route:list           # Список всіх маршрутів
```

## Перевірка міграцій
```bash
php artisan migrate:status       # Статус міграцій
```
