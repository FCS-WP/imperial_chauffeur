<?php

/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 *
 * @var bool $show_downloads Controls whether the downloads table should be rendered.
 */

// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

defined('ABSPATH') || exit;

$order = wc_get_order($order_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if (! $order) {
	return;
}

$order_items        = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));

$order_quantity = 0;

$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
// $downloads          = $order->get_downloadable_items();
$is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);
$service_type = get_post_meta($order_id, 'service_type', true);
// We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
$show_customer_details = $order->get_user_id() === get_current_user_id();
$order_date = $order->get_date_created();
?>
<section class="woocommerce-order-details">
	<?php do_action('woocommerce_order_details_before_order_table', $order); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e('Order details', 'woocommerce'); ?></h2>


	<?php
	if ($is_monthly_payment_order && $order_date) {
		echo '<p><strong>Order Created:</strong> ' . esc_html($order_date->date('H:i d/m/Y')) . '</?php>';
	}
	?>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<?php if ($is_monthly_payment_order): ?>
					<th class="woocommerce-table__product-name product-name"><?php esc_html_e('Order at Total', 'woocommerce'); ?></th>
					<th class="woocommerce-table__action action"><?php esc_html_e('Action', 'woocommerce'); ?></th>
				<?php else: ?>
					<th class="woocommerce-table__product-name product-name"><?php esc_html_e('Item', 'woocommerce'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action('woocommerce_order_details_before_order_table_items', $order);

			foreach ($order_items as $item_id => $item) {
				$product = $item->get_product();
				$order_quantity = $item->get_quantity();
				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
						'service_type'       => $service_type,
					)
				);
			}

			do_action('woocommerce_order_details_after_order_table_items', $order);
			?>
		</tbody>

		<tfoot>

			<?php if ($order->get_customer_note()) : ?>
				<tr>
					<th><?php esc_html_e('Note:', 'woocommerce'); ?></th>
					<td><?php echo wp_kses(nl2br(wptexturize($order->get_customer_note())), array()); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>


	<?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</section>
<?php
if (empty($is_monthly_payment_order)) :
?>
	<div class="woocommerce-order-custom-fields">
		<?php
		$custom_fields = array(
			'service_type'       => __('Service Type', 'woocommerce'),
			'flight_details'     => __('Flight Details', 'woocommerce'),
			'eta_time'           => __('ETE/ETA Time', 'woocommerce'),
			'no_of_passengers'   => __('No Of Passengers', 'woocommerce'),
			'no_of_baggage'      => __('No Of Baggage', 'woocommerce'),
			'key_member'         => __('Key Member', 'woocommerce'),
			'pick_up_date'       => __('Pick Up Date', 'woocommerce'),
			'pick_up_time'       => __('Pick Up Time', 'woocommerce'),
			'pick_up_location'   => __('Pick Up Location', 'woocommerce'),
			'drop_off_location'  => __('Drop Off Location', 'woocommerce'),
		);

		$order_id = $order->get_id();
		$is_editing = isset($_GET['edit_order']) && $_GET['edit_order'] == $order_id;

		if ($is_editing && $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('save_custom_fields_' . $order_id)) {
			$changes = [];

			foreach ($custom_fields as $key => $label) {
				if (isset($_POST[$key])) {
					$old_value = get_post_meta($order_id, $key, true);
					$new_value = sanitize_text_field($_POST[$key]);

					if ($old_value !== $new_value && $new_value !== '') {

						update_post_meta($order_id, $key, $new_value);
						$changes[] = "{$label}: \"{$old_value}\" → \"{$new_value}\"";
					}
				}
			}
			if (!empty($changes)) {
				$order = wc_get_order($order_id);
				if ($order) {
					$note_content = "Custom fields changed:\n" . implode("\n", $changes);
					$order->add_order_note($note_content, true);
					if ($order->get_status() !== 'on-hold') {
						$order->update_status('on-hold');
					}
					$current_user = wp_get_current_user();
					$member_name = $current_user->display_name ?: $current_user->user_login;
					$edit_date = current_time('d/m/Y');

					// Email info
					$user_email = get_option('admin_email');
					// $user_email = 'toan444666@gmail.com';
					$subject = "Member {$member_name} Edited Order #{$order_id} – Action Required";

					$headers = [
						'Content-Type: text/html; charset=UTF-8',
						'From: Imperial <impls@singnet.com.sg>'
					];

					// Build message body
					$message = "<p>Hi Team,</p>";
					$message .= "<p>Please be informed that <strong>Member {$member_name}</strong> has made changes to <strong>Order #{$order_id}</strong>.</p>";

					$message .= "<h4>Details of Change:</h4>";

					foreach ($changes as $change) {
						if (preg_match('/^(.*?): "(.*?)" → "(.*?)"$/', $change, $matches)) {
							$label = $matches[1];
							$old = $matches[2];
							$new = $matches[3];

							$message .= "<p><strong>Custom Field Edited:</strong> {$label}<br>";
							$message .= "<strong>Previous Value:</strong> {$old}<br>";
							$message .= "<strong>New Value:</strong> {$new}<br><hr>";
						}
					}
					$message .= "<strong>Edit Date:</strong> {$edit_date}</p>";
					$message .= "<p>Please review the changes in the backend.</p>";

					$message .= "<br><p>Best regards,</p>";
					$message .= "<p><strong>Imperial Chauffeur Services Pte. Ltd</strong></p>";
					$message .= "<p>Email: impls@singnet.com.sg<br>";
					$message .= "Website: <a href='https://imperialchauffeur.sg/'>imperialchauffeur.sg</a></p>";

					wp_mail($user_email, $subject, $message, $headers);
				}
			}




			$redirect_url = remove_query_arg('edit_order', wp_unslash($_SERVER['REQUEST_URI']));
			echo '<script>window.location.href = "' . esc_url($redirect_url) . '";</script>';
			exit;
		}


		if ($is_editing) {
			echo '<form method="post">';
			wp_nonce_field('save_custom_fields_' . $order_id);
			echo '<div class="field-columns">';

			foreach ($custom_fields as $key => $label) {
				$value = get_post_meta($order_id, $key, true);
				echo '<p><label><strong>' . esc_html($label) . ':</strong><br />';
				$type = 'text';
				if ($key === 'pick_up_date') {
					$type = 'date';
				} elseif ($key === 'pick_up_time' || $key === 'eta_time') {
					$type = 'text';
					$id = $key;
				} elseif ($key === 'no_of_passengers' || $key === 'no_of_baggage') {
					$type = 'number';
				}

				echo '<input id="' . esc_attr($id) . '" type="' . esc_attr($type) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width:100%;" />';

				echo '</label></p>';
			}

			echo '</div>';
			echo '<p><button type="submit" class="button button-black ">Save</button></p>';
			echo '</form>';
		} else {
			echo '<div class="field-columns">';
			foreach ($custom_fields as $key => $label) {

				$value = get_post_meta($order_id, $key, true);
				if (! empty($value)) {

					echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
				}
			}
			if ($service_type == "Hourly/Disposal") {
				echo "<p><strong>Duration: </strong> $order_quantity Hours</p>";
			}
			echo '</div>';
			if (!in_array($order->get_status(), ['completed', 'cancelled'])) {
				echo '<p><a class="button button-black" href="' . esc_url(add_query_arg('edit_order', $order_id)) . '">Edit</a></p>';
			}
		}
		?>
	</div>
<?php endif; ?>



<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action('woocommerce_after_order_details', $order);

if ($show_customer_details) {
	wc_get_template('order/order-details-customer.php', array('order' => $order));
}
