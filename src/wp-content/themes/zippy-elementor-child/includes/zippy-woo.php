<?php

function disable_password_reset()
{
  return false;
}

add_filter('allow_password_reset', 'disable_password_reset');

add_filter('woocommerce_order_actions', 'confirm_email_woocommerce_order_actions', 10, 2);

function confirm_email_woocommerce_order_actions($actions, $order)
{
  $is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);

  $status = $order->get_status();

  unset($actions['regenerate_download_permissions']);
  unset($actions['send_order_details_admin']);

  if (!$order->get_customer_id()) {
    $actions['send_order_details'] =  __('Send order details to Visitor', 'send_order_details');
  }

  if ($is_monthly_payment_order || $status != 'on-hold') return $actions;

  unset($actions['send_order_details']);

  $actions['send-confirmation-email'] = __('Send confirmation email to Member', 'send-confirmation-email');

  return $actions;
}


add_action('woocommerce_order_list_table_restrict_manage_orders', 'show_is_first_order_checkbox', 20);
function show_is_first_order_checkbox()
{
  $selected = isset($_GET['metadata']) ? esc_attr($_GET['metadata']) : '';
  $options  = array(
    ''              => __('By order type', 'woocommerce'),
    '0'  => __('Public orders', 'woocommerce'),
    '1'  => __('Member Orders', 'woocommerce')
  );

  echo '<select name="metadata" id="dropdown_shop_order_metadata">';
  foreach ($options as $value => $label_name) {
    printf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label_name);
  }
  echo '</select>';
}

add_filter('woocommerce_order_query_args', 'filter_woocommerce_orders_in_the_table');
function filter_woocommerce_orders_in_the_table($query_args)
{

  if (!empty($_GET['metadata'])) {
    $meta_query[] = array(
      'key' => 'member_type',
      'value' => intval($_GET['metadata']),
      'compare' => 'AND'
    );

    $query_args['meta_query'] = $meta_query;
  }
  return $query_args;
}

add_action('woocommerce_email', function () {
  if (!defined('DOING_WC_EMAIL')) {
    define('DOING_WC_EMAIL', true);
  }
});

function is_on_wc_orders_page_without_email()
{
  if (!is_admin()) return false;
  if (isset($_REQUEST['wc_order_action']) && !empty($_REQUEST['wc_order_action'])) return false;

  if (function_exists('get_current_screen')) {
    $screen = get_current_screen();
    return $screen && $screen->id === 'woocommerce_page_wc-orders';
  }

  return false;
}
// Change the Order ID in Order Woocommerece
add_filter('woocommerce_order_number', 'custom_order_number_display_type', 10, 2);

function custom_order_number_display_type($order_number, $order)
{
  // Skip for monthly payment orders
  $is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);

  if ($is_monthly_payment_order) {
    return $order_number;
  }
  // Skip if not in admin or during email sending
  if (!is_on_wc_orders_page_without_email()) {
    return $order_number;
  }

  $is_member = $order->get_customer_id();

  if ($is_member) {
    return $order_number . '-Member';
  }

  return $order_number . '-Visitor';
}
// Change the Order ID in Order Woocommerece

add_filter('woocommerce_get_order_payment_method', 'hide_payment_method_in_email', 10, 2);

function hide_payment_method_in_email($payment_method, $order)
{
  if (is_email_context()) {
    return ''; // Hide payment method in emails
  }

  return $payment_method;
}

add_filter('woocommerce_my_account_my_orders_query', 'filter_my_account_orders_query');

function filter_my_account_orders_query($query_args)
{
  // $query_args['meta_query'][] = array(
  //     'key'     => 'is_monthly_payment_order',
  //     'value'   => '1',
  //     'compare' => '='
  // );

  return $query_args;
}
