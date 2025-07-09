<?php
function normalize_array_or_string($value) {
    if (is_string($value)) {
        // Спроба декодувати JSON-рядок у масив
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $value = $decoded;
        } else {
            return ''; // Не валідний JSON
        }
    }

    // Якщо вже масив або об'єкт
    $array = (array) $value;

    if (count($array) === 1) {
        return reset($array); // Повертає єдиний елемент як рядок
    }

    return $array;
}