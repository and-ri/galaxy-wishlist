#!/bin/bash

# 🚀 Скрипт швидкого деплою на сервер через SSH
# Використання: ./deploy.sh

echo "🔧 Увімкнення maintenance mode..."
php artisan down || true

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

echo "🧹 Очистка та кешування конфігурації..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "🔧 Вимкнення maintenance mode..."
php artisan up

echo ""
echo "✅ Деплой завершено успішно!"
echo "🎉 Додаток оновлено та працює!"