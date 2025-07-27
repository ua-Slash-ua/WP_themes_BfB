<?php
add_action('user_register', function($user_id) {
    global $wpdb;

    $user = get_userdata($user_id);
    if (!$user) return;

    $email_prefix = explode('@', $user->user_email)[0];

    $new_login = sanitize_user($email_prefix . '_' . $user_id);

    $wpdb->update(
        $wpdb->users,
        ['user_login' => $new_login],
        ['ID' => $user_id]
    );

    // Очищення кешу
    clean_user_cache($user_id);
});
