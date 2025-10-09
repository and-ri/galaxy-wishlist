# 📂 Структура документації Galaxy Wishlist

## 🎯 Для початку роботи

Якщо ви тільки почали працювати з проектом:

1. 📖 **[README.md](README.md)** - Загальний опис проекту та швидкий старт
2. 🚀 **[READY_TO_DEPLOY.md](READY_TO_DEPLOY.md)** - Огляд готовності до деплою

## 🚀 Деплой на production

### Основна документація
- 📘 **[DEPLOYMENT.md](DEPLOYMENT.md)** - Повна покрокова інструкція з деплою
- ✅ **[DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md)** - Чеклист для перевірки

### Автоматизація
- 🤖 **[.github/workflows/deploy.yml](.github/workflows/deploy.yml)** - GitHub Actions workflow
- 🔐 **[.github/SECRETS.md](.github/SECRETS.md)** - Налаштування GitHub Secrets

### Скрипти
- 🚀 **[deploy.sh](deploy.sh)** - Скрипт автоматичного деплою через SSH
- 🔄 **[migrate-to-mysql.sh](migrate-to-mysql.sh)** - Міграція з SQLite на MySQL
- 🔍 **[test-mysql-connection.sh](test-mysql-connection.sh)** - Перевірка з'єднання з MySQL

### Конфігурація
- ⚙️ **[.env.production.example](.env.production.example)** - Приклад .env для production

## 📝 Довідкова інформація

- 💻 **[COMMANDS.md](COMMANDS.md)** - Швидка довідка всіх команд
- 📦 **[SETUP_COMPLETE.md](SETUP_COMPLETE.md)** - Історія початкового налаштування
- 🔧 **[UPDATES.md](UPDATES.md)** - Журнал оновлень проекту

## 🐛 Виправлення помилок

- 🔧 **[BUGFIX.md](BUGFIX.md)** - Виправлені баги
- 🌍 **[LOCALE_FIX.md](LOCALE_FIX.md)** - Виправлення локалізації
- ⬆️ **[UPGRADE_COMPLETE.md](UPGRADE_COMPLETE.md)** - Завершені оновлення

## 🗺️ Швидка навігація

### Потрібно деплоїти перший раз?
1. Прочитайте [READY_TO_DEPLOY.md](READY_TO_DEPLOY.md)
2. Виконайте [DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md)
3. Дотримуйтесь [DEPLOYMENT.md](DEPLOYMENT.md)

### Потрібно оновити існуючий проект?
1. Запустіть `./deploy.sh`
2. Або налаштуйте [GitHub Actions](.github/workflows/deploy.yml)

### Шукаєте команду?
Відкрийте [COMMANDS.md](COMMANDS.md)

### Хочете перейти на MySQL?
1. Прочитайте розділ "База даних" в [DEPLOYMENT.md](DEPLOYMENT.md)
2. Використайте `./migrate-to-mysql.sh` (опціонально)
3. Перевірте з'єднання: `./test-mysql-connection.sh`

### Щось не працює?
1. Перевірте [BUGFIX.md](BUGFIX.md) - можливо ваша проблема вже вирішена
2. Перегляньте логи (інструкції в [COMMANDS.md](COMMANDS.md))
3. Використайте rollback процедуру з [DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md)

## 📊 Структура проекту

```
galaxy-wishlist/
├── 📄 Документація (ви тут)
│   ├── README.md                    # Головний README
│   ├── READY_TO_DEPLOY.md          # Огляд готовності
│   ├── DEPLOYMENT.md               # Інструкція деплою
│   ├── DEPLOY_CHECKLIST.md         # Чеклист
│   ├── COMMANDS.md                 # Швидкі команди
│   └── .github/
│       ├── workflows/deploy.yml    # GitHub Actions
│       └── SECRETS.md              # Налаштування secrets
│
├── 🔧 Скрипти
│   ├── deploy.sh                   # Автоматичний деплой
│   ├── migrate-to-mysql.sh        # Міграція на MySQL
│   └── test-mysql-connection.sh   # Тест MySQL
│
├── 📁 Додаток
│   ├── app/                        # Laravel додаток
│   ├── resources/                  # Views, CSS, JS
│   ├── database/                   # Міграції, seeders
│   ├── routes/                     # Маршрути
│   └── config/                     # Конфігурація
│
└── ⚙️ Конфігурація
    ├── .env.example                # Приклад для dev
    ├── .env.production.example     # Приклад для production
    ├── composer.json               # PHP залежності
    ├── package.json                # NPM залежності
    └── vite.config.js             # Vite конфігурація
```

## 🎯 Популярні задачі

### Локальна розробка
```bash
composer run dev                    # Запустити все
php artisan migrate                 # Міграції
php artisan tinker                  # Laravel REPL
```

### Деплой
```bash
./deploy.sh                        # Автоматичний деплой
# Або push в main для GitHub Actions
```

### Тестування MySQL
```bash
./test-mysql-connection.sh         # Перевірити з'єднання
./migrate-to-mysql.sh              # Мігрувати дані
```

### Проблеми
```bash
php artisan optimize:clear         # Очистити кеш
tail -f storage/logs/laravel.log   # Дивитись логи
```

## 📞 Підтримка

- 📖 **Документація Laravel**: https://laravel.com/docs
- 🎨 **Tailwind CSS**: https://tailwindcss.com/docs
- 🔐 **Authentik**: https://goauthentik.io/docs

---

**Примітка**: Цей файл створено для швидкої навігації по документації проекту. Для детальної інформації відкрийте відповідні файли.
