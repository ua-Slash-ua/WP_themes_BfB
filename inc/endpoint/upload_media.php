<?php

// Реєструємо REST API ендпоінт для завантаження медіа файлів
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/upload-media', array(
        'methods' => 'POST',
        'callback' => 'handle_media_upload_endpoint',
        'permission_callback' => 'validate_jwt_token_permission',
    ));
});

function validate_jwt_token_permission($request)
{
    error_log('=== validate_jwt_token_permission START ===');

    // Отримуємо токен з параметрів запиту
    $token = $request->get_param('token');

    if (!$token) {
        error_log('No token provided');
        return new WP_Error('no_token', 'Token is required', array('status' => 401));
    }

    error_log('Token received: ' . substr($token, 0, 20) . '...');

    // Спробуємо декодувати токен локально
    try {
        $token_parts = explode('.', $token);
        if (count($token_parts) !== 3) {
            throw new Exception('Invalid token structure');
        }

        // Декодуємо payload
        $payload_encoded = $token_parts[1];
        $payload_encoded .= str_repeat('=', (4 - strlen($payload_encoded) % 4) % 4);

        $payload = json_decode(base64_decode($payload_encoded), true);

        if (!$payload) {
            throw new Exception('Cannot decode token');
        }

        error_log('Token payload: ' . json_encode($payload));

        // Перевіряємо чи токен не прострочений
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            error_log('Token expired');
            return new WP_Error('token_expired', 'Token has expired', array('status' => 401));
        }

        // Знаходимо user_id
        $user_id = null;

        if (isset($payload['data']['user']['id'])) {
            $user_id = $payload['data']['user']['id'];
        } elseif (isset($payload['user_id'])) {
            $user_id = $payload['user_id'];
        } elseif (isset($payload['sub'])) {
            $user_id = $payload['sub'];
        }

        if (!$user_id) {
            error_log('User ID not found in token payload. Available keys: ' . implode(', ', array_keys($payload)));
            return new WP_Error('no_user_id', 'User ID not found in token', array('status' => 401));
        }

        error_log('User ID from token: ' . $user_id);

        // Перевіряємо чи існує користувач
        $user = get_user_by('id', $user_id);
        if (!$user) {
            error_log('User not found: ' . $user_id);
            return new WP_Error('user_not_found', 'User not found', array('status' => 404));
        }

        // Перевіряємо права на завантаження файлів
        if (!user_can($user, 'upload_files') && !user_can($user, 'edit_posts')) {
            error_log('User does not have upload permissions: ' . $user_id);
            return new WP_Error('insufficient_permissions', 'User does not have permission to upload files', array('status' => 403));
        }

        // Зберігаємо дані користувача
        global $validated_user_data;
        $validated_user_data = array(
            'user_id' => $user_id,
            'user' => $user,
            'token_payload' => $payload
        );

        error_log('=== validate_jwt_token_permission END === User validated: ' . $user_id);
        return true;

    } catch (Exception $e) {
        error_log('Error processing token: ' . $e->getMessage());
        return new WP_Error('token_error', 'Error processing token: ' . $e->getMessage(), array('status' => 401));
    }
}

function handle_media_upload_endpoint($request)
{
    error_log('=== handle_media_upload_endpoint START ===');

    global $validated_user_data;
    $user_id = $validated_user_data['user_id'];

    $field_type = sanitize_text_field($request->get_param('field_type'));
    $files = $request->get_file_params();

    error_log('Field type: ' . $field_type);
    error_log('User ID: ' . $user_id);

    // Валідація field_type
    $allowed_field_types = array(
        'img_link_data_gallery_',
        'img_link_data_avatar',
        'img_link_data_certificate'
    );

    if (!in_array($field_type, $allowed_field_types)) {
        return new WP_Error('invalid_field_type', 'Invalid field type: ' . $field_type, array('status' => 400));
    }

    if (!isset($files['files']) || empty($files['files']['name'])) {
        return new WP_Error('no_files', 'No files provided', array('status' => 400));
    }

    // Нормалізуємо структуру файлів
    $normalized_files = array();

    if (is_array($files['files']['name'])) {
        // Множинні файли
        for ($i = 0; $i < count($files['files']['name']); $i++) {
            if (!empty($files['files']['name'][$i])) {
                $normalized_files[] = array(
                    'name' => $files['files']['name'][$i],
                    'type' => $files['files']['type'][$i],
                    'tmp_name' => $files['files']['tmp_name'][$i],
                    'error' => $files['files']['error'][$i],
                    'size' => $files['files']['size'][$i]
                );
            }
        }
    } else {
        // Один файл
        if (!empty($files['files']['name'])) {
            $normalized_files[] = array(
                'name' => $files['files']['name'],
                'type' => $files['files']['type'],
                'tmp_name' => $files['files']['tmp_name'],
                'error' => $files['files']['error'],
                'size' => $files['files']['size']
            );
        }
    }

    if (empty($normalized_files)) {
        return new WP_Error('no_valid_files', 'No valid files found', array('status' => 400));
    }

    error_log('Number of files to process: ' . count($normalized_files));

    $uploaded_files = array();
    $errors = array();

    foreach ($normalized_files as $file_data) {
        if ($file_data['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload error for file: ' . $file_data['name'];
            continue;
        }

        $result = process_single_file($file_data, $user_id, $field_type);

        if (is_wp_error($result)) {
            $errors[] = $file_data['name'] . ': ' . $result->get_error_message();
        } else {
            $uploaded_files[] = $result;
        }
    }

    if (empty($uploaded_files) && !empty($errors)) {
        error_log('All uploads failed: ' . implode(', ', $errors));
        return new WP_Error('upload_failed', 'All uploads failed: ' . implode(', ', $errors), array('status' => 400));
    }

    // Отримуємо поточне значення поля користувача після обробки всіх файлів
    $current_field_value = get_user_meta($user_id, $field_type, true);

    $response = array(
        'success' => true,
        'field_type' => $field_type,
        'processed_count' => count($uploaded_files),
        'files' => $uploaded_files,
        'user_id' => $user_id,
        'current_field_value' => $current_field_value
    );

    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    error_log('=== handle_media_upload_endpoint END === Success: ' . count($uploaded_files) . ' files processed');

    return rest_ensure_response($response);
}

function process_single_file($file_data, $user_id, $field_type)
{
    error_log('=== process_single_file START ===');
    error_log('File: ' . $file_data['name'] . ', User: ' . $user_id . ', Field: ' . $field_type);
    error_log('File type: ' . $file_data['type']);

    // Перевіряємо розмір файлу (максимум 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file_data['size'] > $max_size) {
        error_log('File too large: ' . $file_data['size'] . ' bytes');
        return new WP_Error('file_too_large', 'File size exceeds 10MB limit', array('status' => 400));
    }

    // Перевіряємо тип файлу
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg');
    if (!in_array($file_data['type'], $allowed_types)) {
        error_log('Invalid file type: ' . $file_data['type']);
        return new WP_Error('invalid_file_type', 'Only image files are allowed. Received: ' . $file_data['type'], array('status' => 400));
    }

    // Генеруємо хеш файлу для перевірки дублікатів
    $file_hash = md5_file($file_data['tmp_name']);
    $file_size = $file_data['size'];

    error_log('File hash: ' . $file_hash . ', size: ' . $file_size);

    // Перевіряємо чи файл вже існує
    $existing_attachment = check_existing_file($file_hash, $file_size);

    if ($existing_attachment) {
        error_log('File already exists, using existing attachment: ' . $existing_attachment['id']);

        // Файл вже існує, додаємо його URL до поля користувача
        $attachment_url = $existing_attachment['url'];
        $attachment_id = $existing_attachment['id'];

        // Оновлюємо мета-дані користувача
        $updated = update_user_field_with_url($user_id, $field_type, $attachment_url);

        if (is_wp_error($updated)) {
            return $updated;
        }

        return array(
            'id' => $attachment_id,
            'url' => $attachment_url,
            'filename' => basename($attachment_url),
            'status' => 'existing'
        );
    }

    // Файл не існує, завантажуємо новий
    $attachment_result = upload_new_file($file_data, $user_id, $file_hash, $file_size);

    if (is_wp_error($attachment_result)) {
        return $attachment_result;
    }

    // Оновлюємо мета-дані користувача
    $updated = update_user_field_with_url($user_id, $field_type, $attachment_result['url']);

    if (is_wp_error($updated)) {
        return $updated;
    }

    error_log('=== process_single_file END === New file uploaded: ' . $attachment_result['id']);

    return array(
        'id' => $attachment_result['id'],
        'url' => $attachment_result['url'],
        'filename' => $attachment_result['filename'],
        'status' => 'uploaded'
    );
}

function check_existing_file($file_hash, $file_size)
{
    global $wpdb;

    // Шукаємо attachment з таким же хешем і розміром
    $query = $wpdb->prepare("
        SELECT p.ID, p.guid 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id 
        INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id 
        WHERE p.post_type = 'attachment' 
        AND pm1.meta_key = '_file_hash' 
        AND pm1.meta_value = %s 
        AND pm2.meta_key = '_file_size' 
        AND pm2.meta_value = %s
        LIMIT 1
    ", $file_hash, $file_size);

    $result = $wpdb->get_row($query);

    if ($result) {
        // Перевіряємо чи файл все ще існує
        $attachment_url = wp_get_attachment_url($result->ID);
        if ($attachment_url) {
            return array(
                'id' => $result->ID,
                'url' => $attachment_url
            );
        }
    }

    return false;
}

function upload_new_file($file_data, $user_id, $file_hash, $file_size)
{
    error_log('=== upload_new_file START ===');

    // Тимчасово встановлюємо $_FILES для media_handle_upload
    $temp_files = $_FILES;
    $_FILES['upload_file'] = $file_data;

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attachment_id = media_handle_upload('upload_file', 0, array(
        'post_title' => sanitize_file_name(pathinfo($file_data['name'], PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit'
    ));

    // Відновлюємо $_FILES
    $_FILES = $temp_files;

    if (is_wp_error($attachment_id)) {
        error_log('media_handle_upload error: ' . $attachment_id->get_error_message());
        return $attachment_id;
    }

    // Додаємо мета-дані для відстеження дублікатів
    update_post_meta($attachment_id, '_file_hash', $file_hash);
    update_post_meta($attachment_id, '_file_size', $file_size);
    update_post_meta($attachment_id, '_uploaded_by_user', $user_id);
    update_post_meta($attachment_id, '_upload_timestamp', current_time('timestamp'));

    $attachment_url = wp_get_attachment_url($attachment_id);

    error_log('=== upload_new_file END === Attachment created: ' . $attachment_id);

    return array(
        'id' => $attachment_id,
        'url' => $attachment_url,
        'filename' => basename($attachment_url)
    );
}

function update_user_field_with_url($user_id, $field_type, $new_url)
{
    error_log('=== update_user_field_with_url START ===');
    error_log('User ID: ' . $user_id . ', Field: ' . $field_type . ', URL: ' . $new_url);

    $current_value_raw = get_user_meta($user_id, $field_type, true);
    $current_value = [];

    // Якщо вже JSON-рядок — розпарсимо
    if (is_string($current_value_raw)) {
        $decoded = json_decode($current_value_raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $current_value = $decoded;
        } elseif (!empty($current_value_raw)) {
            $current_value = [$current_value_raw];
        }
    }

    error_log('Current field value: ' . json_encode($current_value));

    $updated_value = null;

    switch ($field_type) {
        case 'img_link_data_gallery_':
        case 'img_link_data_certificate':
            if (!in_array($new_url, $current_value)) {
                $current_value[] = $new_url;
                $updated_value = json_encode($current_value); // 👉 зберігаємо як JSON-рядок
            }
            break;

        default:
            // Для інших просто зберігаємо як рядок
            if ($current_value_raw !== $new_url) {
                $updated_value = $new_url;
            }
            break;
    }

    if ($updated_value !== null) {
        $result = update_user_meta($user_id, $field_type, $updated_value);
        if ($result === false) {
            error_log('❌ Failed to update meta');
            return new WP_Error('meta_update_failed', 'Failed to update user meta', array('status' => 500));
        }

        error_log('✅ Updated user meta: ' . $updated_value);
    } else {
        error_log('ℹ️ No update needed');
    }

    error_log('=== update_user_field_with_url END ===');
    return true;
}
