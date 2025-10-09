<?php

return [
    'required' => 'Поле :attribute є обов\'язковим.',
    'string' => 'Поле :attribute має бути рядком.',
    'max' => [
        'string' => 'Поле :attribute не може містити більше :max символів.',
        'file' => 'Розмір файлу :attribute не може бути більше :max кілобайт.',
    ],
    'min' => [
        'string' => 'Поле :attribute має містити принаймні :min символів.',
    ],
    'email' => 'Поле :attribute має бути дійсною email адресою.',
    'unique' => 'Таке значення поля :attribute вже існує.',
    'url' => 'Поле :attribute має бути дійсним URL.',
    'numeric' => 'Поле :attribute має бути числом.',
    'image' => 'Файл :attribute має бути зображенням.',
    'mimes' => 'Файл :attribute має бути типу: :values.',
    
    'attributes' => [
        'title' => 'назва',
        'description' => 'опис',
        'url' => 'посилання',
        'price' => 'ціна',
        'currency' => 'валюта',
        'priority' => 'пріоритет',
        'email' => 'email',
        'password' => 'пароль',
        'name' => 'ім\'я',
        'avatar' => 'аватар',
    ],
];
