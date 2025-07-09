
<?php
function register_theme_settings_menu()
{
    add_menu_page(
        'Налаштування теми',          // Назва вкладки
        'Налаштування теми',          // Назва меню
        'edit_posts',            // Права доступу (без manage_options)
        'theme_settingss_slug',     // Унікальний ідентифікатор
        'render_theme_settingss_page', // Функція, яка відображатиме контент
        'dashicons-email',       // Іконка
        6                         // Позиція у меню
    );
}

function register_theme_settings_submenu()
{
    add_submenu_page(
        'theme_settingss_slug',           // Батьківський slug
        'Налаштування теми',                 // Назва сторінки
        'Налаштування теми',                 // Назва пункту в підменю
        'edit_posts',                     // Права доступу
        'theme_settingss_slug',           // Той самий slug!
        'render_theme_settingss_page'     // Функція рендеру
    );
}

add_action('admin_menu', 'register_theme_settings_menu');
add_action('admin_menu', 'register_theme_settings_submenu');
function register_theme_settings_rest_route()
{
    register_rest_route('wp/v2', '/theme_settings', [
        'methods' => 'GET',
        'callback' => 'get_theme_settings_info',
        'permission_callback' => '__return_true', // або використовуйте власну перевірку доступу
    ]);
}

add_action('rest_api_init', 'register_theme_settings_rest_route');

function get_theme_settings_info()
{
    // Ініціалізація масиву даних
    $data = [];

    // Опис полів для отримання
    $meta_fields = [
        'save_data_text' => 'json',

    ];

    // Проходимо по кожному полю
    foreach ($meta_fields as $key => $value) {
        if ($value == 'plain') {
            // Отримуємо значення опції як простий текст
            $data[$key] = get_option($key, ''); // Додаємо значення, якщо опція не знайдена, повертається порожній рядок
        } elseif ($value == 'json') {
            // Отримуємо JSON-значення з опції
            $json_value = get_option($key, '');
            $decoded_value = json_decode($json_value, true);

            // Перевірка на помилку декодування JSON
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$key] = $decoded_value;
            } else {
                $data[$key] = 'Invalid JSON'; // Якщо є помилка в JSON, повертаємо повідомлення про помилку
            }
        }
    }

    // Повертаємо дані як REST API відповідь
    return rest_ensure_response($data);
}

// Реєстрація групи налаштувань
function register_settings_theme_settingss_group()
{

    $meta_fields = [
        'save_data_text'
    ];
    foreach ($meta_fields as $key) {
        register_setting(
            'theme_settings_group',  // Унікальна назва групи налаштувань
            $key   // Опція, яку ми будемо зберігати в цій групі
        );
    }
}

add_action('admin_init', 'register_settings_theme_settingss_group');

// Виведення сторінки налаштувань
function render_theme_settingss_page()
{
    ?>
    <div class="wrap">
        <h1>Налаштування теми</h1>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields('theme_settings_group'); // Виводимо nonce та інші безпечні дані для групи налаштувань
            do_settings_sections('theme_settingss_slug'); // Виводимо секції та поля налаштувань
            //++
            ?>
            <div class="mtab_hero">
                <ul class="mtab_header">
                    <li class="mtab_header_item tab_active" id="main">Головна</li>
                    <li class="mtab_header_item" id="settings">Налаштування</li>
                </ul>
                <div class="mtab_content">
                    <div class="mtab_content_item content_active" id="content_main">

                    </div>
                    <div class="mtab_content_item" id="content_settings">

                    </div>
                </div>
            </div>


            <input type="submit" value="Save Changes" class="button button-primary">
        </form>
    </div>
    <?php
}


function enqueue_theme_settings_style_and_script($hook)
{
    // Перевіряємо, чи це сторінка кастомного меню "theme_settingss_slug"
    if ($hook === 'toplevel_page_theme_settingss_slug') {
        // Підключаємо CSS стилі
        wp_enqueue_style(
            'theme_settings-style', // Унікальний ID для стилю
            get_template_directory_uri() . '/inc/admin_panel/ap_styles/ap_theme_settings_styles.css', // Шлях до файлу стилю
            [], // Залежності
            '1.0.0' // Версія стилю
        );

        // Підключаємо JS скрипт
        wp_enqueue_script(
            'theme_settings-script', // Унікальний ID для скрипту
            get_template_directory_uri() . '/inc/admin_panel/ap_scripts/ap_theme_settings_scripts.js', // Шлях до файлу скрипту
            ['jquery'], // Залежність від jQuery
            '1.0.0', // Версія скрипту
            true // Підключаємо скрипт внизу сторінки (після контенту)
        );
    }
}

add_action('admin_enqueue_scripts', 'enqueue_theme_settings_style_and_script');



