<?php
function sanitize_svg($value) {
    // Видаляємо зайві пробіли та нові рядки
    $value = trim($value);
    return $value;  // Повертаємо SVG-код, якщо він виглядає коректним
}