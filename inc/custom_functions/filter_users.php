<?php
// Допоміжна функція для нормалізації параметрів
function normalize_param_to_array($param) {
    if (empty($param)) {
        return [];
    }

    if (is_string($param)) {
        return [$param];
    }

    if (is_array($param)) {
        return $param;
    }

    return [];
}

add_filter('rest_user_query', function($args, $request) {
    // Ініціалізуємо meta_query
    if (!isset($args['meta_query'])) {
        $args['meta_query'] = ['relation' => 'AND'];
    }

    // 0) Фільтр по ролі
    $roles = normalize_param_to_array($request->get_param('roles'));
    if (!empty($roles)) {
        $args['role__in'] = $roles;
    }

    // 1) Фільтр по місту
    $cities = normalize_param_to_array($request->get_param('cities'));
    if (!empty($cities)) {
        $args['meta_query'][] = [
            'key'     => 'input_text_locations_city',
            'value'   => $cities,
            'compare' => 'IN'
        ];
    }

    // 2) Фільтр по країні
    $countries = normalize_param_to_array($request->get_param('countries'));
    if (!empty($countries)) {
        $args['meta_query'][] = [
            'key'     => 'input_text_locations_country',
            'value'   => $countries,
            'compare' => 'IN'
        ];
    }

    // 3) Фільтр по категоріям
    $categories = normalize_param_to_array($request->get_param('categories'));
    if (!empty($categories)) {
        if (!isset($args['tax_query'])) {
            $args['tax_query'] = [];
        }
        $args['tax_query'][] = [
            'taxonomy' => 'user_category',
            'field'    => 'term_id',
            'terms'    => $categories,
            'operator' => 'IN'
        ];
    }

    return $args;
}, 10, 2);
