#!/bin/bash

# 🚀 Скрипт деплою на сервер
# Використання: bash deploy.sh
# (через bash, а не ./deploy.sh — так не залежимо від прав на виконання файлу)

set -euo pipefail

cd "$(dirname "$0")"

# Якщо будь-який крок упав — повертаємо сайт з maintenance mode і завершуємось з помилкою,
# щоб у лозі панелі було видно, що деплой не вдався
trap 'echo "❌ Деплой зупинено через помилку (рядок $LINENO)"; php artisan up || true; exit 1' ERR

echo "🔧 Увімкнення maintenance mode..."
php artisan down --retry=15 || true

echo "📦 Оновлення Composer залежностей..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📦 Оновлення NPM залежностей..."
npm ci

echo "🏗️  Збірка assets..."
npm run build

echo "🗄️  Виконання міграцій..."
php artisan migrate --force

echo "🔗 Створення симлінка storage..."
php artisan storage:link --force

echo "🧹 Очистка та кешування конфігурації, маршрутів і шаблонів..."
php artisan optimize:clear
php artisan optimize

echo "🔁 Перезапуск queue-воркерів..."
php artisan queue:restart

# Якщо скрипт запущено від root (панель), файли кешу й логів мають належати веб-користувачу,
# інакше PHP-FPM не зможе в них писати і сайт віддасть 500
WEB_USER="${WEB_USER:-www}"
if [ "$(id -u)" -eq 0 ] && id "$WEB_USER" >/dev/null 2>&1; then
    echo "👤 Права на storage і bootstrap/cache для $WEB_USER..."
    chown -R "$WEB_USER":"$WEB_USER" storage bootstrap/cache public/build
fi

echo "🔧 Вимкнення maintenance mode..."
php artisan up

echo ""
echo "✅ Деплой завершено успішно!"
