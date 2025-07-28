<?php

define('THEME_PATH',get_template_directory());
define('THEME_URI',get_template_directory_uri());

// Include <helper_func>
require THEME_PATH . '/inc/helper_func/hf_base_auth.php';
require THEME_PATH . '/inc/helper_func/hf_sanitize_checkbox.php';
require THEME_PATH . '/inc/helper_func/hf_sanitize_svg.php';
require THEME_PATH . '/inc/helper_func/hf_normalize_to_array.php';
require THEME_PATH . '/inc/helper_func/hf_normalize_array_or_string.php';
require THEME_PATH . '/inc/helper_func/hf_enqueue_media_uploader.php';
require THEME_PATH . '/inc/helper_func/hf_create_meta_field_config.php';
require THEME_PATH . '/inc/helper_func/reorder_theme_settings_sub_menu.php';

// Include <admin_panel>
require THEME_PATH . '/inc/admin_panel/theme_settings.php';
require THEME_PATH . '/inc/admin_panel/ap_faq.php';
require THEME_PATH . '/inc/admin_panel/ap_banner.php';
require THEME_PATH . '/inc/admin_panel/ap_events.php';

// Include <endpoint>
require THEME_PATH . '/inc/endpoint/upload_media.php';
// Include <custom_functions>
require THEME_PATH . '/inc/custom_functions/filter_user_login.php';



// Include <taxonomy>
require THEME_PATH . '/inc/taxonomy/user_category.php';


// Include <custom_labels>
require THEME_PATH . '/inc/custom_label/new_role_coach.php';
require THEME_PATH . '/inc/custom_label/new_role_partner.php';

require THEME_PATH . '/inc/custom_label/coach_label.php';

// Include <other>

add_theme_support('post-thumbnails');