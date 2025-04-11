<?php
function disable_password_reset()
{
  return false;
}

add_filter('allow_password_reset', 'disable_password_reset');


add_filter('woocommerce_order_actions', 'confirm_email_woocommerce_order_actions', 10, 2);

add_action('woocommerce_process_shop_order_meta', 'confirm_email_woocommerce_order_action_execute', 50, 2);

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

  if (isset($_GET['metadata'])) {
    $meta_query[] = array(
      'key' => 'member_type',
      'value' => intval($_GET['metadata']),
      'compare' => 'AND'
    );

    $query_args['meta_query'] = $meta_query;
  }
  return $query_args;
}

add_filter('woocommerce_order_number', 'custom_order_number_display_type', 10, 2);

function custom_order_number_display_type($order_number, $order)
{
  $is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);

  if ($is_monthly_payment_order || !is_admin()) return $order_number;

  $is_member = $order->get_customer_id();

  if ($is_member) {
    return $order_number . '-Member';
  }

  return $order_number . '-Public';
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
