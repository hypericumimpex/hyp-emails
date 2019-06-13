<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper class
 */
class EC_Email_Core
{
    private $order_id = -1;
    private $email_type = '';
    private $shortcode_data;
    private $full_shortcode_data;
    private $post_helper;
    private $order;
    private $is_preview;

    public function __construct()
    {
      $this->post_helper = new EC_Helper_Posts();
      $this->is_preview=false;
    }

    public function get_order_id()
    {
        return $this->order_id;
    }

    public function is_preview($isPreview)
    {
        $this->is_preview = $isPreview;
    }
    public function set_order_id($id)
    {
        $this->order_id = $id;
    }

    public function get_email_type()
    {
        return $this->email_type;
    }

    public function set_email_type($type)
    {
        $this->email_type = $type;
    }

    public function shortcode_generate($atts, $content, $tag)
    {
        $tag = '[' . $tag . ']';
        if (!isset($this->shortcode_data[$tag])) {
            return '';
        }
        $value = $this->shortcode_data[$tag];
        if (isset($atts['format']) && isset($atts['type']) && $atts['type'] == 'date') {
            $datetime = strtotime($value);
            $new_dateformat = date($atts['format'], $datetime);
            return $new_dateformat;
        }

        return $value;
    }

    public function shortcode_init()
    {
        /*
        * General Shortcodes
        */
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'site_url', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'site_name', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'current_year', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'copyright', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'current_date', array($this, 'shortcode_generate'));

        /*
        * Order Shortcodes
        */
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_id', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_link', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'transaction_id', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_sub_total', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_payment_method', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_payment_url', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_total', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_fee', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_refund', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_date', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_time', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_datetime', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'view_order_url', array($this, 'shortcode_generate'));

        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_total_refunded_amount', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_formatted_total', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_remaining_refund_amount', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_total_amount', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_shipping', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_shipping_total', array($this, 'shortcode_generate'));

        //customer notes
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_notes_last_message', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_notes_last_month', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_notes', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_provided_note', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'payment_method', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items_1', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items_2', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items_3', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items_4', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items_5', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'items_6', array($this, 'shortcode_generate'));
        /*
        * User Shortcodes
        */
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_name', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_id', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_activation_link', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_password_reset_url', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_account_url', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_password', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_password_reset_url_2', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'user_account_url_2', array($this, 'shortcode_generate'));
        /*
        * Billing Shortcodes
        */
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_first_name', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_last_name', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_company', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_address', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_address_1', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_address_2', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_city', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_state', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_postcode', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_country', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_phone', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_email', array($this, 'shortcode_generate'));

        /*
        * Shipping Shortcodes
        */
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_first_name', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_last_name', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_method', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_company', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_address', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_address_1', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_address_2', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_city', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_state', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_postcode', array($this, 'shortcode_generate'));
        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_country', array($this, 'shortcode_generate'));

        add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'related_items', array($this, 'shortcode_generate'));


        $subs_data = new WooSubsLoader($this->order_id);
        if ($subs_data->hasData()) {
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_id', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_id_url', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_status', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_billing_period', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_billing_interval', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_start_date', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_end_date', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_next_payment_date', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_info_1', array($this, 'shortcode_generate'));
          add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_info_2', array($this, 'shortcode_generate'));
        }

        /*
         * Motors theme integration
         */

        if (defined('motors')) {
            add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_pickup_date', array($this, 'shortcode_generate'));
            add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_pickup_location', array($this, 'shortcode_generate'));
            add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_drop_date', array($this, 'shortcode_generate'));
            add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_drop_location', array($this, 'shortcode_generate'));
        }

        /*
        * Bank accounts
        */

        $bacs_info = EC_Helper::get_woo_bank_info();
        if ($bacs_info != false) {
            add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'bank_accounts', array($this, 'shortcode_generate'));

            $account_i = 0;
            foreach ($bacs_info as $account) {
                $account_i++;
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'account_name_' . $account_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'bank_name_' . $account_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'account_number_' . $account_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'routing_number_' . $account_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'iban_' . $account_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'bic_' . $account_i, array($this, 'shortcode_generate'));
            }
        }


        /*
        * WooCommerce Shipping Tracking
        */
        $woo_shipping_tracking_data = EC_Helper::get_woo_shipping_tracking_data($this->order_id);
        if ($woo_shipping_tracking_data != false) {
            $woo_shipping_i = 0;
            foreach ($woo_shipping_tracking_data as $shipping) {
                $woo_shipping_i++;
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_tracking_number_' . $woo_shipping_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_dispatch_date_' . $woo_shipping_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_custom_text_' . $woo_shipping_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_company_name_' . $woo_shipping_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_company_id_' . $woo_shipping_i, array($this, 'shortcode_generate'));
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_tracking_url_' . $woo_shipping_i, array($this, 'shortcode_generate'));
            }
        }


        $delivery_date = EC_Helper::get_order_delivery_date($this->get_order_number());
        if ($delivery_date != false) {
            add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . 'order_delivery_date', array($this, 'shortcode_generate'));
        }
        if (function_exists('wc_get_custom_checkout_fields') && !is_null($this->order)) {
            $custom_fields = wc_get_custom_checkout_fields($this->order);
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $key => $custom_field) {
                    add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . $key, array($this, 'shortcode_generate'));
                }
            }
        }

        $custom_fields_flexible_checkout = EC_Helper::getCustomFieldsOf_FCFP();
        if (!empty($custom_fields_flexible_checkout) && count($custom_fields_flexible_checkout) > 0) {
            foreach ($custom_fields_flexible_checkout as $key => $custom_fields_flexible_checkout_field) {
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . $key, array($this, 'shortcode_generate'));
            }
        }

        //custom shortcodes
        $custom_code_list = $this->post_helper->get_custom_codes();
        if (!empty($custom_code_list) && count($custom_code_list) > 0) {
            foreach ($custom_code_list as $item) {
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . $item->post_title, array($this, 'shortcode_generate'));
            }
        }

        $custom_fields_fcew = EC_Helper::get_custom_fields_flexible_checkout_editor_woo($this->order_id);
        if (!empty($custom_fields_fcew) && count($custom_fields_fcew) > 0) {
            foreach ($custom_fields_fcew as $key => $value) {
                add_shortcode(EC_WOO_BUILDER_SHORTCODE_PRE . $key, array($this, 'shortcode_generate'));
            }
        }
    }

    public function get_shortcode_data()
    {
        return $this->shortcode_data;
    }

    public function get_full_shortcode_data()
    {
        return $this->full_shortcode_data;
    }

    public function collect_data($args = array())
    {
        $_temp_data = array();
        $custom_code_list = $this->post_helper->get_custom_codes();
        if (!empty($custom_code_list) && count($custom_code_list) > 0) {
            foreach ($custom_code_list as $item) {
                $_temp_data['Custom Shortcode']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . $item->post_title . ']'] = $item->post_content;
            }
        }

        $_temp_data['General']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'site_name]'] = get_bloginfo('name');
        $_temp_data['General']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'current_date]'] = date("Y-m-d H:i:s");
        $_temp_data['General']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'site_url]'] = '<a href="' . site_url() . '"> ' . esc_html__('Visit Website', EC_WOO_BUILDER_TEXTDOMAIN) . ' </a>';
        $_temp_data['General']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'current_year]'] = date("Y");
        $_temp_data['General']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'copyright]'] = "Copyright © " . date("Y");
        $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_account_url]'] = '<a href="' . site_url() . '/my-account/"> ' . site_url() . '/my-account </a>';
        $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_account_url_2]'] = '<a class="ec-user-account-url" href="' . site_url() . '/my-account/"> ' . esc_html__('My account', EC_WOO_BUILDER_TEXTDOMAIN) . '</a>';
        if (!empty($args)) {
            if ($this->get_email_type() == 'customer_reset_password') {
                $_temp_data['Reset password']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_name]'] = $args['email']->user_login;
                $_temp_data['Reset password']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]'] = $args['email']->user_email;

                $resetURL = esc_url(add_query_arg(array('key' => $args['email']->reset_key, 'login' => rawurlencode($args['email']->user_login)), wc_get_endpoint_url('lost-password', '', wc_get_page_permalink('myaccount'))));
                $_temp_data['Reset password']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_password_reset_url]'] = '<a href=' . $resetURL . '>' . $resetURL . '</a>';
                $_temp_data['Reset password']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_password_reset_url_2]'] = '<a class="ec-customer-reset-password-url" href=' . $resetURL . '>' . esc_html__('Click here to reset your password', EC_WOO_BUILDER_TEXTDOMAIN) . '</a>';


                $this->convert_shortcode_data($_temp_data);
                $this->full_shortcode_data = $_temp_data;
            }
            if ($this->get_email_type() == 'customer_new_account' || $this->get_email_type() == 'customer_new_account_activation') {
                global $wpdb;
                $key = wp_generate_password(20, false);
                do_action('retrieve_password_key', $args['email']->user_login, $key);
                $_temp_data['New account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_name]'] = $args['email']->user_login;
                $_temp_data['New account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]'] = $args['email']->user_email;
                if (isset($args['email']->user_pass) && !empty($args['email']->user_pass)) {
                    $_temp_data['New account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_password]'] = $args['email']->user_pass;
                }


                $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $args['email']->user_login));
                $activation_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($args['email']->user_login), 'login');
                $_temp_data['New account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_activation_link]'] = $activation_url;

                //$this->shortcode_data = $_temp_data;
                $this->convert_shortcode_data($_temp_data);
                $this->full_shortcode_data = $_temp_data;
            }
        }
        if (is_null($this->order_id)) {
            return;
        }
        //  $_order = wc_get_order( $this->order_id );


        //if ($this->order_id && class_exists('WC_Order')) {
        //  $order = wc_get_order($this->order_id);
        //}
        if ($this->order_id && class_exists('WC_Order')) {
            $order = new WC_Order($this->order_id);
        }


        if (is_null($order)) {
            return;
        }

        $custom_fields_fcew = EC_Helper::get_custom_fields_flexible_checkout_editor_woo($this->order_id);
        if (!empty($custom_fields_fcew) && count($custom_fields_fcew) > 0) {
            foreach ($custom_fields_fcew as $key => $value) {
                $_temp_data['Flexible Woocommerce Checkout Field Editor']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . $key . ']'] = $value;
            }
        }

        $custom_fields_flexible_checkout = EC_Helper::getCustomFieldsOf_FCFP();
        if (!empty($custom_fields_flexible_checkout) && count($custom_fields_flexible_checkout) > 0) {
            foreach ($custom_fields_flexible_checkout as $key => $custom_fields_flexible_checkout_field) {
                $_temp_data['Flexible Checkout Fields']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . $key . ']'] = wpdesk_get_order_meta($order, $key, true);
            }
        }

        $this->order = $order;

        $order_items = $this->get_order_items($order->get_items('array'));

        $order_fee = 0;
        $order_refund = 0;

        $totals = $order->get_order_item_totals();
        foreach ($totals as $index => $value) {
            if (strpos($index, 'fee') !== false) {
                if (is_numeric($value['value'])) {
                    $order_fee += $value['value'];
                }
            }
            if (strpos($index, 'refund') !== false) {
                $order_refund += $value['value'];
            }
        }
        //unset($order_total);


        $order_data = $order->get_data();
        //User Info
        $user_data = $order->get_user();

        if (isset($user_data->user_nicename)) {
            $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_name]'] = $user_data->user_nicename;
        } else {
            $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_name]'] = $this->get_order_billing_first_name() . ' ' . $this->get_order_billing_last_name();
        }
        if (isset($user_data->user_email)) {
            $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]'] = $user_data->user_email;
        } else {
            $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]'] = $this->get_order_billing_email();
        }

        if (isset($user_data->user_email['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]']) && $_temp_data['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]'] != '') {
            $user = get_user_by('email', $_temp_data['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_email]']);
            $_temp_data['User Info']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'user_id]'] = (isset($user->ID)) ? $user->ID : '';
        }


        //Order totals
        if (isset($totals['cart_subtotal']['value'])) {
            $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_sub_total]'] = $totals['cart_subtotal']['value'];
        } else {
            $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_sub_total]'] = '';
        }
        if (isset($totals['payment_method']['value'])) {
            $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_payment_method]'] = $totals['payment_method']['value'];
        } else {
            $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_payment_method]'] = '';
        }
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_total]'] = $this->get_order_total($order);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_fee]'] = $order_fee;
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_refund]'] = $order_refund;
        //$order->add_rate();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_shipping]'] = $order->calculate_shipping();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_shipping_total]'] = $order_data['shipping_total'];

        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_payment_url]'] = esc_url($order->get_checkout_payment_url());
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_payment_url]'] = '<a href="' . esc_url($order->get_checkout_payment_url()) . '">' . esc_html__('Payment page', EC_WOO_BUILDER_TEXTDOMAIN) . '</a>';

        //Order Info

        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_id]'] = $this->get_order_number();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_link]'] = '<a href="' . $order->get_view_order_url() . '">[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_id]</a>';
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_link]'] = str_replace('[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_id]', $this->get_order_number(), $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_link]']);
        $order_date = strtotime($order->get_date_created());
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_date]'] = date('M d, Y', $order_date);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_time]'] = date('H:i:s', $order_date);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_datetime]'] = $order->get_date_created();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'payment_method]'] = $this->get_order_payment_method_title();

        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'view_order_url]'] = '<a href="' . $order->get_view_order_url() . '" >' . $order->get_view_order_url() . '</a>';
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'transaction_id]'] = $order->get_transaction_id();


        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_total_refunded_amount]'] = $order->get_total_refunded();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_formatted_total]'] = $order->get_formatted_order_total();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_remaining_refund_amount]'] = $order->get_remaining_refund_amount();
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_total_amount]'] = $order->get_total();

        $customer_notes = $order->get_customer_order_notes();

        $customer_note_result = '';
        $customer_note_result_last_month = '';
        $customer_note_result_last_message = '';
        if (!empty($customer_notes) && count($customer_notes)) {
            $customer_note_result = $this->get_order_customer_notes($customer_notes);

            EC_Helper::array_sort_bycolumn($customer_notes, 'comment_date', 'desc');

            $customer_note_result_last_month = $this->get_order_customer_notes($customer_notes);

            $customer_note_result_last_message = $this->get_order_customer_notes(array($customer_notes[0]));
        }
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_notes]'] = $customer_note_result;
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_notes_last_message]'] = $customer_note_result_last_message;
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_notes_last_month]'] = $customer_note_result_last_month;

        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'customer_provided_note]'] = $order->get_customer_note();

        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items]'] = $this->get_order_items_template($order, $order_items);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items_1]'] = $this->get_order_items_template_1($order, $order_items);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items_2]'] = $this->get_order_items_template_2($order, $order_items);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items_3]'] = $this->get_order_items_template_3($order, $order_items);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items_4]'] = $this->get_order_items_template_4($order, $order_items);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items_5]'] = $this->get_order_items_template_5($order, $order_items);
        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'items_6]'] = $this->get_order_items_template_6($order, $order_items);

        $_temp_data['Order']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'related_items]'] = $this->get_related_products_grid($order);

        //Address Details
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_address]'] = $order->get_formatted_billing_address();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_first_name]'] = $this->get_order_billing_first_name();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_last_name]'] = $this->get_order_billing_last_name();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_company]'] = $this->get_billing_company();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_address_1]'] = $this->get_billing_address_1();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_address_2]'] = $this->get_billing_address_2();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_city]'] = $this->get_billing_city();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_state]'] = $this->get_billing_state();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_postcode]'] = $this->get_billing_postcode();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_country]'] = $this->get_billing_country();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_phone]'] = $this->get_billing_phone();
        $_temp_data['Billing']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'billing_email]'] = $this->get_order_billing_email();

        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_address]'] = $order->get_formatted_shipping_address();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_first_name]'] = $this->get_shipping_first_name();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_last_name]'] = $this->get_shipping_last_name();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_company]'] = $this->get_shipping_company();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_address_1]'] = $this->get_shipping_address_1();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_address_2]'] = $this->get_shipping_address_2();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_city]'] = $this->get_shipping_city();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_state]'] = $this->get_shipping_state();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_postcode]'] = $this->get_shipping_postcode();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_country]'] = $this->get_shipping_country();
        $_temp_data['Shipping']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'shipping_method]'] = $order->get_shipping_method();


        //WooCommerce Subscription info
        $subs_data = new WooSubsLoader($this->order_id);
        if ($subs_data->hasData()) {
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_id]'] = $subs_data->get_subscription_id();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_id_url]'] = $subs_data->get_subscription_id_url();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_status]'] = $subs_data->get_subscription_status();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_billing_period]'] = $subs_data->get_billing_period();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_billing_interval]'] = $subs_data->get_billing_interval();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_start_date]'] = $subs_data->get_subscription_start_date();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_end_date]'] = $subs_data->get_subscription_end_date();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_next_payment_date]'] = $subs_data->get_subscription_next_payment_date();
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_info_1]'] = $this->get_wcs_info_1($subs_data,$order->get_formatted_order_total());
          $_temp_data['Woo Subscription']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcs_info_2]'] = $this->get_wcs_info_2($subs_data,$order->get_formatted_order_total());
        }


        /*
         * Motors theme integration
         */

        if (defined('motors')) {
            $orderPD = get_post_meta($order->get_id(), 'order_pickup_date', true);
            $orderPL = get_post_meta($order->get_id(), 'order_pickup_location', true);
            $orderDD = get_post_meta($order->get_id(), 'order_drop_date', true);
            $orderDL = get_post_meta($order->get_id(), 'order_drop_location', true);
            $_temp_data['Motors Theme']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_pickup_date]'] = $orderPD;
            $_temp_data['Motors Theme']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_pickup_location]'] = $orderPL;
            $_temp_data['Motors Theme']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_drop_date]'] = $orderDD;
            $_temp_data['Motors Theme']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_drop_location]'] = $orderDL;
        }

        $bacs_info = EC_Helper::get_woo_bank_info();
        if ($bacs_info != false) {
            $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'bank_accounts]'] = $this->get_bacs_account_details_html();
            $account_i = 0;
            foreach ($bacs_info as $account) {
                $account_i++;
                $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'account_name_' . $account_i . ']'] = esc_attr(wp_unslash($account['account_name']));
                $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'bank_name_' . $account_i . ']'] = esc_attr(wp_unslash($account['bank_name']));
                $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'account_number_' . $account_i . ']'] = esc_attr($account['account_number']);
                $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'routing_number_' . $account_i . ']'] = esc_attr($account['sort_code']);
                $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'iban_' . $account_i . ']'] = esc_attr($account['iban']);
                $_temp_data['Bank Account']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'bic_' . $account_i . ']'] = esc_attr($account['bic']);
            }
        }

        $woo_shipping_tracking_data = EC_Helper::get_woo_shipping_tracking_data($this->order_id);
        if ($woo_shipping_tracking_data != false) {
            $woo_shipping_i = 0;
            foreach ($woo_shipping_tracking_data as $shipping) {
                $woo_shipping_i++;
                $_temp_data['Woo Shipping Tracking']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_tracking_number_' . $woo_shipping_i . ']'] = esc_attr($shipping['tracking_number']);
                $_temp_data['Woo Shipping Tracking']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_dispatch_date_' . $woo_shipping_i . ']'] = esc_attr($shipping['dispatch_date']);
                $_temp_data['Woo Shipping Tracking']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_custom_text_' . $woo_shipping_i . ']'] = esc_attr($shipping['custom_text']);
                $_temp_data['Woo Shipping Tracking']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_company_name_' . $woo_shipping_i . ']'] = esc_attr($shipping['company_name']);
                $_temp_data['Woo Shipping Tracking']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_company_id_' . $woo_shipping_i . ']'] = esc_attr($shipping['company_id']);
                $_temp_data['Woo Shipping Tracking']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'wcst_tracking_url_' . $woo_shipping_i . ']'] = esc_attr($shipping['tracking_url']);
            }
        }


        $delivery_date = EC_Helper::get_order_delivery_date($this->get_order_number());
        if ($delivery_date != false) {
            $_temp_data['Order Delivery Date']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . 'order_delivery_date]'] = $delivery_date;
        }
        /*  custom fields */
        if (!empty($this->order) && !is_null($this->order)) {
            if (function_exists('wc_get_custom_checkout_fields')) {
                $custom_fields = wc_get_custom_checkout_fields($this->order);
                if (!empty($custom_fields)) {
                    foreach ($custom_fields as $key => $custom_field) {
                        $_temp_data['Checkout Field Editor']['[' . EC_WOO_BUILDER_SHORTCODE_PRE . '' . $key . ']'] = get_post_meta($this->get_order_number(), $key, true);
                    }
                }
            }
        }


        $this->convert_shortcode_data($_temp_data);
        $this->full_shortcode_data = $_temp_data;
    }

    public function convert_shortcode_data($arr)
    {
        $converted = array();
        foreach ($arr as $group => $group_items) {
            foreach ($group_items as $shortcode => $value) {
                $converted[$shortcode] = $value;
            }
        }
        $this->shortcode_data = $converted;
    }

    public function get_order_total($order)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-totals.php';
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_customer_notes($customer_notes)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/customer-notes.php';
        //return $this->get_template_content($path);
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template_1($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items-1.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template_2($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items-2.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template_3($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items-3.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template_4($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items-4.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template_5($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items-5.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_order_items_template_6($order, $items)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/order-items-6.php';
        //return $this->get_template_content($path);
        ob_start();
        $config = $items;
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function get_related_products_grid($order, $params = array())
    {
        $params['is_preview']=$this->is_preview;
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/products-related-grid.php';
        //return $this->get_template_content($path);
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    private function get_bacs_account_details_html()
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/bacs-info.php';
        ob_start();
        include($path);
        $output = ob_get_clean();
        return $output;
    }
    /*
    *  Woocommerce subscription
    */
    private function get_wcs_info_1($wcs_data,$order_total)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/wcs-info-1.php';
        ob_start();
        include($path);
        $output = ob_get_clean();
        return $output;
    }
    /*
    *  Woocommerce subscription
    */
    private function get_wcs_info_2($wcs_data,$order_total)
    {
        $path = EC_WOO_BUILDER_PATH . '/templates/ec-woo-mail-helper/wcs-info-2.php';
        ob_start();
        include($path);
        $output = ob_get_clean();
        return $output;
    }

    private function get_template_content($path)
    {
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    private function get_order_items($order_items)
    {
        $item_list = array();
        foreach ($order_items as $index => $item) {
            $item_list[$index]['product_name'] = $this->get_first_or_default($item['name']);
            $item_list[$index]['type'] = $this->get_first_or_default($item['type']);
            $item_list[$index]['qty'] = $this->get_first_or_default($item['item_meta']['_qty']);
            $item_list[$index]['tax_class'] = $this->get_first_or_default($item['item_meta']['_tax_class']);
            $item_list[$index]['product_id'] = $this->get_first_or_default($item['item_meta']['_product_id']);
            $item_list[$index]['variation_id'] = $this->get_first_or_default($item['item_meta']['_variation_id']);
            $item_list[$index]['line_total'] = $this->get_first_or_default($item['item_meta']['_line_total']);
            $item_list[$index]['line_subtotal'] = $this->get_first_or_default($item['item_meta']['_line_subtotal']);
            $item_list[$index]['line_subtotal_tax'] = $this->get_first_or_default($item['item_meta']['_line_subtotal_tax']);
            $item_list[$index]['line_tax'] = $this->get_first_or_default($item['item_meta']['_line_tax']);
            $item_list[$index]['line_tax_data'] = $this->get_first_or_default($item['item_meta']['_line_tax_data']);
            $item_list[$index]['item'] = $item;
        }
        return $item_list;
    }

    public function get_first_or_default($arr)
    {
        $result = $arr;
        if (is_array($arr)) {
            if (isset($arr[0])) {
                $result = $arr[0];
            }
        }
        return $result;
    }

    public function get_order_number()
    {
        return method_exists($this->order, 'get_order_number') ? $this->order->get_order_number() : $this->order->id;
    }

    public function get_order_billing_first_name()
    {
        return method_exists($this->order, 'get_billing_first_name') ? $this->order->get_billing_first_name() : $this->order->billing_first_name;
    }

    public function get_order_billing_last_name()
    {
        return method_exists($this->order, 'get_billing_last_name') ? $this->order->get_billing_last_name() : $this->order->billing_last_name;
    }

    public function get_order_billing_email()
    {
        return method_exists($this->order, 'get_billing_email') ? $this->order->get_billing_email() : $this->order->billing_email;
    }

    public function get_order_payment_method_title()
    {
        return method_exists($this->order, 'get_payment_method_title') ? $this->order->get_payment_method_title() : $this->order->payment_method_title;
    }

    public function get_billing_company()
    {
        return method_exists($this->order, 'get_billing_company') ? $this->order->get_billing_company() : $this->order->billing_company;
    }

    public function get_billing_address_1()
    {
        return method_exists($this->order, 'get_billing_address_1') ? $this->order->get_billing_address_1() : $this->order->billing_address_1;
    }

    public function get_billing_address_2()
    {
        return method_exists($this->order, 'get_billing_address_2') ? $this->order->get_billing_address_2() : $this->order->billing_address_2;
    }

    public function get_billing_city()
    {
        return method_exists($this->order, 'get_billing_city') ? $this->order->get_billing_city() : $this->order->billing_city;
    }

    public function get_billing_state()
    {
        return method_exists($this->order, 'get_billing_state') ? $this->order->get_billing_state() : $this->order->billing_state;
    }

    public function get_billing_postcode()
    {
        return method_exists($this->order, 'get_billing_postcode') ? $this->order->get_billing_postcode() : $this->order->billing_postcode;
    }

    public function get_billing_country()
    {
        return method_exists($this->order, 'get_billing_country') ? $this->order->get_billing_country() : $this->order->billing_country;
    }

    public function get_billing_phone()
    {
        return method_exists($this->order, 'get_billing_phone') ? $this->order->get_billing_phone() : $this->order->billing_phone;
    }

    public function get_shipping_first_name()
    {
        return method_exists($this->order, 'get_shipping_first_name') ? $this->order->get_shipping_first_name() : $this->order->shipping_first_name;
    }

    public function get_shipping_last_name()
    {
        return method_exists($this->order, 'get_shipping_last_name') ? $this->order->get_shipping_last_name() : $this->order->shipping_last_name;
    }

    public function get_shipping_company()
    {
        return method_exists($this->order, 'get_shipping_company') ? $this->order->get_shipping_company() : $this->order->shipping_company;
    }

    public function get_shipping_address_1()
    {
        return method_exists($this->order, 'get_shipping_address_1') ? $this->order->get_shipping_address_1() : $this->order->shipping_address_1;
    }

    public function get_shipping_address_2()
    {
        return method_exists($this->order, 'get_shipping_address_2') ? $this->order->get_shipping_address_2() : $this->order->shipping_address_2;
    }

    public function get_shipping_city()
    {
        return method_exists($this->order, 'get_shipping_city') ? $this->order->get_shipping_city() : $this->order->shipping_city;
    }

    public function get_shipping_state()
    {
        return method_exists($this->order, 'get_shipping_state') ? $this->order->get_shipping_state() : $this->order->shipping_state;
    }

    public function get_shipping_postcode()
    {
        return method_exists($this->order, 'get_shipping_postcode') ? $this->order->get_shipping_postcode() : $this->order->shipping_postcode;
    }

    public function get_shipping_country()
    {
        return method_exists($this->order, 'get_shipping_country') ? $this->order->get_shipping_country() : $this->order->shipping_country;
    }
}
