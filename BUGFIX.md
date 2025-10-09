# 🐛 Виправлена помилка

## Проблема:
```
Call to undefined method App\Http\Controllers\WishController::authorize()
```

## Рішення:
Додано трейт `AuthorizesRequests` в `WishController`:

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WishController extends Controller
{
    use AuthorizesRequests;
    
    // ... решта коду
}
```

## Результат:
✅ Метод `authorize()` тепер працює коректно
✅ Policy перевірки доступу працюють
✅ Редагування бажань працює без помилок

## Що це дає:
Трейт `AuthorizesRequests` надає контролеру можливість використовувати:
- `$this->authorize('update', $wish)` - перевірка прав доступу
- `$this->authorizeResource()` - автоматична авторизація для ресурсних контролерів

Це стандартна практика Laravel для контролерів, які використовують Policy.
