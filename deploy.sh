#!/bin/bash

# 🚀 Скрипт швидкого деплою на сервер через SSH
# Використання: ./deploy.sh

echo "🚀 Galaxy Wishlist - Deploy Script"
echo "===================================="
echo ""

# Налаштування (змініть на свої дані)
SERVER_USER="your-user"
SERVER_HOST="your-server.com"
SERVER_PATH="/var/www/galaxy-wishlist"
GIT_BRANCH="main"

echo "📋 Налаштування:"
echo "   Сервер: $SERVER_USER@$SERVER_HOST"
echo "   Шлях: $SERVER_PATH"
echo "   Гілка: $GIT_BRANCH"
echo ""

# Перевірка з'єднання
echo "🔌 Перевірка з'єднання з сервером..."
if ! ssh -q "$SERVER_USER@$SERVER_HOST" exit; then
    echo "❌ Не вдалося підключитися до сервера!"
    exit 1
fi
echo "✅ З'єднання встановлено"
echo ""

# Виконання деплою через SSH
echo "📦 Початок деплою..."
ssh "$SERVER_USER@$SERVER_HOST" << 'ENDSSH'
    set -e  # Зупинити при помилці
    
    cd /var/www/galaxy-wishlist
    
    echo "🔧 Увімкнення maintenance mode..."
    php artisan down || true
    
    echo "📥 Завантаження змін з Git..."
    git fetch origin
    git reset --hard origin/main
    
    echo "📦 Оновлення Composer залежностей..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    echo "📦 Оновлення NPM залежностей..."
    npm ci --omit=dev
    
    echo "🏗️  Збірка assets..."
    npm run build
    
    echo "🗄️  Виконання міграцій..."
    php artisan migrate --force
    
    echo "🧹 Очистка та кешування конфігурації..."
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
    
    echo "🔄 Перезапуск queue workers..."
    sudo supervisorctl restart galaxy-wishlist-worker:* || true
    
    echo "🔧 Вимкнення maintenance mode..."
    php artisan up
    
    echo ""
    echo "✅ Деплой завершено успішно!"
    echo "🎉 Додаток оновлено та працює!"
ENDSSH

echo ""
echo "✅ Деплой завершено!"
echo "🌐 Перевірте сайт: https://$SERVER_HOST"
