<?php
add_action('init', function () {
// Роль: Тренер / Коуч
add_role(
    'bfb_coach',
    'Тренер',
    [
        'read' => true,
        'edit_posts' => true, // Можна редагувати свої пости
        'upload_files' => true,
        'edit_published_posts' => true,
    ]
);
});