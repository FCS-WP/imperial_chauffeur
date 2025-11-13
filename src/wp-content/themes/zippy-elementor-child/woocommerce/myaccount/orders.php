<?php
defined('ABSPATH') || exit;

$current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'booking_date';
$current_order   = isset($_GET['order']) ? strtolower(sanitize_text_field($_GET['order'])) : 'desc';


do_action('woocommerce_before_account_orders', $has_orders); ?>
<?php

if (isset($_GET['orderby']) && $current_orderby == 'booking_date') {
	$customer_orders = [];
	global $wpdb;

	$results = $wpdb->get_col(
		"
    SELECT o.id
    FROM {$wpdb->prefix}wc_orders AS o
    JOIN {$wpdb->prefix}postmeta AS p ON o.id = p.post_id
    WHERE o.type = 'shop_order'
      AND o.customer_id <> 0
      AND p.meta_key = 'pick_up_date'
			AND o.status IN ('wc-on-hold','wc-pending','wc-processing','wc-confirmed')
     AND o.customer_id = " . get_current_user_id() . "
    ORDER BY
      COALESCE(
        STR_TO_DATE(p.meta_value, '%d-%m-%Y'),
        STR_TO_DATE(p.meta_value, '%Y-%m-%d'),
        STR_TO_DATE(p.meta_value, '%d-%M-%Y')
      ) {$current_order}
    "
	);

	$customer_orders = (object) array(
		'orders' => $results
	);
}

?>
<?php if ($has_orders) : ?>
	<?php wc_print_notices(); ?>
	<table class="woocommerce-orders-table shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<th>
					<?php echo build_sort_link(__('Order Number', 'woocommerce'), 'id', $current_orderby, $current_order); ?>
				</th>
				<th><?php esc_html_e('Staff Name', 'woocommerce'); ?></th>
				<th>
					<?php echo build_sort_link(__('Booking Date', 'woocommerce'), 'booking_date', $current_orderby, $current_order); ?>
				</th>
				<th><?php esc_html_e('Type of service', 'woocommerce'); ?></th>
				<th><?php esc_html_e('Status', 'woocommerce'); ?></th>
				<th><?php esc_html_e('Type of vehicle', 'woocommerce'); ?></th>
				<th><?php esc_html_e('Total', 'woocommerce'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($customer_orders->orders as $customer_order) :
				$order = wc_get_order($customer_order);
				$is_monthly = $order->get_meta('is_monthly_payment_order');
				$item_count = $order->get_item_count() - $order->get_item_count_refunded();
				foreach ($order->get_items() as $item) {

					$product = $item->get_product();

					if ($product) {
						$product_ids = $product->get_id();
						$product_name = $product->get_name();
					}
				}
			?>
				<tr class="woocommerce-orders-table__row order">
					<td data-title="<?php esc_attr_e('Order Number', 'woocommerce'); ?>">
						<a href="<?php echo esc_url($order->get_view_order_url()); ?>">
							<?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
						</a>
					</td>
					<td data-title="<?php esc_attr_e('Staff Name', 'woocommerce'); ?>">
						<?php echo get_post_meta($order->get_order_number(), "staff_name", true) ?>
					</td>
					<td data-title="<?php esc_attr_e('Date & time of booking', 'woocommerce'); ?>">
						<time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>">
							<?php
							$pickupdate_value = get_post_meta($order->get_order_number(), "pick_up_date", true);
							$pickupdate = !empty($pickupdate_value)  ? date('d-m-Y', strtotime($pickupdate_value)) : esc_html(wc_format_datetime($order->get_date_created(), 'd-m-Y')) ?>
							<?php echo esc_html($pickupdate); ?>
						</time>
					</td>
					<td data-title="<?php esc_attr_e('Type of service', 'woocommerce'); ?>"><?php echo get_post_meta($order->get_order_number(), "service_type", true) ?></td>
					<td class="order-status <?php echo sanitize_html_class($order->get_status()); ?>" data-title="<?php esc_attr_e('Status', 'woocommerce'); ?>">
						<span><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span>
					</td>
					<td data-title="<?php esc_attr_e('Vehicle', 'woocommerce'); ?>">
						<?php echo esc_html($product_name); ?>
					</td>
					<td data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">

						<?php

						$user = wp_get_current_user();

						if (in_array('customer_v2', (array) $user->roles)) {
							echo apply_filters('woocommerce_order_item_total', wc_price($order->get_total()), $order->get_items(), $order);
						} else {
							echo "Please contact administrator!";
						}

						?>

					</td>
					<td class="order-actions">
						<?php
						$actions = wc_get_account_orders_actions($order);

						//remove cancel action
						unset($actions['cancel']);
						if (!empty($actions)) {
							foreach ($actions as $key => $action) {
								echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
							}
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php do_action('woocommerce_before_account_orders_pagination'); ?>

	<?php if (1 < $customer_orders->max_num_pages) : ?>
		<div class="woocommerce-pagination woocommerce-Pagination">
			<?php if (1 !== $current_page) : ?>
				<a class="woocommerce-button button prev" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>"><?php esc_html_e('Previous', 'woocommerce'); ?></a>
			<?php endif; ?>
			<?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
				<a class="woocommerce-button button next" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>"><?php esc_html_e('Next', 'woocommerce'); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<?php wc_print_notice(
		esc_html__('No order has been made yet.', 'woocommerce') . ' <a class="woocommerce-Button wc-forward button" href="' . esc_url(wc_get_page_permalink('shop')) . '">' . esc_html__('Browse products', 'woocommerce') . '</a>',
		'notice'
	); ?>

<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
<?php
add_action('woocommerce_cancelled_order', 'custom_cancel_order_notice', 10, 1);

function custom_cancel_order_notice($order_id)
{
	if (!is_user_logged_in()) return;

	// Check if it's the current user's order
	$order = wc_get_order($order_id);
	if ($order->get_user_id() === get_current_user_id()) {
		wc_add_notice(__('You have successfully cancelled the order.', 'woocommerce'), 'success');
	}
}
?>