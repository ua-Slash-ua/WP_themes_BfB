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
        'input_text_phone' => 'plain',
        'input_text_schedule' => 'plain',
        'input_text_email' => 'plain',
        'input_text_address' => 'plain',
        'hl_data_contact' => 'json',
        'map_markers' => 'json',

    ];

    // Проходимо по кожному полю
    foreach ($meta_fields as $key => $value) {
        if ($value == 'plain') {
            // Отримуємо значення опції як простий текст
            $data[$key] = get_option($key, ''); // Додаємо значення, якщо опція не знайдена, повертається порожній рядок
        } elseif ($value == 'json') {
            // Отримуємо JSON-значення з опції
            $json_value = get_option($key, '');
            $decoded_value = json_decode($json_value);

            // Перевірка на помилку декодування JSON
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$key] = $decoded_value;
            } else {
                $data[$key] = 'Invalid JSON'; // Якщо є помилка в JSON, повертаємо повідомлення про помилку
            }
        }
        if ($key == 'map_markers') {
            $json_value = get_option($key, '');
            $decoded_value = json_decode($json_value, true); // true — для асоціативного масиву

            // Перевірка на помилку декодування JSON
            if (json_last_error() === JSON_ERROR_NONE) {
                $newData = array();
                foreach ($decoded_value as $mapKey => $mapValue) {
                    $newData[] = array(
                        'title' => $mapKey,
                        'coordinates' => $mapValue
                    );
                }
                $data[$key] = $newData;
            } else {
                $data[$key] = 'Invalid JSON';
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
        'input_text_phone',
        'input_text_schedule',
        'input_text_email',
        'input_text_address',
        'hl_data_contact',
        'map_markers',
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
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields('theme_settings_group'); // Виводимо nonce та інші безпечні дані для групи налаштувань
            do_settings_sections('theme_settingss_slug'); // Виводимо секції та поля налаштувань
            //++
            $input_text_phone = get_option('input_text_phone');
            $input_text_schedule = get_option('input_text_schedule');
            $input_text_email = get_option('input_text_email');
            $input_text_address = get_option('input_text_address');
            $hl_contact = get_option('hl_data_contact');
            $map_markers = get_option('map_markers');
            ?>
            <div class="mtab_hero">
                <ul class="mtab_header">
                    <li class="mtab_header_item tab_active" id="main">Головна</li>
                    <li class="mtab_header_item" id="map">Карта</li>
                    <li class="mtab_header_item" id="contact">Контакти</li>

                </ul>
                <div class="mtab_content">
                    <div class="mtab_content_item content_active" id="content_main">

                    </div>
                    <div class="mtab_content_item" id="content_map">
                        <input type="text" hidden="hidden" name="map_markers" id="map_markers"
                               value="<?php esc_attr_e($map_markers); ?>">
                        <div id="main_map"></div>
                        <div class="action_map">
                            <input type="button" id="editMarkersBtn" value="Редагувати мітки">
                        </div>

                        <div class="pop-up-marker">
                            <div class="pop-up-header">
                                <p>Нова мітка</p>
                                <div class="modal-close"></div>
                            </div>
                            <select name="marker_type" id="marker_type_select">
                                <option value="" disabled selected hidden="">Виберіть тип мітки</option>
                                <option value="gym">Головні зали BFB</option>

                            </select>
                            <div class="pop-up-action">
                                <input type="button" id="saveMarker" value="Зберегти мітку">
                                <input type="button" id="editMarker" value="Оновити мітку">
                                <input type="button" id="removeMarker" value="Видалити мітку">
                            </div>
                        </div>
                    </div>
                    <div class="mtab_content_item" id="content_contact">
                        <div class="form-container-input_text">
                            <label for="input_text_phone">Телефон</label>
                            <input type="text" value="<?php echo esc_attr($input_text_phone); ?>"
                                   name="input_text_phone"
                                   id="input_text_phone" class="input_text-item">
                            <label for="input_text_schedule">Графік роботи</label>
                            <textarea name="input_text_schedule"
                                      id="input_text_schedule" class="texarea-item" cols="30"
                                      rows="5"><?php echo esc_attr($input_text_schedule); ?></textarea>
                            <label for="input_text_email">Email</label>
                            <input type="text" value="<?php echo esc_attr($input_text_email); ?>"
                                   name="input_text_email"
                                   id="input_text_email" class="input_text-item">
                            <label for="input_text_address">Адреса</label>
                            <input type="text" value="<?php echo esc_attr($input_text_address); ?>"
                                   name="input_text_address"
                                   id="input_text_address" class="input_text-item">

                        </div>
                        <div class="form-container-hl">
                            <div id="container-hl-contact" class="container-hl">
                                <h1>Contact</h1>
                                <div class="container-hl-add">
                                    <input type="text" name="hl_data_contact" id="hl_data_contact"
                                           value="<?php echo esc_attr($hl_contact); ?>"
                                           hidden="hidden">
                                    <label for="hl_input_text_name">Name</label>
                                    <input type="text" id="hl_input_text_name" class="input_text-item">
                                    <label for="hl_input_text_link">Link</label>
                                    <input type="text" id="hl_input_text_link" class="input_text-item">
                                    <div class="hl_img_svg" id="hl_img_svg_icon">
                                        <label for="hl_img_svg_input_icon">Icon</label>
                                        <textarea id="hl_img_svg_input_icon" cols="30" rows="10"></textarea>
                                        <div class="hl_img_svg_preview"></div>
                                    </div>
                                    <input type="button" id="hl_btn_add_contact" value="Add">
                                </div>
                                <div class="container-hl-preview">

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="pop-up-overlay"></div>
                    <div class="message_alert">
                        <h3>Application повідомляє</h3>
                        <div class="message_alert_message">
                            <div class="msg-icon"></div>
                            <p>- Тестове повідомлення</p>
                        </div>
                        <div class="alert-progress-bar"></div>
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
        // Підключаємо стилі та скрипти Leaflet
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet/dist/leaflet.css');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet/dist/leaflet.js', [], null);
        enqueue_media_uploader();
    }
}

add_action('admin_enqueue_scripts', 'enqueue_theme_settings_style_and_script');



