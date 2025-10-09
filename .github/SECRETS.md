# 🔐 GitHub Secrets Configuration

Для автоматичного деплою через GitHub Actions потрібно налаштувати наступні secrets в репозиторії.

## Як додати Secrets

1. Перейдіть в репозиторій на GitHub
2. Клікніть на **Settings** → **Secrets and variables** → **Actions**
3. Клікніть **New repository secret**
4. Додайте кожен з наступних secrets

## Обов'язкові Secrets

### SSH_HOST
- **Опис**: IP адреса або домен вашого сервера
- **Приклад**: `123.456.789.0` або `your-server.com`

### SSH_USERNAME
- **Опис**: Ім'я користувача для SSH підключення
- **Приклад**: `ubuntu` або `root`

### SSH_PRIVATE_KEY
- **Опис**: Приватний SSH ключ для підключення до сервера
- **Як отримати**:
  ```bash
  # На локальній машині (якщо ключ ще не створено)
  ssh-keygen -t ed25519 -C "github-actions"
  
  # Виведіть приватний ключ
  cat ~/.ssh/id_ed25519
  
  # Скопіюйте публічний ключ на сервер
  ssh-copy-id -i ~/.ssh/id_ed25519.pub user@your-server.com
  ```
- **Формат**: Весь вміст файлу `~/.ssh/id_ed25519` включаючи:
  ```
  -----BEGIN OPENSSH PRIVATE KEY-----
  ...
  -----END OPENSSH PRIVATE KEY-----
  ```

## Опціональні Secrets

### SSH_PORT
- **Опис**: SSH порт (якщо відрізняється від 22)
- **За замовчуванням**: `22`
- **Приклад**: `2222`

### TELEGRAM_BOT_TOKEN
- **Опис**: Telegram bot token для сповіщень про помилки
- **Як отримати**: 
  1. Знайдіть @BotFather в Telegram
  2. Створіть нового бота командою `/newbot`
  3. Скопіюйте token
- **Приклад**: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`

### TELEGRAM_CHAT_ID
- **Опис**: Telegram chat ID для отримання сповіщень
- **Як отримати**:
  1. Напишіть повідомлення боту
  2. Відкрийте: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
  3. Знайдіть `"chat":{"id":`
- **Приклад**: `123456789`

## Налаштування на сервері

### 1. Налаштування sudo без пароля для Supervisor

GitHub Actions потребує можливість перезапускати supervisor workers без введення пароля.

На сервері виконайте:

```bash
sudo visudo
```

Додайте в кінець файлу:

```
# GitHub Actions deploy user
www-data ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl restart galaxy-wishlist-worker:*
www-data ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl start galaxy-wishlist-worker:*
www-data ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl stop galaxy-wishlist-worker:*
```

Або якщо ви використовуєте іншого користувача (наприклад `deploy`):

```
# GitHub Actions deploy user
deploy ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl
```

### 2. Перевірка SSH доступу

Перевірте, що можете підключитися без пароля:

```bash
ssh -i ~/.ssh/id_ed25519 user@your-server.com "cd /var/www/galaxy-wishlist && git status"
```

Якщо команда виконується без запиту пароля - все налаштовано правильно.

## Тестування GitHub Actions

### Ручний запуск

1. Перейдіть в репозиторій на GitHub
2. Клікніть на **Actions**
3. Виберіть **Deploy to Production**
4. Клікніть **Run workflow** → **Run workflow**

### Автоматичний запуск

GitHub Actions автоматично запуститься при push в гілку `main`:

```bash
git add .
git commit -m "Deploy changes"
git push origin main
```

## Моніторинг

Статус деплою можна побачити в розділі **Actions** на GitHub.

### Якщо деплой не вдався

1. Перегляньте логи в GitHub Actions
2. Підключіться до сервера та перевірте логи:
   ```bash
   tail -f /var/www/galaxy-wishlist/storage/logs/laravel.log
   ```
3. Перевірте що всі secrets налаштовано правильно
4. Перевірте права доступу на сервері

## Альтернативи GitHub Actions

Якщо не хочете використовувати GitHub Actions, використовуйте скрипт деплою:

```bash
# Відредагуйте deploy.sh
nano deploy.sh

# Запустіть деплой
./deploy.sh
```

## Безпека

⚠️ **Важливо**: 
- Ніколи не комітьте приватні ключі в репозиторій
- Використовуйте окремий SSH ключ для GitHub Actions
- Регулярно змінюйте ключі
- Обмежте права доступу для deploy користувача
- Використовуйте GitHub Secrets для зберігання чутливих даних
