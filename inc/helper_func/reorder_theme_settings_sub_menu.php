<?php
add_action('admin_menu', 'reorder_theme_settings_submenu', 100);

function reorder_theme_settings_submenu() {
    global $submenu;

    if (!isset($submenu['theme_settingss_slug'])) {
        return;
    }

    $items = $submenu['theme_settingss_slug'];

    if (count($items) < 2) {
        return; // Немає чого міняти, якщо менше 2 пунктів
    }

    // Витягуємо останній елемент
    $last = array_pop($items);

    // Додаємо його на початок
    array_unshift($items, $last);

    // Присвоюємо оновлений масив назад
    $submenu['theme_settingss_slug'] = $items;
}
