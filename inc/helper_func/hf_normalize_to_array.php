<?php
function normalize_to_array($value) {
    if (is_string($value)) {
        // Якщо значення - рядок, намагаємося його перетворити в масив
        $value = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return []; // Якщо не вдалося декодувати, повертаємо порожній масив
        }
    }

    return (array) $value; // Перетворюємо на масив, якщо це не рядок
}