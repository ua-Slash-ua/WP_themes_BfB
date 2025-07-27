<?php
// === 1. Конфігурація полів ===
$fields_user = [
    create_meta_field_config('input_text_position', 'position'),
    create_meta_field_config('input_text_experience', 'experience'),
    create_meta_field_config('input_text_locations', 'locations'),
    create_meta_field_config('input_text_boards', 'boards'),
    create_meta_field_config('textarea_super_power', 'super_power'),
    create_meta_field_config('img_link_data_banner', 'banner', 'sanitize_text_field', 'normalize_array_or_string'),
    create_meta_field_config('point_data_favourite_exercise', 'favourite_exercise', 'sanitize_text_field', 'normalize_to_array'),
    create_meta_field_config('point_data_my_specialty', 'my_specialty', 'sanitize_text_field', 'normalize_to_array'),
    create_meta_field_config('hl_data_my_experience', 'my_experience', '', 'normalize_to_array'),
    create_meta_field_config('hl_data_my_wlocation', 'my_wlocation', '', 'normalize_to_array'),
];



function add_trainer_meta_boxes($user)
{
    $user_id = $user->ID;
    if (!in_array('bfb_coach', (array)$user->roles)) {
        return;
    }
    $input_text_position = get_user_meta($user_id, 'input_text_position', true);
    $input_text_experience = get_user_meta($user_id, 'input_text_experience', true);
    $input_text_locations = get_user_meta($user_id, 'input_text_locations', true);
    $input_text_boards = get_user_meta($user_id, 'input_text_boards', true);
    $textarea_super_power = get_user_meta($user_id, 'textarea_super_power', true);
    $point_favourite_exercise = get_user_meta($user_id, 'point_data_favourite_exercise', true);
    $point_my_specialty = get_user_meta($user_id, 'point_data_my_specialty', true);
    $hl_my_experience = get_user_meta($user_id, 'hl_data_my_experience', true);
    $hl_my_wlocation = get_user_meta($user_id, 'hl_data_my_wlocation', true);

    ?>
    <div class="mtab_hero">
        <ul class="mtab_header">
            <li class="mtab_header_item tab_active" id="self">Особисті дані</li>
            <li class="mtab_header_item" id="specialty">Спеціалізація</li>
            <li class="mtab_header_item" id="exercise">Моя улюблена вправа</li>
            <li class="mtab_header_item" id="experience">Досвід роботи</li>
            <li class="mtab_header_item" id="wlocation">Місця проведення тренувань</li>
            <li class="mtab_header_item" id="certificate">Сертифікати</li>

        </ul>
        <div class="mtab_content_item content_active" id="content_self">
            <div class="form-container-input_text">
                <label for="input_text_position">Position</label>
                <input type="text" value="<?php echo esc_attr($input_text_position); ?>" name="input_text_position"
                       id="input_text_position" class="input_text-item">
                <label for="input_text_experience">Experience</label>
                <input type="text" value="<?php echo esc_attr($input_text_experience); ?>" name="input_text_experience"
                       id="input_text_experience" class="input_text-item">
                <label for="input_text_locations">Locations</label>
                <input type="text" value="<?php echo esc_attr($input_text_locations); ?>" name="input_text_locations"
                       id="input_text_locations" class="input_text-item">
                <label for="input_text_boards">Boards</label>
                <input type="text" value="<?php echo esc_attr($input_text_boards); ?>" name="input_text_boards"
                       id="input_text_boards" class="input_text-item">

            </div>
            <div class="form-container-textarea">
                <label for="textarea_super_power">Super_power</label>
                <textarea name="textarea_super_power" id="textarea_super_power" cols="30"
                          rows="10"><?php echo esc_attr($textarea_super_power); ?></textarea>

            </div>
        </div>
        <div class="mtab_content_item " id="content_specialty">
            <div class="form-container-point">
                <div class="point_hero" id="point_hero_my_specialty">
                    <div class="point-edit">
                        <label for="point_input_my_specialty">My_specialty
                            <input type="text" id="point_input_my_specialty" class="point_input">
                        </label>
                        <input type="button" value="+" id="point_add_my_specialty" class="point_add">
                    </div>

                    <label for="point_data_my_specialty">
                        <input type="text" hidden="hidden" id="point_data_my_specialty" name="point_data_my_specialty"
                               value="<?php echo esc_attr(json_encode($point_my_specialty)); ?>">
                    </label>
                    <div id="point_container_my_specialty" class="point_container">

                    </div>
                </div>
            </div>

        </div>
        <div class="mtab_content_item " id="content_exercise">
            <div class="form-container-point">
                <div class="point_hero" id="point_hero_favourite_exercise">
                    <div class="point-edit">
                        <label for="point_input_favourite_exercise">Favourite_exercise
                            <input type="text" id="point_input_favourite_exercise" class="point_input">
                        </label>
                        <input type="button" value="+" id="point_add_favourite_exercise" class="point_add">
                    </div>

                    <label for="point_data_favourite_exercise">
                        <input type="text" hidden="hidden" id="point_data_favourite_exercise"
                               name="point_data_favourite_exercise"
                               value="<?php echo esc_attr(json_encode($point_favourite_exercise)); ?>">
                    </label>
                    <div id="point_container_favourite_exercise" class="point_container">

                    </div>
                </div>
            </div>
        </div>
        <div class="mtab_content_item " id="content_experience">
            <div id="container-hl-my_experience" class="container-hl">
                <h1>Досвід роботи</h1>
                <div class="container-hl-add">
                    <input type="text" name="hl_data_my_experience" id="hl_data_my_experience"
                           value="<?php echo esc_attr(json_encode($hl_my_experience)); ?>"
                           hidden="hidden">

                    <label for="hl_input_text_gym">Gym</label>
                    <input type="text" id="hl_input_text_gym" class="input_text-item">
                    <label for="hl_input_date_date_start">Date_start</label>
                    <input type="date" id="hl_input_date_date_start" class="input_text-item">
                    <label for="hl_input_date_date_end">Date_end</label>
                    <input type="date" id="hl_input_date_date_end" class="input_text-item">
                    <label for="hl_textarea_ex_description">Ex_description</label>
                    <textarea id="hl_textarea_ex_description" cols="30" rows="10"></textarea>
                    <input type="button" id="hl_btn_add_my_experience" value="Add">
                </div>
                <div class="container-hl-preview">

                </div>
            </div>
        </div>
        <div class="mtab_content_item " id="content_wlocation">
            <div id="container-hl-my_wlocation" class="container-hl">
                <h1>Місця проведення тренувань</h1>
                <div class="container-hl-add">
                    <input type="text" name="hl_data_my_wlocation" id="hl_data_my_wlocation"
                           value="<?php echo esc_attr(json_encode($hl_my_wlocation)); ?>"
                           hidden="hidden">

                    <label for="hl_input_text_title">Title</label>
                    <input type="text" id="hl_input_text_title" class="input_text-item">
                    <label for="hl_input_text_email">Email</label>
                    <input type="text" id="hl_input_text_email" class="input_text-item">
                    <label for="hl_input_text_phone">Phone</label>
                    <input type="text" id="hl_input_text_phone" class="input_text-item">
                    <label for="hl_input_text_schedule_five">Schedule_five</label>
                    <input type="text" id="hl_input_text_schedule_five" class="input_text-item">
                    <label for="hl_input_text_schedule_two">Schedule_two</label>
                    <input type="text" id="hl_input_text_schedule_two" class="input_text-item">
                    <label for="hl_input_text_address">Address</label>
                    <input type="text" id="hl_input_text_address" class="input_text-item">
                    <input type="button" id="hl_btn_add_my_wlocation" value="Add">
                </div>
                <div class="container-hl-preview">

                </div>
            </div>
        </div>
        <div class="mtab_content_item " id="content_certificate">
        </div>
    </div>
    <?php


}

add_action('show_user_profile', 'add_trainer_meta_boxes');
add_action('edit_user_profile', 'add_trainer_meta_boxes');

// === 2. Збереження в профілі користувача ===
add_action('personal_options_update', 'save_user_custom_meta_fields');
add_action('edit_user_profile_update', 'save_user_custom_meta_fields');

function save_user_custom_meta_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    global $fields_user;
    foreach ($fields_user as $field) {
        $key = $field['key'];
        if (!isset($_POST[$key])) continue;

        $value = $_POST[$key];
        if ($field['sanitize_for_save']) {
            $value = call_user_func($field['sanitize_for_save'], $value);
        }

        update_user_meta($user_id, $key, $value);
    }
}

add_filter('rest_prepare_user', 'register_user_rest_meta_fields', 10, 3);

function register_user_rest_meta_fields($response, $user, $request) {
    global $fields_user;

    foreach ($fields_user as $field) {
        $key = $field['key'];
        $value = get_user_meta($user->ID, $key, true);

        if ($field['sanitize_for_rest_api'] && function_exists($field['sanitize_for_rest_api'])) {
            $value = call_user_func($field['sanitize_for_rest_api'], $value);
        }

        $response->data[$field['name_rest_api']] = $value;
    }

    return $response;
}


add_action('admin_enqueue_scripts', function ($hook) {
    // Потрібна саме сторінка редагування користувача
    if ($hook !== 'user-edit.php' && $hook !== 'profile.php') {
        return;
    }

    // Отримуємо ID користувача, який редагується
    $user_id = 0;

    // Для user-edit.php id у GET
    if (isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
    } // Для profile.php — поточний користувач
    elseif ($hook === 'profile.php') {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return;
    }

    $user = get_userdata($user_id);

    if (!$user) {
        return;
    }

    // Перевірка ролі тренер
    if (in_array('bfb_coach', (array)$user->roles)) {
        // Підключаємо свій JS (та CSS, якщо треба)
        wp_enqueue_script(
            'my-trainer-script',
            get_template_directory_uri() . '/inc/admin_panel/ap_scripts/coach_scripts.js',
            ['jquery'], // залежності
            '1.0',
            true
        );

        // Наприклад, CSS
        wp_enqueue_style(
            'my-trainer-style',
            get_template_directory_uri() . '/inc/admin_panel/ap_styles/coach_styles.css',
            [],
            '1.0'
        );
    }
});
add_action('rest_insert_user', 'add_custom_user_meta_on_create', 10, 3);

function add_custom_user_meta_on_create($user, $request, $creating) {
    if (!$creating) return;

    if (!isset($request['meta']) || !is_array($request['meta'])) return;

    $meta = $request['meta'];

    // Перелік очікуваних мета-полів (назви з JSON)
    $fields = [
        'input_text_position',
        'input_text_experience',
        'input_text_locations',
        'input_text_boards',
        'textarea_super_power',
        'img_link_data_banner',
        'point_data_favourite_exercise',
        'point_data_my_specialty',
        'hl_data_my_experience',
        'hl_data_my_wlocation',
    ];

    foreach ($fields as $key) {
        if (array_key_exists($key, $meta)) {
            update_user_meta($user->ID, $key, $meta[$key]);
        }
    }
}
