#!/bin/bash

# 🔄 Скрипт міграції даних з SQLite на MySQL
# Використовуйте цей скрипт якщо потрібно перенести існуючі дані з локальної SQLite на production MySQL

echo "🚀 Galaxy Wishlist - Міграція з SQLite на MySQL"
echo "================================================"
echo ""

# Перевірка наявності .env файлу
if [ ! -f .env ]; then
    echo "❌ Файл .env не знайдено!"
    exit 1
fi

# Бекап поточної SQLite бази
echo "📦 Створення бекапу SQLite бази даних..."
cp database/database.sqlite database/database.sqlite.backup
echo "✅ Бекап створено: database/database.sqlite.backup"
echo ""

# Експорт даних з SQLite
echo "📤 Експорт даних з SQLite..."
php artisan db:seed --class=DatabaseSeeder --database=sqlite

# Запит підтвердження
echo ""
echo "⚠️  УВАГА: Наступний крок видалить всі дані з MySQL бази!"
read -p "Продовжити? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "❌ Операцію скасовано"
    exit 0
fi

# Очистка MySQL бази
echo ""
echo "🗑️  Очистка MySQL бази даних..."
php artisan migrate:fresh --database=mysql --force

# Дамп даних з SQLite
echo ""
echo "💾 Експорт користувачів..."
sqlite3 database/database.sqlite ".mode insert users" "SELECT * FROM users;" > /tmp/users.sql

echo "💾 Експорт бажань..."
sqlite3 database/database.sqlite ".mode insert wishes" "SELECT * FROM wishes;" > /tmp/wishes.sql

# Імпорт даних в MySQL
echo ""
echo "📥 Імпорт даних в MySQL..."

# Отримуємо дані для підключення з .env
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

# Імпортуємо дані
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < /tmp/users.sql
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < /tmp/wishes.sql

# Очистка тимчасових файлів
rm /tmp/users.sql /tmp/wishes.sql

echo ""
echo "✅ Міграція завершена!"
echo ""
echo "📋 Наступні кроки:"
echo "1. Перевірте дані в MySQL базі"
echo "2. Скопіюйте папку storage/app/public на сервер"
echo "3. Оновіть .env файл на сервері (DB_CONNECTION=mysql)"
echo "4. Виконайте: php artisan storage:link"
echo "5. Очистіть кеш: php artisan optimize:clear"
echo ""
echo "🎉 Готово!"
