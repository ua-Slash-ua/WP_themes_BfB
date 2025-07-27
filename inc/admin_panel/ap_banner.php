<?php


// Реєстрація кастомного типу запису "banner"
function create_banner_post_type()
{
    // Викликаємо функцію register_post_type для реєстрації нового типу запису
    register_post_type('banner', [
        'labels' => [
            'name' => 'Banners',  // Загальна назва для цього типу запису
            'singular_name' => 'Banner',  // Одинична назва для цього типу запису
            'add_new' => 'Add New Banner',  // Текст для кнопки додавання нового запису
            'add_new_item' => 'Add New Banner',  // Текст для додавання нового елемента
            'edit_item' => 'Edit Banner',  // Текст для редагування елемента
            'new_item' => 'New Banner',  // Текст для нової позиції
            'view_item' => 'View Banner',  // Текст для перегляду елемента
            'search_items' => 'Search Banners',  // Текст для пошуку
            'not_found' => 'No Banners found',  // Текст, що показується, коли немає записів
            'not_found_in_trash' => 'No Banners found in Trash',  // Текст, що показується, коли в кошику немає записів
            'all_items' => 'All Banners',  // Текст для перегляду всіх елементів
            'archives' => 'Banner Archives',  // Архіви типу запису
        ],
        'public' => true,  // Робимо тип запису публічним, щоб він відображався на сайті
        'has_archive' => true,  // Дозволяємо мати архів цього типу запису
        'supports' => ['title'],  // Додаємо підтримку для полів заголовка
        'show_in_rest' => true,  // Дозволяє доступ через REST API
        'rest_base' => 'banner',  // Назва для доступу до типу запису через REST API
        'menu_icon' => 'dashicons-admin-post', // Іконка меню (https://developer.wordpress.org/resource/dashicons/)
        'show_in_menu' => 'theme_settingss_slug', // <- це додає CPT як підменю
        // Інші параметри можна додати за потреби
    ]);
}

// Реєструємо функцію для виконання при ініціалізації WordPress
add_action('init', 'create_banner_post_type');

// Функція для додавання мета-полів до відповіді REST API для типу запису "banner"
function register_banner_rest_api_meta_fields($data, $post, $request)
{
    // Перевіряємо, чи обробляється потрібний тип запису
    if ($post->post_type === 'banner') {
        // Глобальний доступ до масиву полів
        global $fields_banner;
        // Додаємо кожне значення мета-поля до відповіді API
        foreach ($fields_banner as $field) {
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

// Прикріплюємо функцію до фільтра REST API для типу запису "banner"
add_filter('rest_prepare_banner', 'register_banner_rest_api_meta_fields', 10, 3);

// Реєструє мета-бокс "main" для кастомного типу записів "banner" 
add_action('add_meta_boxes', 'add_banner_main_meta_boxes');
function add_banner_main_meta_boxes()
{
    add_meta_box(
        'banner_main_meta',                 // Унікальний ID мета-боксу
        'Main Поля',                 // Назва, яка відображається у редакторі
        'render_banner_main_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'banner',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "main")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_banner_main_meta_box($post)
{

    $input_text_title = get_post_meta($post->ID, 'input_text_title', true);
    $input_text_description = get_post_meta($post->ID, 'input_text_description', true);
    $input_text_sub_title = get_post_meta($post->ID, 'input_text_sub_title', true);
    $img_link_banner = get_post_meta($post->ID, 'img_link_data_banner', true);

    ?>

    <div class="form-container-input_text">
        <label for="input_text_title">Title</label>
        <input type="text" value="<?php echo esc_attr($input_text_title); ?>" name="input_text_title"
               id="input_text_title" class="input_text-item">
        <label for="input_text_description">Description</label>
        <input type="text" value="<?php echo esc_attr($input_text_description); ?>" name="input_text_description"
               id="input_text_description" class="input_text-item">
        <label for="input_text_sub_title">Sub_title</label>
        <input type="text" value="<?php echo esc_attr($input_text_sub_title); ?>" name="input_text_sub_title"
               id="input_text_sub_title" class="input_text-item">

    </div>
    <div class="form-container-img_link">
        <div class="img_link_hero" id="img_link_hero_banner">
            <input type="button" value="Upload Banner" id="img_link_upload_banner">
            <input type="text" hidden="hidden" value="<?php echo esc_attr($img_link_banner); ?>"
                   id="img_link_data_banner" name="img_link_data_banner">
            <div class="img_link_preview_container" id="img_link_preview_container_banner">

            </div>
        </div>

    </div>

    <?php

}

// Реєструє мета-бокс "video" для кастомного типу записів "banner" 
add_action('add_meta_boxes', 'add_banner_video_meta_boxes');
function add_banner_video_meta_boxes()
{
    add_meta_box(
        'banner_video_meta',                 // Унікальний ID мета-боксу
        'Video Поля',                 // Назва, яка відображається у редакторі
        'render_banner_video_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'banner',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "video")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_banner_video_meta_box($post)
{

    $img_link_aside_photo = get_post_meta($post->ID, 'img_link_data_aside_photo', true);
    $video_aside_video = get_post_meta($post->ID, 'video_data_aside_video', true);

    ?>

    <div class="form-container-img_link">
        <div class="img_link_hero" id="img_link_hero_aside_photo">
            <input type="button" value="Upload Aside_photo" id="img_link_upload_aside_photo">
            <input type="text" hidden="hidden" value="<?php echo esc_attr($img_link_aside_photo); ?>"
                   id="img_link_data_aside_photo" name="img_link_data_aside_photo">
            <div class="img_link_preview_container" id="img_link_preview_container_aside_photo">

            </div>
        </div>

    </div>
    <div class="form-container-video">
        <div class="video_hero" id="video_hero_aside_video">
            <input type="button" value="Upload Aside_video" id="video_upload_aside_video">
            <input type="text" hidden="hidden" value="<?php echo esc_attr($video_aside_video); ?>"
                   id="video_data_aside_video" name="video_data_aside_video">
            <div class="video_preview_container" id="video_preview_container_aside_video">

            </div>
        </div>

    </div>

    <?php

}

$fields_banner = [
    create_meta_field_config('input_text_title', 'Title'),
    create_meta_field_config('input_text_description', 'Description'),
    create_meta_field_config('input_text_sub_title', 'Sub_title'),
    create_meta_field_config('img_link_data_banner', 'Banner', 'sanitize_text_field', 'normalize_array_or_string'),
    create_meta_field_config('img_link_data_aside_photo', 'Aside_photo', 'sanitize_text_field', 'normalize_array_or_string'),
    create_meta_field_config('video_data_aside_video', 'Aside_video', 'sanitize_text_field', 'normalize_array_or_string')
];

add_action('save_post_banner', 'save_banner_meta');
function save_banner_meta($post_id)
{
    global $fields_banner;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach ($fields_banner as $field) {
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

// Функція для підключення стилів та скриптів для адміністративної панелі типу запису "banner"
function enqueue_banner_style_and_script($hook)
{
    // Перевіряємо, чи ми знаходимося на сторінці редагування або створення запису
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;

        // Перевіряємо, чи поточний пост є типом "banner"
        if (isset($post) && get_post_type($post) === 'banner') {
            // Підключаємо стилі
            wp_enqueue_style(
                'banner_style', // Унікальний ID для стилю
                get_template_directory_uri() . '/inc/admin_panel/ap_styles/banner_style.css', // Шлях до файлу стилю
                [], // Массив залежностей, якщо немає - залишаємо порожнім
                '1.0.0' // Версія стилю
            );

            // Підключаємо скрипти
            wp_enqueue_script(
                'banner_script', // Унікальний ID для скрипту
                get_template_directory_uri() . '/inc/admin_panel/ap_scripts/banner_script.js', // Шлях до файлу скрипту
                ['jquery'], // Масив залежностей, наприклад jQuery
                '1.0.0', // Версія скрипту
                true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
            );
            enqueue_media_uploader();
        }
    }
}

// Додаємо функцію до хуку для підключення стилів та скриптів в адмін-панелі
add_action('admin_enqueue_scripts', 'enqueue_banner_style_and_script');