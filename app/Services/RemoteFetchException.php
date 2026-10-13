<?php

namespace App\Services;

use RuntimeException;

/**
 * Помилка завантаження зовнішньої сторінки. Повідомлення безпечно показувати користувачу.
 */
class RemoteFetchException extends RuntimeException {}
