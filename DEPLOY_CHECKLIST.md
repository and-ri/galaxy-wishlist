# ✅ Чеклист деплою Galaxy Wishlist

## Перед деплоєм (на локальній машині)

- [ ] Всі зміни закомічено в Git
- [ ] Код пройшов тестування локально
- [ ] Створено production build: `npm run build`
- [ ] Оновлено документацію (якщо потрібно)
- [ ] Перевірено `.env.production.example`

## На сервері - Перше встановлення

### 1. Підготовка сервера
- [ ] Встановлено PHP 8.2+
- [ ] Встановлено Composer
- [ ] Встановлено Node.js 18+
- [ ] Встановлено MySQL 8.0+
- [ ] Встановлено Nginx
- [ ] Налаштовано firewall (порти 22, 80, 443)

### 2. База даних
- [ ] Створено базу даних MySQL
- [ ] Створено користувача БД з правами
- [ ] Записано credentials для .env

### 3. Код додатку
- [ ] Склоновано репозиторій в `/var/www/galaxy-wishlist`
- [ ] Виконано `composer install --no-dev --optimize-autoloader`
- [ ] Виконано `npm ci --omit=dev`
- [ ] Виконано `npm run build`

### 4. Налаштування Laravel
- [ ] Скопійовано `.env.production.example` в `.env`
- [ ] Налаштовано всі змінні в `.env`
- [ ] Виконано `php artisan key:generate`
- [ ] Виконано `php artisan storage:link`
- [ ] Виконано `php artisan migrate --force`
- [ ] Виконано `php artisan db:seed --class=AdminUserSeeder`
- [ ] Виконано `php artisan optimize`

### 5. Права доступу
- [ ] Встановлено права: `chown -R www-data:www-data`
- [ ] Встановлено права для storage: `chmod -R 775 storage`
- [ ] Встановлено права для bootstrap/cache: `chmod -R 775 bootstrap/cache`

### 6. Web сервер (Nginx)
- [ ] Створено конфігурацію в `/etc/nginx/sites-available/`
- [ ] Створено symlink в `/etc/nginx/sites-enabled/`
- [ ] Протестовано конфігурацію: `nginx -t`
- [ ] Перезапущено Nginx: `systemctl restart nginx`

### 7. SSL сертифікат
- [ ] Встановлено Certbot
- [ ] Отримано SSL сертифікат Let's Encrypt
- [ ] Налаштовано автоматичне оновлення

### 8. Queue Worker
- [ ] Створено конфігурацію Supervisor
- [ ] Запущено worker: `supervisorctl start galaxy-wishlist-worker:*`
- [ ] Перевірено статус: `supervisorctl status`

### 9. Cron Jobs
- [ ] Додано Laravel scheduler в crontab для www-data

### 10. Перевірка
- [ ] Відкривається головна сторінка
- [ ] Працює логін користувача
- [ ] Працює логін адміна
- [ ] Працює створення бажання
- [ ] Працює завантаження зображень
- [ ] Працює автозаповнення з URL
- [ ] Перевірено логи на помилки

## Оновлення (Deploy)

### Автоматичний деплой
- [ ] Оновлено `deploy.sh` з credentials сервера
- [ ] Виконано `./deploy.sh`

### Ручний деплой
- [ ] Підключено до сервера: `ssh user@server`
- [ ] Перейшов в директорію: `cd /var/www/galaxy-wishlist`
- [ ] Увімкнено maintenance: `php artisan down`
- [ ] Оновлено код: `git pull origin main`
- [ ] Оновлено залежності: `composer install --no-dev`
- [ ] Оновлено assets: `npm ci && npm run build`
- [ ] Виконано міграції: `php artisan migrate --force`
- [ ] Очищено кеш: `php artisan optimize:clear`
- [ ] Кешовано конфігурацію: `php artisan optimize`
- [ ] Перезапущено workers: `supervisorctl restart galaxy-wishlist-worker:*`
- [ ] Вимкнено maintenance: `php artisan up`
- [ ] Перевірено сайт в браузері

## Безпека

- [ ] APP_DEBUG=false в production
- [ ] APP_ENV=production
- [ ] Складний APP_KEY
- [ ] Складні паролі для БД та адміна
- [ ] SSL сертифікат активний
- [ ] Firewall налаштований
- [ ] Fail2Ban встановлений (опціонально)
- [ ] Регулярні бекапи БД налаштовані

## Моніторинг

- [ ] Налаштовано логування помилок
- [ ] Налаштовано моніторинг uptime
- [ ] Налаштовано сповіщення про помилки (опціонально)
- [ ] Перевірено розмір логів

## Після деплою

- [ ] Протестовано всі основні функції
- [ ] Перевірено мобільну версію
- [ ] Перевірено швидкість завантаження
- [ ] Перевірено логи на помилки
- [ ] Сповістити команду про оновлення

## У разі проблем

### Rollback
```bash
cd /var/www/galaxy-wishlist
php artisan down
git reset --hard HEAD~1
composer install --no-dev
npm ci && npm run build
php artisan migrate:rollback
php artisan optimize
php artisan up
```

### Перегляд логів
```bash
# Laravel
tail -f storage/logs/laravel.log

# Nginx
tail -f /var/log/nginx/galaxy-wishlist-error.log

# Queue worker
tail -f storage/logs/worker.log
```

### Очистка всього кешу
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Контакти підтримки

- **Репозиторій**: https://github.com/your-username/galaxy-wishlist
- **Документація**: DEPLOYMENT.md
- **Email**: admin@your-domain.com
