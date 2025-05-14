<?php
    $order_id = $order->get_id();
    $items = $order->get_items();
    $subtotal = $order->get_subtotal();
    $user_name = !empty($user->display_name) ? $user->display_name : $order->get_formatted_billing_full_name();
    $user_email = !empty($user->user_email) ? $user->user_email : $order->get_billing_email();
    $service_type = get_post_meta($order_id, "service_type", true);
    $member_type = $order->get_meta("member_type");
    $time_use = reset($items)->get_quantity();
    $tax_percent = get_tax_percent();
    $tax_rate_label = $tax_percent->tax_rate_name;
    $tax_rate = intval($tax_percent->tax_rate);

    $eta_label = $service_type == "Airport Departure Transfer" ? "ETD" : "ETA";

?>
<p style="font-size:13px">Hi <?php echo $user_name ?></p>
<p style="font-size:13px">Your payment has been received and we will send the driver details to you one day before the booking.</p>
<p style="font-size:13px">If you have any queries, kindly contact us.</p>

<h3 style="color:#e91e21;">
    [Order #<?php echo $order->get_order_number(); ?>] (<?php echo $order->get_date_created()->setTimezone( new DateTimeZone('Asia/Singapore') )->format('d/m/Y'); ?>)
</h3>
<table cellspacing="0" cellpadding="6" style="width:600px;border:2px solid #e5e5e5;border-collapse:collapse;text-align:left" border="2">
    <thead>
        <tr>
            <th style="padding:12px;font-size:13px;text-align:left">Product</th>
            <th style="padding:12px;font-size:13px;text-align:center">Quantity</th>
            <th style="padding:12px;font-size:13px;text-align:right">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ( $items as $item ) :
            $product = $item->get_product();
            $item_name = $item->get_name();
            $qty = $item->get_quantity();
            $price = $item->get_total();
        ?>
            <tr>
                <td style="padding:12px;font-size:13px;"><?php echo esc_html( $item_name ); ?></td>
                <td style="text-align:center;padding:12px;font-size:13px;"><?php echo esc_html( $qty ); ?></td>
                <td style="text-align:right;padding:12px;font-size:13px;">$<?php echo number_format( $price, 2 ); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <?php
            $custom_subtotal = 0;
            foreach ($items as $item_id => $item) {
                $pne_total = $item->get_total();
                $pne_total_excl_tax = $pne_total / (1 + ($tax_rate / 100));
                $custom_subtotal += $pne_total_excl_tax;
            }
            $order_total = $order->get_total();
        ?>
        <tr>
            <th class="td" colspan="2" style="border-top-width:4px;font-size:13px;">
                <?php esc_html_e('Subtotal', 'woocommerce'); ?>
            </th>
            <td class="td" style="text-align:right; border-top-width:4px;font-size:13px;">
                <?php echo wc_price($custom_subtotal); ?>
            </td>
        </tr>

        <tr>
            <th class="td" colspan="2" style="font-size:13px;">
                <?php echo esc_html($tax_rate_label); ?>
            </th>
            <td class="td" style="text-align:right;font-size:13px;">
                <?php echo esc_html($tax_rate) . '%'; ?>
            </td>
        </tr>

        <tr>
            <th class="td" colspan="2" style="font-size:13px;">
                <?php esc_html_e('Total', 'woocommerce'); ?>
            </th>
            <td class="td" style="text-align:right;font-size:13px;">
                <?php echo wc_price($order_total); ?>
            </td>
        </tr>
    </tfoot>
</table>

<h3>Order Information:</h3>
<p style="font-size:13px">Service type: <?php echo $service_type ?></p>
<?php  if ($service_type == "Hourly/Disposal") { ?>
    <p style="font-size:13px">Usage time: <?php echo $time_use; ?> Hours</p>
<?php } ?>
<p style="font-size:13px">Pick up date: <?php echo get_post_meta($order_id, "pick_up_date", true) ?></p>
<p style="font-size:13px">Pick up time: <?php echo get_post_meta($order_id, "pick_up_time", true) ?></p>
<?php if ($service_type == "Airport Arrival Transfer" || $service_type = "Airport Departure Transfer") { ?>
    <p style="font-size:13px">Fpght details: <?php echo get_post_meta($order_id, "fpght_details", true) ?></p>
    <p style="font-size:13px"><?php echo $eta_label ?>: <?php echo get_post_meta($order_id, "eta_time", true) ?></p>
<?php } ?>
<p style="font-size:13px">Pick up location: <?php echo get_post_meta($order_id, "pick_up_location", true) ?></p>
<p style="font-size:13px">Drop off location: <?php echo get_post_meta($order_id, "drop_off_location", true) ?></p>
<p style="font-size:13px">No of pax: <?php echo get_post_meta($order_id, "no_of_passengers", true) ?></p>
<p style="font-size:13px">No of luggages: <?php echo get_post_meta($order_id, "no_of_baggage", true) ?></p>
<p style="font-size:13px">Special requests: <?php echo get_post_meta($order_id, "special_requests", true) ?></p>
<?php if($member_type == 1){ ?>
    <p style="font-size:13px">Staff name: <?php echo get_post_meta($order_id, "staff_name", true) ?></p>
<?php } ?>

<h3>Preferred Contact Method:</h3>
<p style="font-size:13px">Imperial Chauffeur Services Pte. Ltd</p>
<p style="font-size:13px">OFFICE TELEPHONE +65 6734 0428 (24Hours)</p>
<p style="font-size:13px">EMAIL: impls@singnet.com.sg</p>
<p style="font-size:13px">Website: <a href='https://imperialchauffeur.sg/'>imperialchauffeur.sg</a></p>
<p style="font-size:13px">Best regards,</p>