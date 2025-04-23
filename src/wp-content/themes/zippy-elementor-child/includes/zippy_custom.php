<?php
add_action('wp_enqueue_scripts', 'shin_scripts');

function shin_scripts()
{
    $version = time();

    wp_enqueue_style('main-style-css', THEME_URL . '-child' . '/assets/dist/css/main.min.css', array(), $version, 'all');

    // wp_enqueue_script('main-scripts-js', THEME_URL . '-child' . '/assets/dist/js/main.min.js', array('jquery'), $version, true);

    // Load Thirt-party
	// wp_enqueue_style('vanilla-celendar-css', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.css', array(), $version, 'all');
	// wp_enqueue_script('vanilla-scripts-js', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.js', array('jquery'), $version, true);
}

add_filter( 'woocommerce_my_account_my_orders_actions', 'remove_pay_action', 10, 2 );

function remove_pay_action( $actions, $order ) {
    unset( $actions['pay'] );
    return $actions;
}

add_filter( 'woocommerce_email_enabled_customer_completed_order', 'disable_completed_email_for_non_monthly_orders', 10, 2 );

function disable_completed_email_for_non_monthly_orders( $enabled, $order ) {
    if ( is_a( $order, 'WC_Order' ) ) {
        $is_monthly = $order->get_meta('is_monthly_payment_order');

        if ( ! $is_monthly ) {
            return false;
        }
    }

    return $enabled;
}

add_filter( 'woocommerce_account_menu_items', 'remove_my_account_downloads_tab', 99 );
function remove_my_account_downloads_tab( $items ) {
    unset( $items['downloads'] );
    return $items;
}

add_filter('woocommerce_order_again_button', 'custom_hide_order_again_button_detail', 10, 1);

function custom_hide_order_again_button_detail($button_html) {
    global $order;
    if (is_account_page() && is_wc_endpoint_url('view-order')) {
        if ($order && is_a($order, 'WC_Order') && $order->has_status('completed')) {
            return '';
        }
    }

    return $button_html;
}
function get_tax_percent()
{
  $all_tax_rates = [];
  $tax_classes = WC_Tax::get_tax_classes();
  if (!in_array('', $tax_classes)) {
    array_unshift($tax_classes, '');
  }

  foreach ($tax_classes as $tax_class) {
    $taxes = WC_Tax::get_rates_for_tax_class($tax_class);
    $all_tax_rates = array_merge($all_tax_rates, $taxes);
  }

  if (empty($all_tax_rates)) return;
  return $all_tax_rates[0];
}