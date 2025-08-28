<?php

add_action('add_meta_boxes', function() {
    global $post;

    if (!$post || $post->post_type !== 'product') {
        return;
    }

    $product = wc_get_product($post->ID);

    if ($product && $product->is_virtual()) {
        add_meta_box(
            'my_virtual_box',
            'Метабокс для курсів',
            'product_course_callback',
            'product',
            'normal',
            'default'
        );
    } else {
        add_meta_box(
            'my_regular_box',
            'Метабокс для звичайних товарів',
            'product_inventory_callback',
            'product',
            'normal',
            'default'
        );
    }
});

// Функція для підключення стилів та скриптів для адміністративної панелі типу запису "banner"
function enqueue_product_style_and_script($hook)
{

    global $post;

    if (!$post || $post->post_type !== 'product') {
        return;
    }

    $product = wc_get_product($post->ID);

    if ($product && $product->is_virtual()) {
        // Підключаємо стилі
        wp_enqueue_style(
            'product_course_style', // Унікальний ID для стилю
            get_template_directory_uri() . '/inc/admin_panel/ap_styles/product_course_style.css', // Шлях до файлу стилю
            [], // Массив залежностей, якщо немає - залишаємо порожнім
            '1.0.0' // Версія стилю
        );

        // Підключаємо скрипти
        wp_enqueue_script(
            'product_course_script', // Унікальний ID для скрипту
            get_template_directory_uri() . '/inc/admin_panel/ap_scripts/product_course_script.js', // Шлях до файлу скрипту
            ['jquery'], // Масив залежностей, наприклад jQuery
            '1.0.0', // Версія скрипту
            true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
        );
        enqueue_media_uploader();
    } else {
        // Підключаємо стилі
        wp_enqueue_style(
            'product_inventory_style', // Унікальний ID для стилю
            get_template_directory_uri() . '/inc/admin_panel/ap_styles/product_inventory_style.css', // Шлях до файлу стилю
            [], // Массив залежностей, якщо немає - залишаємо порожнім
            '1.0.0' // Версія стилю
        );

        // Підключаємо скрипти
        wp_enqueue_script(
            'product_inventory_script', // Унікальний ID для скрипту
            get_template_directory_uri() . '/inc/admin_panel/ap_scripts/product_inventory_script.js', // Шлях до файлу скрипту
            ['jquery'], // Масив залежностей, наприклад jQuery
            '1.0.0', // Версія скрипту
            true // Вказуємо, що скрипт потрібно підключити в кінці сторінки
        );
        enqueue_media_uploader();
    }

}

// Додаємо функцію до хуку для підключення стилів та скриптів в адмін-панелі
add_action('admin_enqueue_scripts', 'enqueue_product_style_and_script');





