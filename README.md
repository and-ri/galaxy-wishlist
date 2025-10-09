# 🌌 Galaxy Wishlist

Веб-додаток для створення та обміну списками бажань з можливістю SSO авторизації через Authentik.

## ✨ Функціонал

- 🔐 SSO авторизація через Authentik (goauthentik.io)
- 🎁 Створення та управління власними бажаннями
- 🖼️ Завантаження зображень для бажань
- 🔗 Автозаповнення даних товару з URL (Open Graph)
- 👤 Завантаження аватарів користувачів
- 🎂 Профіль з датою народження та адресою доставки
- � Перегляд бажань інших користувачів
- 📱 Повністю адаптивний дизайн (mobile-friendly)
- � Сучасний дизайн з Tailwind CSS 4.0
- �🇺🇦 Українська локалізація
- 🔒 Локальний вхід для адміністратора

## 🚀 Швидкий старт (Development)

### Вимоги

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite (для розробки) або MySQL (для production)

### Встановлення

```bash
# Клонування репозиторію
git clone <ваш-репозиторій>
cd galaxy-wishlist

# Встановлення залежностей
composer install
npm install

# Налаштування оточення
cp .env.example .env
php artisan key:generate

# База даних
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=AdminUserSeeder

# Storage
php artisan storage:link

# Запуск
composer run dev
```

Відкрийте браузер: `http://localhost:8000`

## 📦 Production Deployment

Детальна інструкція з деплою на production сервер з MySQL знаходиться в [DEPLOYMENT.md](DEPLOYMENT.md).

### Швидкий деплой

1. Налаштуйте сервер згідно з [DEPLOYMENT.md](DEPLOYMENT.md)
2. Відредагуйте `deploy.sh` з вашими credentials
3. Виконайте: `./deploy.sh`

### Чеклист деплою

Використовуйте [DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md) для перевірки всіх кроків.

## 🔑 Локальний вхід (для розробки)

Для локального тестування без Authentik:

- URL: `/admin/login`
- Email: `admin@example.com`
- Password: `password`

## 🔧 Налаштування Authentik

### 1. Створіть Provider в Authentik

1. Перейдіть в Admin Interface → Applications → Providers
2. Створіть новий **OAuth2/OpenID Provider**
3. Налаштування:
   - **Name**: Galaxy Wishlist
   - **Authorization flow**: default-provider-authorization-implicit-consent
   - **Client type**: Confidential
   - **Redirect URIs**: `http://localhost:8000/auth/authentik/callback`
   - **Signing Key**: виберіть будь-який сертифікат

4. Збережіть та скопіюйте:
   - Client ID
   - Client Secret

### 2. Створіть Application

1. Applications → Create
2. Налаштування:
   - **Name**: Galaxy Wishlist
   - **Slug**: galaxy-wishlist
   - **Provider**: виберіть створений Provider
   - **Launch URL**: `http://localhost:8000`

### 3. Оновіть .env

Додайте отримані дані в `.env`:

```env
AUTHENTIK_BASE_URL=https://your-authentik-domain.com
AUTHENTIK_CLIENT_ID=<client_id_from_provider>
AUTHENTIK_CLIENT_SECRET=<client_secret_from_provider>
AUTHENTIK_REDIRECT_URI=http://localhost:8000/auth/authentik/callback
```

## 📁 Структура проекту

```
app/
├── Http/Controllers/
│   ├── AuthController.php       # Авторизація
│   ├── WishController.php       # CRUD для бажань
│   ├── ProfileController.php    # Профіль користувача
│   └── UserController.php       # Список користувачів
├── Models/
│   ├── User.php                 # Модель користувача
│   └── Wish.php                 # Модель бажання
├── Policies/
│   └── WishPolicy.php           # Політики доступу
└── Providers/
    ├── AppServiceProvider.php   # Реєстрація Authentik provider
    └── AuthentikProvider.php    # Custom Socialite provider

resources/
├── views/
│   ├── auth/                    # Сторінки авторизації
│   ├── wishes/                  # CRUD сторінки бажань
│   ├── users/                   # Сторінки користувачів
│   ├── profile/                 # Редагування профілю
│   └── layouts/app.blade.php    # Основний layout
└── lang/uk/                     # Українська локалізація

database/
├── migrations/                  # Міграції БД
└── seeders/
    └── AdminUserSeeder.php      # Створення адмін користувача
```

## 🛠️ Розробка

### Створення нового користувача

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Ім\'я',
    'email' => 'email@example.com',
    'password' => Hash::make('password'),
    'is_admin' => false,
]);
```

### Очистка кешу

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📝 Технології

- **Backend**: Laravel 11
- **Frontend**: Blade Templates, Vanilla CSS
- **Database**: SQLite (за замовчуванням)
- **Auth**: Laravel Socialite + Custom Authentik Provider
- **Localization**: Українська мова

## 🤝 Внесок

Якщо ви знайшли помилку або маєте пропозицію - створіть Issue або Pull Request!

## 📄 Ліцензія

MIT License


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
