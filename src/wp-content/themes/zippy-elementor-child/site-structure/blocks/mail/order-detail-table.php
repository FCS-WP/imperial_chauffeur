<?php
$fees = $order->get_items("fee");
$total_custom_fee = 0;
$total_fee = 0;
$items = $order->get_items();
$cc_fee_amount = get_option("zippy_cc_fee_amount");
$total_custom_fee = 0;
$custom_subtotal = 0;
$cc_fee = 0;
$gst = 0;
?>
<table cellspacing="0" cellpadding="6" style="border:1px solid #e5e5e5;vertical-align:middle;color:#000;width:502px" border="1">
    <thead>
        <tr>
            <th scope="col" align="left" style="border:1px solid #e5e5e5;padding:12px;font-size:13px;color:#000;text-align:left">Product</th>
            <th scope="col" align="left" style="border:1px solid #e5e5e5;padding:12px;font-size:13px;color:#000;text-align:left">Quantity</th>
            <th scope="col" align="left" style="border:1px solid #e5e5e5;padding:12px;font-size:13px;color:#000;text-align:left">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($items as $item) :
            $product = $item->get_product();
            $item_name = $item->get_name();
            $qty = $item->get_quantity();
            $price = $item->get_total();
        ?>
            <tr>
                <td align="left" style="border:1px solid #e5e5e5;padding:12px;font-size:13px;color:#000;"><?php echo esc_html($item_name); ?></td>
                <td align="left" style="border:1px solid #e5e5e5;text-align:left;padding:12px;font-size:13px;color:#000;"><?php echo esc_html($qty); ?></td>
                <td align="left" style="border:1px solid #e5e5e5;text-align:left;padding:12px;font-size:13px;color:#000;">$<?php echo number_format($price, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>

        <!-- Another  Fee -->
        <?php
        if (!empty($fees)) {
            foreach ($fees as $itm_id => $itm) {
                if ($itm->get_name() !== get_option("zippy_cc_fee_name")) {
                    $total_custom_fee += $itm->get_total();
        ?>
                    <tr>
                        <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                            <?php echo esc_html($itm->get_name()); ?>
                        </th>
                        <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                            <?php
                            echo wc_price($itm->get_total());
                            ?>
                        </td>
                    </tr>
        <?php }
            }
        }; ?>

        <!-- Subtotal -->
        <?php
        foreach ($items as $item_id => $item) {
            $pne_total = $item->get_total();
            $custom_subtotal += $pne_total;
        }
        $subtotal = $custom_subtotal + $total_custom_fee;
        ?>
        <tr>
            <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left;border-top-width:4px" align="left">
                <?php esc_html_e('Subtotal', 'woocommerce'); ?>
            </th>
            <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left;border-top-width:4px" align="left">
                <?php echo wc_price($subtotal); ?>
            </td>
        </tr>

        <!-- GST -->
        <?php
        if (!empty($order->get_items("tax"))) {
            foreach ($order->get_items("tax") as $itm_id => $itm) {
                $gst_rate = $itm["rate_percent"];
                $gst = ($gst_rate * $subtotal) / 100;
        ?>
                <?php if ($gst > 0): ?>
                    <tr>
                        <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                            <?php echo esc_html($itm->get_label()); ?>
                        </th>
                        <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                            <?php
                            echo wc_price($gst);
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>

        <?php }
        }; ?>

        <!-- CC Fee -->
        <?php
        if (!empty($fees)) {
            foreach ($fees as $itm_id => $itm) {
                if ($itm->get_name() == get_option("zippy_cc_fee_name")) {
                    $cc_fee = ($cc_fee_amount * ($gst + $subtotal)) / 100;
        ?>
                    <tr>
                        <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                            <?php echo esc_html($itm->get_name()); ?>
                        </th>
                        <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                            <?php
                            echo wc_price($cc_fee);
                            ?>
                        </td>
                    </tr>
        <?php }
            }
        }; ?>

        <!-- Total -->
        <tr>
            <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                <?php esc_html_e('Grand Total', 'woocommerce'); ?>
            </th>
            <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                <?php echo wc_price($subtotal + $gst + $cc_fee); ?>
            </td>
        </tr>

        <!-- Paymend Method -->
        <?php if ($type == "complete_email") { ?>
            <tr>
                <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                    <?php esc_html_e('Payment Method', 'woocommerce'); ?>
                </th>
                <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                    <?php echo esc_html_e($order->get_payment_method_title()); ?>
                </td>
            </tr>
        <?php } ?>
    </tfoot>
</table>
