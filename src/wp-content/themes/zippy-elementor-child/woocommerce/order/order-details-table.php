<?php
$order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$order_quantity = 0;
$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
$is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);
$service_type = get_post_meta($order->get_id(), 'service_type', true);
$order_date = $order->get_date_created();
?>

<section class="woocommerce-order-details">
  <h2 class="woocommerce-order-details__title"><?php esc_html_e('Order details', 'woocommerce'); ?></h2>

  <?php if ($is_monthly_payment_order && $order_date) : ?>
    <p><strong>Order Created:</strong> <?php echo esc_html($order_date->date('H:i d/m/Y')); ?></p>
  <?php endif; ?>

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
      ?>
    </tbody>
  </table>
</section>
