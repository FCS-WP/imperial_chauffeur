<?php
defined('ABSPATH') || exit;

$order = wc_get_order($order_id);

if (! $order) {
	return;
}

$show_customer_details = $order->get_user_id() === get_current_user_id();

do_action('woocommerce_order_details_before_order_table', $order);

wc_get_template('order/order-details-table.php', array('order' => $order));
wc_get_template('order/order-details-custom-fields.php', array('order' => $order));

do_action('woocommerce_after_order_details', $order);

if ($show_customer_details) {
	wc_get_template('order/order-details-customer.php', array('order' => $order));
}
