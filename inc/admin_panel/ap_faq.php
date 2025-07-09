<?php


// Реєстрація кастомного типу запису "faq"
function create_faq_post_type()
{
    // Викликаємо функцію register_post_type для реєстрації нового типу запису
    register_post_type('faq', [
        'labels' => [
            'name' => 'Faqs',  // Загальна назва для цього типу запису
            'singular_name' => 'Faq',  // Одинична назва для цього типу запису
            'add_new' => 'Add New Faq',  // Текст для кнопки додавання нового запису
            'add_new_item' => 'Add New Faq',  // Текст для додавання нового елемента
            'edit_item' => 'Edit Faq',  // Текст для редагування елемента
            'new_item' => 'New Faq',  // Текст для нової позиції
            'view_item' => 'View Faq',  // Текст для перегляду елемента
            'search_items' => 'Search Faqs',  // Текст для пошуку
            'not_found' => 'No Faqs found',  // Текст, що показується, коли немає записів
            'not_found_in_trash' => 'No Faqs found in Trash',  // Текст, що показується, коли в кошику немає записів
            'all_items' => 'All Faqs',  // Текст для перегляду всіх елементів
            'archives' => 'Faq Archives',  // Архіви типу запису
        ],
        'public' => true,  // Робимо тип запису публічним, щоб він відображався на сайті
        'has_archive' => true,  // Дозволяємо мати архів цього типу запису
        'supports' => ['title'],  // Додаємо підтримку для полів заголовка
        'show_in_rest' => true,  // Дозволяє доступ через REST API
        'rest_base' => 'faq',  // Назва для доступу до типу запису через REST API
        'menu_icon' => 'dashicons-admin-post', // Іконка меню (https://developer.wordpress.org/resource/dashicons/)
        'show_in_menu' => 'theme_settingss_slug', // <- це додає CPT як підменю
        // Інші параметри можна додати за потреби
    ]);
}

// Реєструємо функцію для виконання при ініціалізації WordPress
add_action('init', 'create_faq_post_type');

// Функція для додавання мета-полів до відповіді REST API для типу запису "faq"
function register_faq_rest_api_meta_fields($data, $post, $request)
{
    // Перевіряємо, чи обробляється потрібний тип запису
    if ($post->post_type === 'faq') {
        // Глобальний доступ до масиву полів
        global $fields_faq;
        // Додаємо кожне значення мета-поля до відповіді API
        foreach ($fields_faq as $field) {
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

// Прикріплюємо функцію до фільтра REST API для типу запису "faq"
add_filter('rest_prepare_faq', 'register_faq_rest_api_meta_fields', 10, 3);

// Реєструє мета-бокс "content" для кастомного типу записів "faq" 
add_action('add_meta_boxes', 'add_faq_content_meta_boxes');
function add_faq_content_meta_boxes()
{
    add_meta_box(
        'faq_content_meta',                 // Унікальний ID мета-боксу
        'Content Поля',                 // Назва, яка відображається у редакторі
        'render_faq_content_meta_box',      // Назва функції, яка виводить HTML в середині боксу
        'faq',                      // Тип запису, до якого прив’язується бокс (у нашому випадку це "content")
        'normal',                   // Розміщення мета-боксу: 'normal', 'side', 'advanced'
        'high'                   // Пріоритет: 'high', 'core', 'default', 'low'
    );
}

function render_faq_content_meta_box($post)
{

    $input_text_question = get_post_meta($post->ID, 'input_text_question', true);
$input_text_answer = get_post_meta($post->ID, 'input_text_answer', true);

    ?>

              <div class="form-container-input_text">
<label for="input_text_question">Question</label>
        <input type="text" value="<?php echo esc_attr($input_text_question); ?>" name = "input_text_question" id="input_text_question" class ="input_text-item">
<label for="input_text_answer">Answer</label>
        <input type="text" value="<?php echo esc_attr($input_text_answer); ?>" name = "input_text_answer" id="input_text_answer" class ="input_text-item">

           </div>

    <?php

}

$fields_faq = [
    create_meta_field_config('input_text_question','Question'),
create_meta_field_config('input_text_answer','Answer')
];

add_action('save_post_faq', 'save_faq_meta');
function save_faq_meta($post_id)
{
    global $fields_faq;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach ($fields_faq as $field) {
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

// Функція для підключення стилів та скриптів для адміністративної панелі типу запису "faq"
function enqueue_faq_style_and_script($hook)
{
    // Перевіряємо, чи ми знаходимося на сторінці редагування або створення запису
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;

        // Перевіряємо, чи поточний пост є типом "faq"
        if (isset($post) && get_post_type($post) === 'faq') {
            // Підключаємо стилі
            wp_enqueue_style(
                'faq_style', // Унікальний ID для стилю
                get_template_directory_uri() . '/inc/admin_panel/ap_styles/faq_style.css', // Шлях до файлу стилю
                [], // Массив залежностей, якщо немає - залишаємо порожнім
                '1.0.0' // Версія стилю
            );

            // Підключаємо скрипти
            wp_enqueue_script(
                'faq_script', // Унікальний ID для скрипту
                get_template_directory_uri() . '/inc/admin_panel/ap_scripts/faq_script.js', // Шлях до файлу скрипту
                ['jquery'], // Масив залежностей, наприклад jQuery
                '1.0.0', // Версія скрипту
                true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
            );
            enqueue_media_uploader();
        }
    }
}

// Додаємо функцію до хуку для підключення стилів та скриптів в адмін-панелі
add_action('admin_enqueue_scripts', 'enqueue_faq_style_and_script');