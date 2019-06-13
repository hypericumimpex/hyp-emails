<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper class
 */
class EC_Helper
{
    public function __construct()
    {
    }
    /*
    * Get list of mail types
    */
    public static function get_mails_list()
    {
        if (function_exists('WC')) {
            $wooinst = WC();
            $mailer = $wooinst->mailer();
            $mails = $mailer->get_emails();
        } else {
            $mailer = $woocommerce->mailer();
            $mails = $mailer->get_emails();
        }
        return $mails;
    }
    /*
    * Get list of available languages
    */
    public static function get_available_languages()
    {
        return get_available_languages();
    }
    /*
    * Get Local language of wp
    */
    public static function get_locale()
    {
        return get_locale();
    }

    public function ec_woo_get_new_template($located, $template_name, $args, $template_path, $default_path)
    {
        $plugin_path= EC_WOO_BUILDER_PATH . '/templates/woo-mail-template.php';

        if (!isset($args)) {
            return $located;
        }
        if (!isset($args['email'])) {
            return $located;
        }
        if (!isset($args['email']->id)) {
            return $located;
        }

        $email_type=$args['email']->id;
        $lang=strtolower(EC_Helper::get_locale());
        $replace_email=self::get_replace_email_for_type($email_type);

        if ($replace_email=='0') {
            return $located;
        }


        //check template
        if (isset($email_type) && !empty($email_type)) {
            $post_helper=new EC_Helper_Posts();
            $result=$post_helper->exists_email($lang, $email_type);
            //if email template changed
            if ($result) {
                //change the default path to our plugin path
                $located = $plugin_path;
            }
        }


        return $located;
    }
    public static function get_order_delivery_date($order_id)
    {
        if (class_exists('orddd_lite_common')) {
            $delivery_date = orddd_lite_common::orddd_lite_get_order_delivery_date($order_id);
            ;
            return $delivery_date;
        }
        return false;
    }
    public static function get_woo_bank_info()
    {
        $bacs_info = get_option('woocommerce_bacs_accounts', '-1');
        if ($bacs_info=='-1') {
            return false;
        }
        return $bacs_info;
    }


    //get flexible Checkou tFields Plugin
    public static function getCustomFieldsOf_FCFP()
    {
        $fields = array();
        global $flexible_checkout_fields;
        if (self::checkFCFPlugin()) {
            $fields = array();

            if (method_exists($flexible_checkout_fields, 'get_settings')) {
                $field_settings = $flexible_checkout_fields->get_settings();
                $custom_fields['billing'] = (isset($field_settings['billing']))? $field_settings['billing']: array();
                $custom_fields['shipping'] = (isset($field_settings['shipping']))? $field_settings['shipping']: array();
                $custom_fields['order'] = (isset($field_settings['order']))? $field_settings['order']: array();
                foreach ($custom_fields as $custom_field) {
                    if (!empty($custom_field)) {
                        foreach ($custom_field as $field_data) {
                            if (isset($field_data['custom_field']) && $field_data['custom_field'] == 1) {
                                if (isset($field_data['name']) && $field_data['name']) {
                                    $fields['_'.$field_data['name']] = $field_data['label'];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $fields;
    }

    //exist Flexible Checkou tFields Plugin
    public static function checkFCFPlugin()
    {
        if (function_exists('wpdesk_get_order_meta')) {
            return true;
        }
        return false;
    }

    // check Flexible Woocommerce Checkout Field Editor
    public static function check_flexible_checkout_editor_woo()
    {
        if (class_exists('FWCFE_Settings_Page_Manager')) {
            return true;
        }
        return false;
    }

    public static function get_custom_fields_flexible_checkout_editor_woo($order_id)
    {
        if (self::check_flexible_checkout_editor_woo()) {
            $fields=array("orderFieldsOptions","accountFieldsOptions","shippingFieldsOptions","billingFieldsOptions");
            $_custom_field_arr=[];
            foreach ($fields as $custom_field) {
                $field_names=json_decode(get_option($custom_field, ''));
                foreach ($field_names as  $field_name) {
                    if (strpos($field_name->name, 'wc_')>-1) {
                        $_custom_field_arr[$field_name->name]='';
                    }
                }
            }
            foreach ($_custom_field_arr as $key => $value) {
                $_custom_field_arr[$key]=EC_Helper_Posts::get_custom_field_value_flexible_checkout_editor_woo($order_id, $key);
            }
            return $_custom_field_arr;
        }
    }

    public static function get_order_billing_first_name($order)
    {
        return method_exists($order, 'get_billing_first_name') ? $order->get_billing_first_name() : $order->billing_first_name ;
    }
    public static function get_order_billing_last_name($order)
    {
        return method_exists($order, 'get_billing_last_name') ? $order->get_billing_last_name() : $order->billing_last_name ;
    }
    public static function get_order_billing_email($order)
    {
        return method_exists($order, 'get_billing_email') ? $order->get_billing_email() : $order->billing_email ;
    }
    public static function get_order_number($order)
    {
        return method_exists($order, 'get_order_number') ? $order->get_order_number() :$order->id ;
    }
    public static function get_order_get_date_created($order)
    {
        return method_exists($order, 'get_date_created') ? $order->get_date_created() : $order->date;
    }
    public static function get_order_date($order)
    {
        return method_exists($order, 'get_date_created') ? $order->get_date_created() : $order->order_date;
    }
    public static function generate_shortcode_json($arr)
    {
        $groups=array();
        $tags=array();
        foreach ($arr as $group_name=>$group_items) {
            $groups[]=$group_name;
            foreach ($group_items as $key => $value) {
                $tag_item=array();
                $tag_item['group']=$group_name;
                $tag_item['title']=self::get_tag_title($key);
                $tag_item['content']=$key;
                $tags[]=$tag_item;
            }
        }
        $json = array(
        'groups' =>$groups ,
        'tags'=>$tags
       );
        return json_encode($json);
    }
    private static function get_tag_title($value)
    {
        $str=str_replace('ec_woo', ' ', $value);
        $str=str_replace('_', ' ', $str);
        $str=str_replace('[', ' ', $str);
        $str=str_replace(']', ' ', $str);
        return 'Get '.$str;
    }
    // check Woocommerce shipping tracking
    public static function check_woo_shipping_tracking()
    {
        if (function_exists('wcst_setup')) {
            return true;
        }
        return false;
    }
    public static function get_woo_shipping_tracking_data($order_id)
    {
        if (self::check_woo_shipping_tracking()) {
            $tracking_info = wcst_get_order_tracking_data($order_id);
            ;
            return $tracking_info;
        }
        return false;
    }
    public static function array_sort_bycolumn(&$array, $column, $dir = 'asc')
    {
        foreach ($array as $a) {
            $sortcol[$a->$column][] = $a;
        }
        ksort($sortcol);
        foreach ($sortcol as $col) {
            foreach ($col as $row) {
                $newarr[] = $row;
            }
        }
        if ($dir=='desc') {
            $array = array_reverse($newarr);
        } else {
            $array = $newarr;
        }
    }
    public static function get_replace_email_for_type($type)
    {
      $replace_email_all=get_option('ec_woo_settings_replace_mail', EC_WOO_BUILDER_REPLACE_MAIL);
      $replace_email=get_option('ec_woo_settings_replace_mail_'.$type, $replace_email_all);
      return $replace_email;
    }

    public static function logger($display_errors=0)
    {
      error_reporting(E_ALL | E_STRICT);
      ini_set('display_errors', $display_errors);
      ini_set('log_errors', 1);
      ini_set('error_log', EC_WOO_BUILDER_PATH."/logs/errors.txt");
    }

    public static function get_wcs_subscriptions($order_id)
    {
      // Get all customers subscriptions
      $customer_subscriptions = get_posts( array(
          'numberposts' => -1,
          // 'meta_key'    => '_customer_user',
          // 'meta_value'  => get_current_user_id(), // Or $user_id
          'post_type'   => 'shop_subscription', // WC orders post type
          'post_status' => 'wc-active' // Only orders with status "completed"
      ) );
      $order='';
      // Iterating through each post subscription object
      foreach( $customer_subscriptions as $customer_subscription )
      {
          // The subscription ID
          $subscription_id = $customer_subscription->ID;

          // IMPORTANT HERE: Get an instance of the WC_Subscription Object
          $subscription = new WC_Subscription( $subscription_id );
          // Or also you can use
          // wc_get_order( $subscription_id );

          // Getting the related Order ID (added WC 3+ comaptibility)
          $_order_id = method_exists( $subscription, 'get_parent_id' ) ? $subscription->get_parent_id() : $subscription->order->id;

          if ($order_id==$_order_id)
          {
            $order =$subscription;// method_exists( $subscription, 'get_parent' ) ? $subscription->get_parent() : $subscription->order;
            break;
          }
      }
      return $order;
    }

}
