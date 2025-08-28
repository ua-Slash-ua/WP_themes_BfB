<?php
/**
 * Отримує масив значень користувацьких мета-полів
 *
 * @param string $meta_key Метаполе користувача, наприклад 'input_text_locations_city'
 * @param bool $all Чи повертати усі значення (true) або лише унікальні (false)
 * @return array Масив значень
 */
function get_user_meta_values($meta_key, $all = false) {
    // Беремо всіх користувачів, які мають значення цього метаполя
    $users = get_users([
        'meta_key' => $meta_key,
        'fields' => ['ID']
    ]);

    $values = [];
    foreach ($users as $user) {
        $value = get_user_meta($user->ID, $meta_key, true);
        if ($value !== '') {
            $values[] = $value;
        }
    }

    if (!$all) {
        $values = array_values(array_unique($values)); // тільки унікальні значення
    }

    return $values;
}
