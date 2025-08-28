<?php

$fields_product_course = [
    create_meta_field_config('point_data_course_themes', 'Course_themes', 'sanitize_text_field', 'normalize_to_array'),
    create_meta_field_config('input_date_date_start', 'Date_start'),
    create_meta_field_config('input_text_duration', 'Duration'),

];
function product_course_callback($post)
{
    $point_course_themes = get_post_meta($post->ID, 'point_data_course_themes', true);
    $input_date_date_start = get_post_meta($post->ID, 'input_date_date_start', true);
    $input_text_duration = get_post_meta($post->ID, 'input_text_duration', true);

    ?>
    <div class="mtab_hero">
        <ul class="mtab_header">
            <li class="mtab_header_item tab_active" id="main">Основне</li>
            <li class="mtab_header_item tab_active" id="program">22222</li>
        </ul>
        <div class="mtab_content_item content_active" id="content_main">
            <div class="form-container-point">
                <div class="form-container-point">
                    <div class="point_hero" id="point_hero_course_themes">
                        <div class="point-edit">
                            <label for="point_input_course_themes">Які теми покриває курс:
                                <input type="text" id="point_input_course_themes" class="point_input">
                            </label>
                            <input type="button" value="+" id="point_add_course_themes" class="point_add">
                        </div>

                        <label for="point_data_course_themes">
                            <input type="text" hidden="hidden" id="point_data_course_themes"
                                   name="point_data_course_themes"
                                   value="<?php echo esc_attr($point_course_themes); ?>">
                        </label>
                        <div id="point_container_course_themes" class="point_container">

                        </div>
                    </div>
                </div>

            </div>
            <div class="form-container-input_date">
                <label for="input_date_date_start">Дата початку</label>
                <input type="date" value="<?php echo esc_attr($input_date_date_start); ?>"
                       name="input_date_date_start"
                       id="input_date_date_start" class="input_date-item">
            </div>
            <div class="form-container-input_text">
                <label for="input_text_duration">Тривалість</label>
                <input type="text" value="<?php echo esc_attr($input_text_duration); ?>" name="input_text_duration"
                       id="input_text_duration" class="input_еуче-item">
            </div>
        </div>
        <div class="mtab_content_item " id="content_program">

            asdasd
        </div>
    </div>


    <?php
}

add_action('save_post_product', 'save_post_course_meta');
function save_post_course_meta($post_id)
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
        error_log('$key = ' . $key . ' $value = ' . $value . '');
        // Оновлюємо мета-дані
        update_post_meta($post_id, $key, $value);
    }
}
