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
	<table class="td" cellspacing="0" cellpadding="6" style="color: #000;width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="color: #000;font-size:13px; ?>;"><?php esc_html_e('Product', 'woocommerce'); ?></th>
				<th class="td" scope="col" style="color: #000;font-size:13px; ?>;"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
				<th class="td" scope="col" style="color: #000;font-size:13px; ?>;"><?php esc_html_e('Price', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($order->get_items() as $item_id => $item) :
				$product_name = $item->get_name();
				$product_quantity = $item->get_quantity();
				$product_price_with_tax = $order->get_formatted_line_subtotal($item);
				$product_price_excl_tax = wc_price($item->get_total());
			?>
				<tr>
					<td class="td" style="color: #000;font-size:13px; ?>;">
						<?php echo esc_html($product_name); ?>
					</td>
					<td class="td" style="color: #000;font-size:13px; ?>;">
						<?php echo esc_html($product_quantity); ?>
					</td>
					<td class="td" style="color: #000;font-size:13px; ?>;">
						<?php echo wp_kses_post($product_price_excl_tax); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<!-- Subtotal -->
			<?php
			$custom_subtotal = 0;
			foreach ($order->get_items() as $item_id => $item) {
				$line_total = $item->get_total();
				$custom_subtotal += $line_total;
			}
			$order_total = $order->get_total();
			?>
			<tr>
				<th class="td" colspan="2" style="color: #000;font-size:13px; ?>; border-top-width: 4px;">
					<?php esc_html_e('Subtotal', 'woocommerce'); ?>
				</th>
				<td class="td" style="color: #000;font-size:13px; ?>; border-top-width: 4px;">
					<?php echo wc_price($custom_subtotal); ?>
				</td>
			</tr>
			
			<!-- GST and CC Fee -->
			<?php
				if(!empty($order->get_items("tax"))){
					foreach ($order->get_items("tax") as $itm_id => $itm) {
			?>
			<tr>
				<th class="td" colspan="2" style="color: #000;font-size:13px;">
					<?php echo esc_html($itm->get_label()); ?>
				</th>
				<td class="td" style="color: #000;font-size:13px;">
					<?php 
						echo wc_price($itm->get_tax_total()); 
					?>
				</td>
			</tr>
			<?php }}; ?>
			
			<!-- Total -->
			<tr>
				<th class="td" colspan="2" style="color: #000;font-size:13px; ?>;">
					<?php esc_html_e('Grand Total', 'woocommerce'); ?>
				</th>
				<td class="td" style="color: #000;font-size:13px; ?>;">
					<?php echo wc_price($order_total); ?>
				</td>
			</tr>
		</tfoot>

	</table>
</div>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email); ?>