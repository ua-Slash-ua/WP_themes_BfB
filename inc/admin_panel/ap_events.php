<?php


// Реєстрація кастомного типу запису "events"
function create_events_post_type()
{
    // Викликаємо функцію register_post_type для реєстрації нового типу запису
    register_post_type('events', [
        'labels' => [
            'name' => 'Eventss',  // Загальна назва для цього типу запису
            'singular_name' => 'Events',  // Одинична назва для цього типу запису
            'add_new' => 'Add New Events',  // Текст для кнопки додавання нового запису
            'add_new_item' => 'Add New Events',  // Текст для додавання нового елемента
            'edit_item' => 'Edit Events',  // Текст для редагування елемента
            'new_item' => 'New Events',  // Текст для нової позиції
            'view_item' => 'View Events',  // Текст для перегляду елемента
            'search_items' => 'Search Eventss',  // Текст для пошуку
            'not_found' => 'No Eventss found',  // Текст, що показується, коли немає записів
            'not_found_in_trash' => 'No Eventss found in Trash',  // Текст, що показується, коли в кошику немає записів
            'all_items' => 'All Eventss',  // Текст для перегляду всіх елементів
            'archives' => 'Events Archives',  // Архіви типу запису
        ],
        'public' => true,  // Робимо тип запису публічним, щоб він відображався на сайті
        'has_archive' => true,  // Дозволяємо мати архів цього типу запису
        'supports' => ['title'],  // Додаємо підтримку для полів заголовка
        'show_in_rest' => true,  // Дозволяє доступ через REST API
        'rest_base' => 'events',  // Назва для доступу до типу запису через REST API
        'menu_icon' => 'dashicons-admin-post', // Іконка меню (https://developer.wordpress.org/resource/dashicons/)
        'show_in_menu' => 'theme_settingss_slug', // <- це додає CPT як підменю
        // Інші параметри можна додати за потреби
    ]);
}

// Реєструємо функцію для виконання при ініціалізації WordPress
add_action('init', 'create_events_post_type');

// Функція для додавання мета-полів до відповіді REST API для типу запису "events"
function register_events_rest_api_meta_fields($data, $post, $request)
{
    // Перевіряємо, чи обробляється потрібний тип запису
    if ($post->post_type === 'events') {
        // Глобальний доступ до масиву полів
        global $fields_events;
        // Додаємо кожне значення мета-поля до відповіді API
        foreach ($fields_events as $field) {
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

// Прикріплюємо функцію до фільтра REST API для типу запису "events"
add_filter('rest_prepare_events', 'register_events_rest_api_meta_fields', 10, 3);

// Реєструє мета-бокс "main" для кастомного типу записів "events" 
add_action('add_meta_boxes', 'add_events_main_meta_boxes');
function add_events_main_meta_boxes()
{
    add_meta_box(
        'events_main_meta',                 // Унікальний ID мета-боксу
        'Main Поля',                 // Назва, яка відображається у редакторі
        'render_events_main_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'events',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "main")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_events_main_meta_box($post)
{

    $input_text_title = get_post_meta($post->ID, 'input_text_title', true);
$input_text_city = get_post_meta($post->ID, 'input_text_city', true);
$input_text_location = get_post_meta($post->ID, 'input_text_location', true);
$textarea_description = get_post_meta($post->ID, 'textarea_description', true);
$img_link_banner = get_post_meta($post->ID, 'img_link_data_banner', true);
$hl_result = get_post_meta($post->ID, 'hl_data_result', true);

    ?>

              <div class="form-container-input_text">
<label for="input_text_title">Title</label>
        <input type="text" value="<?php echo esc_attr($input_text_title); ?>" name = "input_text_title" id="input_text_title" class ="input_text-item">
<label for="input_text_city">City</label>
        <input type="text" value="<?php echo esc_attr($input_text_city); ?>" name = "input_text_city" id="input_text_city" class ="input_text-item">
<label for="input_text_location">Location</label>
        <input type="text" value="<?php echo esc_attr($input_text_location); ?>" name = "input_text_location" id="input_text_location" class ="input_text-item">

           </div>
          <div class="form-container-textarea">
<label for="textarea_description">Description</label>
        <textarea name="textarea_description" id="textarea_description" cols="30" rows="10"><?php echo esc_attr($textarea_description);?></textarea>

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
          <div class="form-container-hl">
<div id="container-hl-result" class="container-hl">
            <h1>Contact</h1>
            <div class="container-hl-add">
                <input type="text" name="hl_data_result" id="hl_data_result"
                       value="<?php echo esc_attr($hl_result); ?>"
                       hidden="hidden">
                <label for="hl_input_text_text">Text</label>
                <input type="text" id="hl_input_text_text" class="input_text-item">
<div class="hl_img_svg" id="hl_img_svg_icon">
                    <label for="hl_img_svg_input_icon">Icon</label>
                    <textarea id="hl_img_svg_input_icon" cols="30" rows="10"></textarea>
                    <div class="hl_img_svg_preview"></div>
                </div>
                <input type="button" id="hl_btn_add_result" value="Add">
            </div>
            <div class="container-hl-preview">

            </div>
        </div>

           </div>

    <?php

}

// Реєструє мета-бокс "schedule" для кастомного типу записів "events" 
add_action('add_meta_boxes', 'add_events_schedule_meta_boxes');
function add_events_schedule_meta_boxes()
{
    add_meta_box(
        'events_schedule_meta',                 // Унікальний ID мета-боксу
        'Schedule Поля',                 // Назва, яка відображається у редакторі
        'render_events_schedule_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'events',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "schedule")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_events_schedule_meta_box($post)
{

    $hl_schedule = get_post_meta($post->ID, 'hl_data_schedule', true);

    ?>

              <div class="form-container-hl">
<div id="container-hl-schedule" class="container-hl">
            <h1>Contact</h1>
            <div class="container-hl-add">
                <input type="text" name="hl_data_schedule" id="hl_data_schedule"
                       value="<?php echo esc_attr($hl_schedule); ?>"
                       hidden="hidden">
                <label for="hl_input_date_date">Date</label>
                <input type="date" id="hl_input_date_date" class="input_text-item">
<label for="hl_input_time_time">Time</label>
                <input type="time" id="hl_input_time_time" class="input_text-item">
                <input type="button" id="hl_btn_add_schedule" value="Add">
            </div>
            <div class="container-hl-preview">

            </div>
        </div>

           </div>

    <?php

}

$fields_events = [
    create_meta_field_config('input_text_title','Title'),
create_meta_field_config('input_text_city','City'),
create_meta_field_config('input_text_location','Location'),
create_meta_field_config('textarea_description','Description'),
create_meta_field_config('img_link_data_banner', 'Banner', 'sanitize_text_field', 'normalize_array_or_string'),
create_meta_field_config('hl_data_result', 'Result', '', 'normalize_to_array'),
create_meta_field_config('hl_data_schedule', 'Schedule', '', 'normalize_to_array'),
];

add_action('save_post_events', 'save_events_meta');
function save_events_meta($post_id)
{
    global $fields_events;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach ($fields_events as $field) {
        $key = $field['key'];
        $sanitize = $field['sanitize_for_save'];
        if (!$sanitize){
            $value = $_POST[$key];
        }else{
            $value = call_user_func($sanitize, $_POST[$key]);
        }
        // Оновлюємо мета-дані
        update_post_meta($post_id, $key, $value);
    }
}

// Функція для підключення стилів та скриптів для адміністративної панелі типу запису "events"
function enqueue_events_style_and_script($hook)
{
    // Перевіряємо, чи ми знаходимося на сторінці редагування або створення запису
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;

        // Перевіряємо, чи поточний пост є типом "events"
        if (isset($post) && get_post_type($post) === 'events') {
            // Підключаємо стилі
            wp_enqueue_style(
                'events_style', // Унікальний ID для стилю
                get_template_directory_uri() . '/inc/admin_panel/ap_styles/events_style.css', // Шлях до файлу стилю
                [], // Массив залежностей, якщо немає - залишаємо порожнім
                '1.0.0' // Версія стилю
            );

            // Підключаємо скрипти
            wp_enqueue_script(
                'events_script', // Унікальний ID для скрипту
                get_template_directory_uri() . '/inc/admin_panel/ap_scripts/events_script.js', // Шлях до файлу скрипту
                ['jquery'], // Масив залежностей, наприклад jQuery
                '1.0.0', // Версія скрипту
                true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
            );
            enqueue_media_uploader();
        }
    }
}

// Додаємо функцію до хуку для підключення стилів та скриптів в адмін-панелі
add_action('admin_enqueue_scripts', 'enqueue_events_style_and_script');