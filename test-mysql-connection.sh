#!/bin/bash

# 🔍 Скрипт перевірки з'єднання з MySQL
# Використовуйте перед деплоєм для перевірки налаштувань БД

echo "🔍 Galaxy Wishlist - MySQL Connection Test"
echo "==========================================="
echo ""

# Перевірка наявності .env файлу
if [ ! -f .env ]; then
    echo "❌ Файл .env не знайдено!"
    echo "💡 Створіть .env файл на основі .env.production.example"
    exit 1
fi

# Завантаження змінних з .env
source .env

# Перевірка що використовується MySQL
if [ "$DB_CONNECTION" != "mysql" ]; then
    echo "⚠️  Увага: DB_CONNECTION не встановлено на 'mysql'"
    echo "   Поточне значення: $DB_CONNECTION"
    echo ""
fi

# Виведення налаштувань
echo "📋 Налаштування бази даних:"
echo "   Connection: $DB_CONNECTION"
echo "   Host: $DB_HOST"
echo "   Port: $DB_PORT"
echo "   Database: $DB_DATABASE"
echo "   Username: $DB_USERNAME"
echo "   Password: ${DB_PASSWORD:0:3}***"
echo ""

# Перевірка чи встановлено mysql client
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL client не встановлено!"
    echo "💡 Встановіть: sudo apt install mysql-client"
    exit 1
fi

# Спроба підключення
echo "🔌 Спроба підключення до MySQL..."
if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" &> /dev/null; then
    echo "✅ Підключення успішне!"
    echo ""
    
    # Перевірка чи існує база даних
    echo "🗄️  Перевірка бази даних '$DB_DATABASE'..."
    DB_EXISTS=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW DATABASES LIKE '$DB_DATABASE';" 2>/dev/null | grep "$DB_DATABASE")
    
    if [ -n "$DB_EXISTS" ]; then
        echo "✅ База даних існує"
        echo ""
        
        # Перевірка таблиць
        echo "📊 Таблиці в базі даних:"
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SHOW TABLES;" 2>/dev/null
        echo ""
        
        # Статистика
        echo "📈 Статистика:"
        TABLES_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SHOW TABLES;" 2>/dev/null | wc -l)
        echo "   Кількість таблиць: $((TABLES_COUNT - 1))"
        
        if [ $((TABLES_COUNT - 1)) -gt 0 ]; then
            USERS_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SELECT COUNT(*) as count FROM users;" 2>/dev/null | tail -n 1)
            echo "   Користувачів: $USERS_COUNT"
            
            WISHES_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SELECT COUNT(*) as count FROM wishes;" 2>/dev/null | tail -n 1)
            echo "   Бажань: $WISHES_COUNT"
        fi
    else
        echo "⚠️  База даних '$DB_DATABASE' не існує"
        echo "💡 Створіть базу даних:"
        echo "   CREATE DATABASE $DB_DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    fi
    
    echo ""
    echo "✅ Всі перевірки пройдено успішно!"
    echo "🚀 Можете продовжувати деплой"
    
else
    echo "❌ Не вдалося підключитися до MySQL!"
    echo ""
    echo "🔍 Можливі причини:"
    echo "   1. Неправильний хост або порт"
    echo "   2. Неправильні credentials (username/password)"
    echo "   3. MySQL сервер не запущено"
    echo "   4. Firewall блокує з'єднання"
    echo "   5. Користувач не має прав для підключення з цього хосту"
    echo ""
    echo "💡 Перевірте налаштування в .env файлі"
    exit 1
fi
