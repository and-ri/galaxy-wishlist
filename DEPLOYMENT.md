# 🚀 Інструкція з деплою Galaxy Wishlist

## Вимоги до сервера

- PHP 8.2+
- Composer
- Node.js 18+ та npm
- MySQL 8.0+
- Nginx або Apache
- Git

## Крок 1: Підготовка сервера

### Встановлення необхідних пакетів (Ubuntu/Debian)

```bash
# Оновлення системи
sudo apt update && sudo apt upgrade -y

# PHP та розширення
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip php8.2-redis

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js та npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# MySQL
sudo apt install -y mysql-server

# Nginx
sudo apt install -y nginx
```

## Крок 2: Створення бази даних MySQL

```bash
# Вхід в MySQL
sudo mysql

# Створення бази даних та користувача
CREATE DATABASE galaxy_wishlist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'galaxy_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';
GRANT ALL PRIVILEGES ON galaxy_wishlist.* TO 'galaxy_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Крок 3: Завантаження коду на сервер

### Варіант A: Через Git (рекомендовано)

```bash
# На сервері
cd /var/www
sudo git clone https://github.com/your-username/galaxy-wishlist.git
sudo chown -R www-data:www-data galaxy-wishlist
cd galaxy-wishlist
```

### Варіант B: Через FTP/SFTP

Завантажте всі файли проєкту в `/var/www/galaxy-wishlist`

## Крок 4: Налаштування Laravel

```bash
cd /var/www/galaxy-wishlist

# Встановлення залежностей
composer install --no-dev --optimize-autoloader
npm ci --omit=dev

# Збірка assets для production
npm run build

# Копіювання та налаштування .env
cp .env.example .env
nano .env
```

### Налаштування .env файлу:

```env
APP_NAME="Galaxy Wishlist"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Europe/Kyiv
APP_URL=https://your-domain.com
APP_LOCALE=uk
APP_FALLBACK_LOCALE=uk
APP_FAKER_LOCALE=uk_UA

# MySQL Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=galaxy_wishlist
DB_USERNAME=galaxy_user
DB_PASSWORD=your_secure_password_here

# Session and Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (налаштуйте за потреби)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Authentik OAuth (налаштуйте за потреби)
AUTHENTIK_BASE_URL=https://your-authentik-url.com
AUTHENTIK_CLIENT_ID=your-client-id
AUTHENTIK_CLIENT_SECRET=your-client-secret
AUTHENTIK_REDIRECT_URI="${APP_URL}/auth/callback"

# Admin credentials
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=your-secure-admin-password
```

### Генерація ключа та міграції:

```bash
# Генерація APP_KEY
php artisan key:generate

# Створення символічного посилання для storage
php artisan storage:link

# Міграції бази даних
php artisan migrate --force

# Створення admin користувача
php artisan db:seed --class=AdminUserSeeder

# Оптимізація для production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Крок 5: Налаштування прав доступу

```bash
cd /var/www/galaxy-wishlist

# Права для storage та bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Права для всього проєкту
sudo chown -R www-data:www-data /var/www/galaxy-wishlist
sudo find /var/www/galaxy-wishlist -type f -exec chmod 644 {} \;
sudo find /var/www/galaxy-wishlist -type d -exec chmod 755 {} \;
```

## Крок 6: Налаштування Nginx

Створіть конфігураційний файл:

```bash
sudo nano /etc/nginx/sites-available/galaxy-wishlist
```

Вставте конфігурацію:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    
    # Редірект на HTTPS (після налаштування SSL)
    # return 301 https://$server_name$request_uri;
    
    root /var/www/galaxy-wishlist/public;
    index index.php index.html;

    # Логи
    access_log /var/log/nginx/galaxy-wishlist-access.log;
    error_log /var/log/nginx/galaxy-wishlist-error.log;

    # Обмеження розміру завантажуваних файлів
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Активуйте конфігурацію:

```bash
sudo ln -s /etc/nginx/sites-available/galaxy-wishlist /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## Крок 7: Налаштування SSL (Let's Encrypt)

```bash
# Встановлення Certbot
sudo apt install -y certbot python3-certbot-nginx

# Отримання SSL сертифіката
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Автоматичне оновлення сертифіката
sudo certbot renew --dry-run
```

## Крок 8: Налаштування Supervisor для Queue

Створіть конфігурацію:

```bash
sudo nano /etc/supervisor/conf.d/galaxy-wishlist-worker.conf
```

Вставте:

```ini
[program:galaxy-wishlist-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/galaxy-wishlist/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/galaxy-wishlist/storage/logs/worker.log
stopwaitsecs=3600
```

Запустіть worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start galaxy-wishlist-worker:*
```

## Крок 9: Налаштування Cron для планувальника

```bash
sudo crontab -e -u www-data
```

Додайте рядок:

```cron
* * * * * cd /var/www/galaxy-wishlist && php artisan schedule:run >> /dev/null 2>&1
```

## Крок 10: Перевірка

1. Відкрийте браузер і перейдіть на https://your-domain.com
2. Перевірте логін адміна: https://your-domain.com/admin/login
3. Перевірте завантаження зображень
4. Перевірте функцію автозаповнення з URL

## 🔄 Оновлення додатку

Для оновлення додатку виконайте:

```bash
cd /var/www/galaxy-wishlist

# Вимкнути maintenance mode
php artisan down

# Отримати нові зміни
git pull origin main

# Оновити залежності
composer install --no-dev --optimize-autoloader
npm ci --omit=dev
npm run build

# Виконати міграції
php artisan migrate --force

# Очистити та перекешувати
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Перезапустити queue workers
sudo supervisorctl restart galaxy-wishlist-worker:*

# Увімкнути додаток
php artisan up
```

## 🔐 Безпека

### Додаткові рекомендації:

1. **Firewall**:
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

2. **Fail2Ban для захисту від брутфорсу**:
```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
```

3. **Регулярні бекапи бази даних**:
```bash
# Створіть скрипт бекапу
sudo nano /usr/local/bin/backup-galaxy-db.sh
```

Вміст скрипту:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/galaxy-wishlist"
mkdir -p $BACKUP_DIR

mysqldump -u galaxy_user -p'your_password' galaxy_wishlist | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Видалення старих бекапів (старше 7 днів)
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete
```

Зробіть скрипт виконуваним та додайте в cron:
```bash
sudo chmod +x /usr/local/bin/backup-galaxy-db.sh
sudo crontab -e
# Додайте: 0 2 * * * /usr/local/bin/backup-galaxy-db.sh
```

## 🐛 Відлагодження

### Перегляд логів:

```bash
# Laravel логи
tail -f /var/www/galaxy-wishlist/storage/logs/laravel.log

# Nginx логи
tail -f /var/log/nginx/galaxy-wishlist-error.log

# PHP-FPM логи
tail -f /var/log/php8.2-fpm.log

# Queue worker логи
tail -f /var/www/galaxy-wishlist/storage/logs/worker.log
```

### Очистка кешу:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

## 📊 Моніторинг

Рекомендовані інструменти:
- **Laravel Telescope** (для development)
- **New Relic** або **Blackfire** (для production)
- **Sentry** (для відстеження помилок)

## 🎉 Готово!

Ваш додаток Galaxy Wishlist тепер працює на production сервері з MySQL!
