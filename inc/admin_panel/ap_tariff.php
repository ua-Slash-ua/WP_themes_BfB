<?php


// Реєстрація кастомного типу запису "tariff"
function create_tariff_post_type()
{
    // Викликаємо функцію register_post_type для реєстрації нового типу запису
    register_post_type('tariff', [
        'labels' => [
            'name' => 'Тарифи',  // Загальна назва для цього типу запису
            'singular_name' => 'Тариф',  // Одинична назва для цього типу запису
            'add_new' => 'Додати новий тариф',  // Текст для кнопки додавання нового запису
            'add_new_item' => 'Додати новий тариф',  // Текст для додавання нового елемента
            'edit_item' => 'Редагувати тариф',  // Текст для редагування елемента
            'new_item' => 'Новий тариф',  // Текст для нової позиції
            'view_item' => 'Переглянути тариф',  // Текст для перегляду елемента
            'search_items' => 'Шукати тарифи',  // Текст для пошуку
            'not_found' => 'Тарифи не знайдено',  // Текст, що показується, коли немає записів
            'not_found_in_trash' => 'В кошику тарифи не знайдено',  // Текст, що показується, коли в кошику немає записів
            'all_items' => 'Тарифи',  // Текст для перегляду всіх елементів
            'archives' => 'Архів тарифів',  // Архіви типу запису
        ],
        'public' => true,  // Робимо тип запису публічним, щоб він відображався на сайті
        'has_archive' => true,  // Дозволяємо мати архів цього типу запису
        'supports' => ['title'],  // Додаємо підтримку для полів заголовка
        'show_in_rest' => true,  // Дозволяє доступ через REST API
        'rest_base' => 'tariff',  // Назва для доступу до типу запису через REST API
        'menu_icon' => 'dashicons-admin-post', // Іконка меню (https://developer.wordpress.org/resource/dashicons/)
        'show_in_menu' => 'theme_settingss_slug', // <- це додає CPT як підменю
        // Інші параметри можна додати за потреби
    ]);
}

// Реєструємо функцію для виконання при ініціалізації WordPress
add_action('init', 'create_tariff_post_type');

// Функція для додавання мета-полів до відповіді REST API для типу запису "tariff"
function register_tariff_rest_api_meta_fields($data, $post, $request)
{
    // Перевіряємо, чи обробляється потрібний тип запису
    if ($post->post_type === 'tariff') {
        // Глобальний доступ до масиву полів
        global $fields_tariff;
        // Додаємо кожне значення мета-поля до відповіді API
        foreach ($fields_tariff as $field) {
            // Отримуємо значення мета-поля
            $raw_value = get_post_meta($post->ID, $field['key'], true);

            // Якщо вказано кастомну функцію для REST API
            if ($field['sanitize_for_rest_api'] && function_exists($field['sanitize_for_rest_api'])) {
                // Викликаємо функцію для обробки значення
                $raw_value = call_user_func($field['sanitize_for_rest_api'], $raw_value);
            }

            // Додаємо оброблене значення до відповіді API
            $data->data[$field['name_rest_api']] = $raw_value;
        }


    }

    // Повертаємо змінений об'єкт відповіді
    return $data;
}

// Прикріплюємо функцію до фільтра REST API для типу запису "tariff"
add_filter('rest_prepare_tariff', 'register_tariff_rest_api_meta_fields', 10, 3);

// Реєструє мета-бокс "main" для кастомного типу записів "tariff" 
add_action('add_meta_boxes', 'add_tariff_main_meta_boxes');
function add_tariff_main_meta_boxes()
{
    add_meta_box(
        'tariff_main_meta',                 // Унікальний ID мета-боксу
        'Main Поля',                 // Назва, яка відображається у редакторі
        'render_tariff_main_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'tariff',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "main")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_tariff_main_meta_box($post)
{

    $input_text_price = get_post_meta($post->ID, 'input_text_price', true);
    $input_text_time = get_post_meta($post->ID, 'input_text_time', true);
    $table_data_points = get_post_meta($post->ID, 'table_input_data-points', true);

    ?>

    <div class="form-container-input_text">
        <label for="input_text_price">Ціна (у місяць)</label>
        <input type="number" value="<?php echo esc_attr($input_text_price); ?>" name="input_text_price"
               id="input_text_price" class="input_text-item">
        <label for="input_text_time">Тривалість (у місяцях)</label>
        <input type="number" value="<?php echo esc_attr($input_text_time); ?>" name="input_text_time" id="input_text_time"
               class="input_text-item">

    </div>
    <div class="form-container-table">
        <div class="table-container">
            <div class="table_content_input">
                <input type="text" hidden="hidden" value="<?php echo esc_attr($table_data_points); ?>"
                       name="table_input_data-points" id="table_input_data-points">
                <label for="table_input_points-status">Статус</label>
                <input type="text" id="table_input_points-status" class="table_input_input" placeholder="Input status">
                <label for="table_input_points-text">Текст</label>
                <input type="text" id="table_input_points-text" class="table_input_input" placeholder="Input text">
                <input type="button" value="Add Points" id="btn_table_input_add-points">
            </div>
            <div class="table_content_main">
                <table class="form-table" id="table-points">
                    <thead class="table-head">
                    <tr>
                        <th>#</th>
                        <th>Статус</th>
                        <th>Текст</th>
                        <th>Дія</th>
                    </tr>
                    </thead>
                    <tbody class="table-body">

                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php

}

$fields_tariff = [
    create_meta_field_config('input_text_price', 'Price'),
    create_meta_field_config('input_text_time', 'Time'),
    create_meta_field_config('table_input_data-points', 'Points', 'sanitize_text_field', 'normalize_to_array')
];

add_action('save_post_tariff', 'save_tariff_meta');
function save_tariff_meta($post_id)
{
    global $fields_tariff;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach ($fields_tariff as $field) {
        $key = $field['key'];
        $sanitize = $field['sanitize_for_save'];
        if (!$sanitize) {
            $value = $_POST[$key];
        } else {
            $value = call_user_func($sanitize, $_POST[$key]);
        }
        // Оновлюємо мета-дані
        update_post_meta($post_id, $key, $value);
    }
}

// Функція для підключення стилів та скриптів для адміністративної панелі типу запису "tariff"
function enqueue_tariff_style_and_script($hook)
{
    // Перевіряємо, чи ми знаходимося на сторінці редагування або створення запису
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;

        // Перевіряємо, чи поточний пост є типом "tariff"
        if (isset($post) && get_post_type($post) === 'tariff') {
            // Підключаємо стилі
            wp_enqueue_style(
                'tariff_style', // Унікальний ID для стилю
                get_template_directory_uri() . '/inc/admin_panel/ap_styles/tariff_style.css', // Шлях до файлу стилю
                [], // Массив залежностей, якщо немає - залишаємо порожнім
                '1.0.0' // Версія стилю
            );

            // Підключаємо скрипти
            wp_enqueue_script(
                'tariff_script', // Унікальний ID для скрипту
                get_template_directory_uri() . '/inc/admin_panel/ap_scripts/tariff_script.js', // Шлях до файлу скрипту
                ['jquery'], // Масив залежностей, наприклад jQuery
                '1.0.0', // Версія скрипту
                true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
            );
            enqueue_media_uploader();
        }
    }
}

// Додаємо функцію до хуку для підключення стилів та скриптів в адмін-панелі
add_action('admin_enqueue_scripts', 'enqueue_tariff_style_and_script');