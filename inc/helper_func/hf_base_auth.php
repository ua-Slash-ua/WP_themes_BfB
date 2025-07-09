<?php
function check_basic_auth_permission() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        return new WP_Error('unauthorized', 'Authentication required', ['status' => 401]);
    }

    $valid_user = BASE_AUTH_LOGIN; // 👈 Заміни на свій логін
    $valid_pass = BASE_AUTH_PASS; // 👈 Заміни на свій пароль

    if (
        $_SERVER['PHP_AUTH_USER'] !== $valid_user ||
        $_SERVER['PHP_AUTH_PW'] !== $valid_pass
    ) {
        return new WP_Error('forbidden', 'Invalid credentials', ['status' => 403]);
    }

    return true;
}