<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce Subs Loader
 */
class WooSubsLoader
{
  private $post_helper;
  private $order_id;
  private $subs_data;
  private $subs_meta;
  private $subs_types;
  private $wp_date_format;

  function __construct($order_id)
  {
    $this->order_id=$order_id;
    $this->post_helper=new EC_Helper_Posts();

    $this->subs_types=array();
    $this->subs_types['wc-active'] = __("Active", EC_WOO_BUILDER_TEXTDOMAIN);
    $this->subs_types['wc-cancelled'] = __("Cancelled", EC_WOO_BUILDER_TEXTDOMAIN);
    $this->subs_types['wc-on-hold'] = __("On hold", EC_WOO_BUILDER_TEXTDOMAIN);
    $this->subs_types['wc-pending'] = __("Pending", EC_WOO_BUILDER_TEXTDOMAIN);

    $this->wp_date_format=get_option('date_format','F j, Y');
    //load data from DB
    $this->loadData();
  }

  private function loadData()
  {
    $this->subs_data=$this->post_helper->get_woo_subscription( $this->order_id );
    if ($this->hasData()) {
      $temp_data=$this->post_helper->get_woo_subscription_meta( $this->get_subscription_id() );
      foreach ($temp_data as $row) {
        $this->subs_meta[$row->meta_key]=$row->meta_value;
      }
    }
  }

  public function hasData()
  {
    if (empty($this->subs_data) || is_null($this->subs_data)) {
      return false;
    }
    return true;
  }

  public function get_subscription_id_url()
  {
    return '<a class="ec-woo-wcs-id" href="' . site_url() . '/my-account/view-subscription/'.$this->get_subscription_id().'"> #'
      . $this->get_subscription_id() .
     ' </a>';
  }

  public function get_subscription_id()
  {
    return $this->subs_data->ID;
  }
  public function get_subscription_status()
  {
    return $this->subs_types[$this->subs_data->post_status];
  }
  public function get_billing_period()
  {
    return $this->subs_meta['_billing_period'];
  }
  public function get_billing_interval()
  {
    return $this->subs_meta['_billing_interval'];
  }
  public function get_subscription_start_date()
  {
    return $this->formatted_date($this->subs_data->post_date);
  }

  public function get_subscription_end_date()
  {
    $date=$this->formatted_date($this->subs_meta['_schedule_end']);
    if ($date=='') {
      return __("When Cancelled", EC_WOO_BUILDER_TEXTDOMAIN);
    }
    return $date;
  }
  public function get_subscription_price()
  {
    return $this->subs_meta['_order_total'];
  }

  public function get_subscription_next_payment_date()
  {
    return $this->formatted_date($this->subs_meta['_schedule_next_payment']);
  }

  private function formatted_date($str_date)
  {
    if (is_null($str_date) || !isset($str_date) || empty($str_date)) {
      return '';
    }
    $datetime = strtotime($str_date);
    return date($this->wp_date_format, $datetime);
  }


  //Lazimli data. DB_den yukle
  /*
    * Subscription No - > postId . Link olmalidi bele ->http://wordpress:987/wp1/my-account/view-subscription/305/
    * Start Date
    * End Date
    * Price
    * Period : Month/Year
    * Schedule_next_payment
    */

//asagidaki methodlari check et lazim olanlari elave et


  //Checks if the subscription has an unpaid order or renewal order (and therefore, needs payment).
  //var_dump($subs_data->needs_payment());

  //xxxxxxxxxxxx
  //var_dump($subs_data->is_manual());

  //nece defe odenis edib. Get the number of payments completed for a subscription
  //  var_dump($subs_data->get_completed_payment_count());

  //Get the number of payments failed
  //var_dump($subs_data->get_failed_payment_count());

  //Get billing period.
//  var_dump($subs_data->get_billing_period());

  //Get billing interval.
  //var_dump($subs_data->get_billing_interval());

  //Get trial period
  //var_dump($subs_data->get_trial_period());

  //* Get the MySQL formatted date for a specific piece of the subscriptions schedule
  //  * @param string $date_type 'date_created', 'trial_end', 'next_payment', 'last_order_date_created' or 'end'
  //var_dump($subs_data->get_date('next_payment'));

  /* Get date_paid prop of most recent related order that has been paid.
   *
   * A subscription's paid date is actually determined by the most recent related order,
   * with a paid date set, not a prop on the subscription itself.
   */
 //  var_dump($subs_data->get_date_paid());

 /**
  * Returns a string representation of a subscription date in the site's time (i.e. not GMT/UTC timezone).
  *
  * @param string $date_type 'date_created', 'trial_end', 'next_payment', 'last_order_date_created', 'end' or 'end_of_prepaid_term'
  */
  //var_dump($subs_data->get_date_to_display());

  /**
   * Calculate a given date for the subscription in GMT/UTC.
   *
   * @param string $date_type 'trial_end', 'next_payment', 'end_of_prepaid_term' or 'end'
   */
  //var_dump($subs_data->calculate_date('next_payment'));













  //$customer_subscriptions = $this->post_helper->get_woo_subscription('304');

  // Iterating through each post subscription object
  // foreach( $customer_subscriptions as $customer_subscription ){
  //     // The subscription ID
  //     $subscription_id = $customer_subscription->ID;
  //     //echo $subscription_id;
  //     //
  //     // // IMPORTANT HERE: Get an instance of the WC_Subscription Object
  //     // $subscription = new WC_Subscription( $subscription_id );
  //     // // Or also you can use
  //     // // wc_get_order( $subscription_id );
  //     //
  //     // // Getting the related Order ID (added WC 3+ comaptibility)
  //     // $order_id = method_exists( $subscription, 'get_parent_id' ) ? $subscription->get_parent_id() : $subscription->order->id;
  //     // // Getting an instance of the related WC_Order Object (added WC 3+ comaptibility)
  //     // //$order = method_exists( $subscription, 'get_parent' ) ? $subscription->get_parent() : $subscription->order;
  //     // echo $order_id.'xxx';
  //     // if ($order_id==$this->order_id) {
  //     //   //echo ($order);
  //     // }
  //
  //
  //     // Optional (uncomment below): Displaying the WC_Subscription object raw data
  //     // echo '<pre>';print_r($subscription);echo '</pre>';
  // }
}
