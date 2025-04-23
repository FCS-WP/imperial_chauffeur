<?php

/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

$text_align = is_rtl() ? 'right' : 'left';
$tax_percent = get_tax_percent();
$tax_rate_label = $tax_percent->tax_rate_name;
$tax_rate = intval($tax_percent->tax_rate);

//do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );
?>
<br>
<h2>
	<?php
	if ($sent_to_admin) {
		$before = '<a class="link" href="' . esc_url($order->get_edit_order_url()) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/* translators: %s: Order ID. */
	echo wp_kses_post($before . sprintf(__('[Order #%s]', 'woocommerce') . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format('c'), wc_format_datetime($order->get_date_created())));
	?>
</h2>

<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Product', 'woocommerce'); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Price', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($order->get_items() as $item_id => $item) :
				$product_name = $item->get_name();
				$product_quantity = $item->get_quantity();
				$product_price_with_tax = $order->get_formatted_line_subtotal($item);

				$price_excl_tax = wc_format_decimal($item->get_total() / (1 + ($tax_rate / 100)));
				$product_price_excl_tax = wc_price($price_excl_tax);

			?>
				<tr>
					<td class="td" style="text-align:<?php echo esc_attr($text_align); ?>;">
						<?php echo esc_html($product_name); ?>
					</td>
					<td class="td" style="text-align:<?php echo esc_attr($text_align); ?>;">
						<?php echo esc_html($product_quantity); ?>
					</td>
					<td class="td" style="text-align:<?php echo esc_attr($text_align); ?>;">
						<?php echo wp_kses_post($product_price_excl_tax); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php
			$custom_subtotal = 0;
			foreach ($order->get_items() as $item_id => $item) {
				$line_total = $item->get_total();
				$line_total_excl_tax = $line_total / (1 + ($tax_rate / 100));
				$custom_subtotal += $line_total_excl_tax;
			}

			$order_total = $order->get_total();
			?>

			<tr>
				<th class="td" colspan="2" style="text-align:<?php echo esc_attr($text_align); ?>; border-top-width: 4px;">
					<?php esc_html_e('Subtotal', 'woocommerce'); ?>
				</th>
				<td class="td" style="text-align:<?php echo esc_attr($text_align); ?>; border-top-width: 4px;">
					<?php echo wc_price($custom_subtotal); ?>
				</td>
			</tr>

			<tr>
				<th class="td" colspan="2" style="text-align:<?php echo esc_attr($text_align); ?>;">
					<?php echo esc_html($tax_rate_label); ?>
				</th>
				<td class="td" style="text-align:<?php echo esc_attr($text_align); ?>;">
					<?php echo esc_html($tax_rate) . '%'; ?>
				</td>
			</tr>

			<tr>
				<th class="td" colspan="2" style="text-align:<?php echo esc_attr($text_align); ?>;">
					<?php esc_html_e('Total', 'woocommerce'); ?>
				</th>
				<td class="td" style="text-align:<?php echo esc_attr($text_align); ?>;">
					<?php echo wc_price($order_total); ?>
				</td>
			</tr>
		</tfoot>

	</table>
</div>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email); ?>