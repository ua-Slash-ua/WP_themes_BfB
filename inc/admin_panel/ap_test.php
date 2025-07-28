<?php


// Реєстрація кастомного типу запису "test"
function create_test_post_type()
{
    // Викликаємо функцію register_post_type для реєстрації нового типу запису
    register_post_type('test', [
        'labels' => [
            'name' => 'Tests',  // Загальна назва для цього типу запису
            'singular_name' => 'Test',  // Одинична назва для цього типу запису
            'add_new' => 'Add New Test',  // Текст для кнопки додавання нового запису
            'add_new_item' => 'Add New Test',  // Текст для додавання нового елемента
            'edit_item' => 'Edit Test',  // Текст для редагування елемента
            'new_item' => 'New Test',  // Текст для нової позиції
            'view_item' => 'View Test',  // Текст для перегляду елемента
            'search_items' => 'Search Tests',  // Текст для пошуку
            'not_found' => 'No Tests found',  // Текст, що показується, коли немає записів
            'not_found_in_trash' => 'No Tests found in Trash',  // Текст, що показується, коли в кошику немає записів
            'all_items' => 'All Tests',  // Текст для перегляду всіх елементів
            'archives' => 'Test Archives',  // Архіви типу запису
        ],
        'public' => true,  // Робимо тип запису публічним, щоб він відображався на сайті
        'has_archive' => true,  // Дозволяємо мати архів цього типу запису
        'supports' => ['title'],  // Додаємо підтримку для полів заголовка
        'show_in_rest' => true,  // Дозволяє доступ через REST API
        'rest_base' => 'test',  // Назва для доступу до типу запису через REST API
        'menu_icon' => 'dashicons-admin-post', // Іконка меню (https://developer.wordpress.org/resource/dashicons/)
        'show_in_menu' => 'theme_settingss_slug', // <- це додає CPT як підменю
        // Інші параметри можна додати за потреби
    ]);
}

// Реєструємо функцію для виконання при ініціалізації WordPress
add_action('init', 'create_test_post_type');

// Функція для додавання мета-полів до відповіді REST API для типу запису "test"
function register_test_rest_api_meta_fields($data, $post, $request)
{
    // Перевіряємо, чи обробляється потрібний тип запису
    if ($post->post_type === 'test') {
        // Глобальний доступ до масиву полів
        global $fields_test;
        // Додаємо кожне значення мета-поля до відповіді API
        foreach ($fields_test as $field) {
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

// Прикріплюємо функцію до фільтра REST API для типу запису "test"
add_filter('rest_prepare_test', 'register_test_rest_api_meta_fields', 10, 3);

// Реєструє мета-бокс "main" для кастомного типу записів "test" 
add_action('add_meta_boxes', 'add_test_main_meta_boxes');
function add_test_main_meta_boxes()
{
    add_meta_box(
        'test_main_meta',                 // Унікальний ID мета-боксу
        'Main Поля',                 // Назва, яка відображається у редакторі
        'render_test_main_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'test',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "main")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_test_main_meta_box($post)
{

    $img_link_gallery_ = get_post_meta($post->ID, 'img_link_data_gallery_', true);

    ?>

    <div class="form-container-img_link">
        <div class="img_link_hero" id="img_link_hero_gallery_">
            <input type="button" value="Upload Gallery_" id="img_link_upload_gallery_">
            <input type="text" hidden="hidden" value="<?php echo esc_attr($img_link_gallery_); ?>"
                   id="img_link_data_gallery_" name="img_link_data_gallery_">
            <div class="img_link_preview_container" id="img_link_preview_container_gallery_">

            </div>
        </div>

    </div>

    <?php

}

$fields_test = [
    create_meta_field_config('img_link_data_gallery_', 'Gallery_', 'sanitize_text_field', 'normalize_array_or_string')
];

add_action('save_post_test', 'save_test_meta');
function save_test_meta($post_id)
{
    global $fields_test;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach ($fields_test as $field) {
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

// Функція для підключення стилів та скриптів для адміністративної панелі типу запису "test"
function enqueue_test_style_and_script($hook)
{
    // Перевіряємо, чи ми знаходимося на сторінці редагування або створення запису
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;

        // Перевіряємо, чи поточний пост є типом "test"
        if (isset($post) && get_post_type($post) === 'test') {
            // Підключаємо стилі
            wp_enqueue_style(
                'test_style', // Унікальний ID для стилю
                get_template_directory_uri() . '/inc/admin_panel/ap_styles/test_style.css', // Шлях до файлу стилю
                [], // Массив залежностей, якщо немає - залишаємо порожнім
                '1.0.0' // Версія стилю
            );

            // Підключаємо скрипти
            wp_enqueue_script(
                'test_script', // Унікальний ID для скрипту
                get_template_directory_uri() . '/inc/admin_panel/ap_scripts/test_script.js', // Шлях до файлу скрипту
                ['jquery'], // Масив залежностей, наприклад jQuery
                '1.0.0', // Версія скрипту
                true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
            );
            enqueue_media_uploader();
        }
    }
}

// Додаємо функцію до хуку для підключення стилів та скриптів в адмін-панелі
add_action('admin_enqueue_scripts', 'enqueue_test_style_and_script');