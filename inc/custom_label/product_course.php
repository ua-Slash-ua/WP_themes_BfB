<?php

$fields_product_course = [
    create_meta_field_config('point_data_course_themes', 'Course_themes', 'sanitize_text_field', 'normalize_to_array'),
    create_meta_field_config('point_data_course_what_learn', 'What_learn', 'sanitize_text_field', 'normalize_to_array'),
    create_meta_field_config('point_data_course_include', 'Course_include', 'sanitize_text_field', 'normalize_to_array'),

    create_meta_field_config('hl_data_course_program', 'Course_program', '', 'normalize_to_array'),

    create_meta_field_config('input_date_date_start', 'Date_start'),
    create_meta_field_config('input_text_duration', 'Duration'),
    create_meta_field_config('input_text_blocks', 'Blocks'),
    create_meta_field_config('input_text_online_lessons', 'Online_lessons'),

];
function product_course_callback($post)
{
    $point_course_themes = get_post_meta($post->ID, 'point_data_course_themes', true);
    $point_course_what_learn = get_post_meta($post->ID, 'point_data_course_what_learn', true);
    $point_course_include = get_post_meta($post->ID, 'point_data_course_include', true);

    $hl_course_program = get_post_meta($post->ID, 'hl_data_course_program', true);

    $input_date_date_start = get_post_meta($post->ID, 'input_date_date_start', true);
    $input_text_duration = get_post_meta($post->ID, 'input_text_duration', true);
    $input_text_blocks = get_post_meta($post->ID, 'input_text_blocks', true);
    $input_text_online_lessons = get_post_meta($post->ID, 'input_text_online_lessons', true);

    ?>
    <div class="mtab_hero">
        <ul class="mtab_header">
            <li class="mtab_header_item tab_active" id="main">Основне</li>
            <li class="mtab_header_item tab_active" id="program">Програма курсу</li>
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
                <div class="form-container-point">
                    <div class="point_hero" id="point_hero_course_what_learn">
                        <div class="point-edit">
                            <label for="point_input_course_what_learn">Чого ви навчитесь?
                                <input type="text" id="point_input_course_what_learn" class="point_input">
                            </label>
                            <input type="button" value="+" id="point_add_course_what_learn" class="point_add">
                        </div>

                        <label for="point_data_course_what_learn">
                            <input type="text" hidden="hidden" id="point_data_course_what_learn"
                                   name="point_data_course_what_learn"
                                   value="<?php echo esc_attr($point_course_what_learn); ?>">
                        </label>
                        <div id="point_container_course_what_learn" class="point_container">

                        </div>
                    </div>
                </div>
                <div class="form-container-point">
                    <div class="point_hero" id="point_hero_course_include">
                        <div class="point-edit">
                            <label for="point_input_course_include">Цей курс включає:
                                <input type="text" id="point_input_course_include" class="point_input">
                            </label>
                            <input type="button" value="+" id="point_add_course_include" class="point_add">
                        </div>

                        <label for="point_data_course_include">
                            <input type="text" hidden="hidden" id="point_data_course_include"
                                   name="point_data_course_include"
                                   value="<?php echo esc_attr($point_course_include); ?>">
                        </label>
                        <div id="point_container_course_include" class="point_container">

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
                       id="input_text_duration" class="input_text-item">

                <label for="input_text_blocks">Блоки</label>
                <input type="text" value="<?php echo esc_attr($input_text_blocks); ?>" name="input_text_blocks"
                       id="input_text_blocks" class="input_text-item">

                <label for="input_text_online_lessons">Онлайн заняття</label>
                <input type="number" value="<?php echo esc_attr($input_text_online_lessons); ?>" name="input_text_online_lessons"
                       id="input_text_online_lessons" class="input_text-item">
            </div>
        </div>
        <div class="mtab_content_item " id="content_program">
            <div class="form-container-hl">
                <div id="container-hl-course_program" class="container-hl">
                    <h1>Contact</h1>
                    <div class="container-hl-add">
                        <input type="text" name="hl_data_course_program" id="hl_data_course_program"
                               value="<?php echo esc_attr($hl_course_program); ?>"
                               hidden="hidden">
                        <label for="hl_input_text_title">Назва теми</label>
                        <input type="text" id="hl_input_text_title" class="input_text-item">
                        <label for="hl_input_text_lesson_count">Кількість уроків</label>
                        <input type="text" id="hl_input_text_lesson_count" class="input_text-item">
                        <label for="hl_textarea_description">Опис</label>
                        <textarea id="hl_textarea_description" cols="30" rows="10"></textarea>
                        <label for="hl_textarea_themes">Туми ( роздільник |||)</label>
                        <textarea id="hl_textarea_themes" cols="30" rows="10"></textarea>
                        <input type="button" id="hl_btn_add_course_program" value="Add">
                    </div>
                    <div class="container-hl-preview">

                    </div>
                </div>

            </div>
        </div>
    </div>


    <?php
}

add_action('save_post_product', 'save_post_course_meta');
function save_post_course_meta($post_id)
{
    global $fields_product_course;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach ($fields_product_course as $field) {
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
