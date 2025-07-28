<?php
function register_user_categories_taxonomy() {
    // Додаємо підтримку таксономій для користувачів
    add_action('registered_post_type', function($post_type, $post_type_object) {
        if ($post_type === 'user') {
            register_taxonomy_for_object_type('user_category', 'user');
        }
    }, 10, 2);

    register_taxonomy(
        'user_category',
        'user',
        array(
            'public'            => false, // Змінено на false для користувачів
            'show_in_menu'      => false, // Користувачі не відображаються в меню постів
            'labels'            => array(
                'name'          => 'Категорії користувачів',
                'singular_name' => 'Категорія користувача',
                'menu_name'     => 'Категорії користувачів',
                'add_new_item'  => 'Додати нову категорію',
                'edit_item'     => 'Редагувати категорію',
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => false, // Не працює для користувачів
            'rewrite'           => false, // Відключаємо для користувачів
            'capabilities'      => array(
                'manage_terms' => 'manage_options',
                'edit_terms'   => 'manage_options',
                'delete_terms' => 'manage_options',
                'assign_terms' => 'edit_users',
            ),
        )
    );
}
add_action('init', 'register_user_categories_taxonomy');

// Додаємо підтримку таксономій для користувачів
add_action('init', function() {
    register_taxonomy_for_object_type('user_category', 'user');
});

add_action('show_user_profile', 'show_user_category_field');
add_action('edit_user_profile', 'show_user_category_field');

function show_user_category_field($user) {
    if (!current_user_can('edit_users') && get_current_user_id() !== $user->ID) {
        return;
    }

    $terms = get_terms(array(
        'taxonomy' => 'user_category',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ));

    if (is_wp_error($terms) || empty($terms)) {
        echo '<p>Категорії не знайдено. <a href="' . admin_url('edit-tags.php?taxonomy=user_category') . '">Створити категорії</a></p>';
        return;
    }

    $user_terms = wp_get_object_terms($user->ID, 'user_category', array('fields' => 'ids'));
    if (is_wp_error($user_terms)) {
        $user_terms = array();
    }
    ?>
    <h3>Категорія користувача</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_category">Оберіть категорію:</label></th>
            <td>
                <select name="user_category[]" id="user_category" multiple style="min-width: 200px; height: 100px;">
                    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php echo in_array($term->term_id, $user_terms) ? 'selected="selected"' : ''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Утримуйте Ctrl (або Cmd на Mac) для вибору декількох категорій.</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'save_user_category_field');
add_action('edit_user_profile_update', 'save_user_category_field');

function save_user_category_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    // Перевіряємо nonce для безпеки (рекомендується додати)
    $cats = isset($_POST['user_category']) ? array_map('intval', $_POST['user_category']) : array();

    // Очищуємо існуючі терми та встановлюємо нові
    $result = wp_set_object_terms($user_id, $cats, 'user_category', false);

    if (is_wp_error($result)) {
        error_log('Помилка збереження категорій користувача: ' . $result->get_error_message());
    }
}

// Додаємо колонку в таблицю користувачів (опціонально)
add_filter('manage_users_columns', 'add_user_category_column');
function add_user_category_column($columns) {
    $columns['user_category'] = 'Категорії';
    return $columns;
}

add_action('manage_users_custom_column', 'show_user_category_column_content', 10, 3);
function show_user_category_column_content($value, $column_name, $user_id) {
    if ($column_name === 'user_category') {
        $terms = wp_get_object_terms($user_id, 'user_category');
        if (!is_wp_error($terms) && !empty($terms)) {
            $term_names = array_map(function($term) {
                return $term->name;
            }, $terms);
            return implode(', ', $term_names);
        }
        return '—';
    }
    return $value;
}

// Додаємо меню для управління категоріями
add_action('admin_menu', 'add_user_category_admin_menu');
function add_user_category_admin_menu() {
    add_users_page(
        'Категорії користувачів',
        'Категорії користувачів',
        'manage_options',
        'edit-tags.php?taxonomy=user_category'
    );
}