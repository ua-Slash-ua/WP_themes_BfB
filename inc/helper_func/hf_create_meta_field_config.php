<?php
function create_meta_field_config(string $key, string $name_rest_api = '', string $sanitize_for_save = 'sanitize_text_field', string $sanitize_for_rest_api = 'sanitize_text_field'): array
{
    if (!$key) {
        return []; // Якщо ключ не переданий, повертаємо порожній масив
    }

    return [
        'key' => $key, // ключ мета-поля
        'name_rest_api' => $name_rest_api ?: $key, // Якщо name_rest_api не передано, використовуємо $key
        'sanitize_for_save' => $sanitize_for_save, // Санітайзер для збереження
        'sanitize_for_rest_api' => $sanitize_for_rest_api, // Санітайзер для REST API
    ];
}