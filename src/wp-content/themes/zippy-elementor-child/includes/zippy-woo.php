<?php

function disable_password_reset()
{
  return false;
}

add_filter('allow_password_reset', 'disable_password_reset');
  
add_action('woocommerce_order_list_table_restrict_manage_orders', 'show_is_first_order_checkbox', 20);
function show_is_first_order_checkbox()
{
  $selected = isset($_GET['metadata']) ? esc_attr($_GET['metadata']) : '';
  $options  = array(
    ''              => __('By order type', 'woocommerce'),
    '0'  => __('Visitor Orders', 'woocommerce'),
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
  if (isset($_GET['metadata']) && is_numeric($_GET['metadata'])) {
    $meta_query[] = array(
      'key' => 'member_type',
      'value' => intval($_GET['metadata']),
      'compare' => 'AND'
    );
    $query_args['meta_query'] = $meta_query;
  }
  return $query_args;
}

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