<?php

if (!defined('ABSPATH')) {
    exit;
}

//load helper classes
require_once(EC_WOO_BUILDER_PATH . '/includes/helper.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/general-settings.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/load-defaults.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/email-core.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/custom-shortcode.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/helper/init.php');

if (class_exists('EC_General_Settings')) {
    $generalSettings=new EC_General_Settings();
}
if (class_exists('EC_Load_Defaults')) {
    $defaults=new EC_Load_Defaults();
}

if (class_exists('EC_Helper_Ajax')) {
    $helper_ajax=new EC_Helper_Ajax();
}
$ec_helper=new EC_Helper();

add_action('wp_ajax_save_panel_position', array($helper_ajax, 'save_panel_position'));
add_action('wp_ajax_export_html', array($helper_ajax, 'export_html'));
add_action('wp_ajax_export_all', array($helper_ajax, 'export_all'));
add_action('wp_ajax_export_json', array($helper_ajax, 'export_json'));
add_action('wp_ajax_import_json', array($helper_ajax, 'import_json'));
add_action('wp_ajax_import_all', array($helper_ajax, 'import_all'));
add_action('wp_ajax_send_email', array($helper_ajax, 'send_email'));
add_action('wp_ajax_template_load', array($helper_ajax, 'template_load'));
add_action('wp_ajax_template_save', array($helper_ajax, 'template_save'));
add_action('wp_ajax_template_new_save', array($helper_ajax, 'template_new_save'));
add_action('wp_ajax_template_load_saved', array($helper_ajax, 'template_load_saved'));
add_action('wp_ajax_template_delete_saved', array($helper_ajax, 'template_delete_saved'));
add_action('wp_ajax_template_save_as', array($helper_ajax, 'template_save_as'));
add_action('wp_ajax_save_settings', array($helper_ajax, 'save_settings'));
add_action('wp_ajax_save_custom_css', array($helper_ajax, 'save_custom_css'));
add_action('wp_ajax_activate_updates', array($helper_ajax, 'activate_updates'));
add_action('wp_ajax_skip_activate_updates', array($helper_ajax, 'skip_activate_updates'));
add_action('wp_ajax_generate_shortcode', array($helper_ajax, 'generate_shortcode'));
add_action('wp_ajax_save_settings_replace_email_type', array($helper_ajax, 'save_settings_replace_email_type'));
add_action('wp_ajax_save_related_items', array($helper_ajax, 'save_related_items'));

add_filter('wc_get_template', array($ec_helper, 'ec_woo_get_new_template'), 10, 5);
