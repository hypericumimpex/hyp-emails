<?php
/*
  Plugin Name:  HYP Emails
  Description:  Hyp Custom Emails.
  Plugin URI:   https://github.com/hypericumimpex/hyp-emails/
  Version:      2.2.11
  Author:       Romeo C.
  Author URI:   https://github.com/hypericumimpex/
  Text Domain:  ec-for-woo-with-drag-drop-builder
  Domain Path: /languages/
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('EC_WOO_BUILDER_SLUG')) {
    define('EC_WOO_BUILDER_SLUG', 'email-customizer-for-woocommerce-with-drag-drop-builder');
    define('EC_WOO_BUILDER_POST_TYPE', 'ec_woo_ddb_template');
    define('EC_WOO_BUILDER_POST_TYPE_CUSTOM_CODE', 'ecwoo_csc');
    define('EC_WOO_BUILDER_SHORTCODE_PRE', 'ec_woo_');
    define('EC_WOO_BUILDER_VERSION', '2.2.11');
    define('EC_WOO_BUILDER_FILE', __FILE__);
    define('EC_WOO_BUILDER_PATH', plugin_dir_path(__FILE__));
    define('EC_WOO_BUILDER_URL', plugin_dir_url(__FILE__));
    define('EC_WOO_BUILDER_PLUGIN_SLUG', plugin_basename(__FILE__));
    define('EC_WOO_BUILDER_TEXTDOMAIN', 'email-customizer-for-woocommerce-with-drag-drop-builder');
    define('EC_WOO_BUILDER_REQUIRED_WOO_VERSION', '2.4');
    define('EC_WOO_BUILDER_PREVIEW_PAGE', 'ecwoo_preview');
    define('EC_WOO_BUILDER_SHOW_ACTIVATE', 'yes');
    //
    //defaults
    define('EC_WOO_BUILDER_IMG', 32);
    define('EC_WOO_BUILDER_SHOW_IMAGE', 1);
    define('EC_WOO_BUILDER_SHOW_SKU', 1);
    define('EC_WOO_BUILDER_BORDER_PADDING', 3);
    define('EC_WOO_BUILDER_CUSTOM_CSS', '/*add your css here*/');
    define('EC_WOO_BUILDER_REPLACE_MAIL', 1);
    define('EC_WOO_BUILDER_RTL', 0);

    define('EC_WOO_BUILDER_RELATED_ITEMS_COLUMNS', 3);
    define('EC_WOO_BUILDER_RELATED_ITEMS_COUNT', 3);
    define('EC_WOO_BUILDER_RELATED_ITEMS_SHOW_NAME', 1);
    define('EC_WOO_BUILDER_RELATED_ITEMS_SHOW_PRICE', 1);
    define('EC_WOO_BUILDER_RELATED_ITEMS_SHOW_IMAGE', 1);
    define('EC_WOO_BUILDER_RELATED_ITEMS_BY', 'product_type');
    define('EC_WOO_BUILDER_SHOW_CUSTOM_SHORTCODE', 1);

    define('EC_WOO_BUILDER_SHOW_META', 1);
}

require_once(EC_WOO_BUILDER_PATH . '/includes/checking-compatibility.php');

if (!ec_woo_builder_is_woocommerce_active() || version_compare(get_option('woocommerce_version'), EC_WOO_BUILDER_REQUIRED_WOO_VERSION, '<')) {
    add_action('admin_notices', 'ec_woo_builder_woocommerce_inactive_message');
    return;
}

add_filter( 'wp_targeted_link_rel', '__return_false' );

require_once(EC_WOO_BUILDER_PATH . '/includes/helper.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/general-settings.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/load-defaults.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/email-core.class.php');
require_once(EC_WOO_BUILDER_PATH . '/includes/init.php');


if (!function_exists('ec_woo_builder_email_post_register')) {
    function ec_woo_builder_email_post_register()
    {
        $labels = array(
            'name' => _x('Email Template', 'post type general name'),
            'singular_name' => _x('Email Template', 'post type singular name'),
            'add_new' => _x('Add New Email Template', 'Team item'),
            'add_new_item' => __('Add a new post of type Email Template'),
            'edit_item' => __('Edit Email Template'),
            'new_item' => __('New Email Template'),
            'view_item' => __('View Email Template'),
            'search_items' => __('Search Email Template'),
            'not_found' => __('No Email Template found'),
            'not_found_in_trash' => __('No Email Template currently trashed'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => false,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => EC_WOO_BUILDER_POST_TYPE,
            'capabilities' => array(),
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'author', 'thumbnail')
        );
        register_post_type(EC_WOO_BUILDER_POST_TYPE, $args);
    }

    add_action('init', 'ec_woo_builder_email_post_register');
}

$ec_helper_activate = new Helper_Activation();
register_activation_hook(EC_WOO_BUILDER_FILE, array($ec_helper_activate, 'activate'));
register_deactivation_hook(EC_WOO_BUILDER_FILE, array($ec_helper_activate, 'deactivate'));




function ec_woo_add_query_vars_filter($vars)
{
    $vars[] = "page";
    return $vars;
}

add_filter('query_vars', 'ec_woo_add_query_vars_filter');

function remove_admin_notices()
{
    if (empty($_GET)) {
        return;
    }
    if (!isset($_GET['page'])) {
        return;
    }
    $page = sanitize_text_field($_GET['page']);
    if ($page == EC_WOO_BUILDER_SLUG) {
        remove_all_actions('admin_notices');
    }
}

add_action('admin_head', 'remove_admin_notices');

$purchase_code = get_option('ec_woo_purchase_code', '');
if (isset($purchase_code) && $purchase_code != '') {
    require_once(EC_WOO_BUILDER_PATH . '/includes/updater/updater.php');
    $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://emailcustomizer.com/api/update.php?key=' . $purchase_code . '&version=' . EC_WOO_BUILDER_VERSION,
        __FILE__,
        EC_WOO_BUILDER_SLUG
    );
}


$ec_woo_preview_mail = EC_WOO_Preview_Mail::get_instance();

