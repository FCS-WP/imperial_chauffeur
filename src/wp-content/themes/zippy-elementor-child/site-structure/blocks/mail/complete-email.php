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
                $line_total = $item->get_total();
                $line_total_excl_tax = $line_total / (1 + ($tax_rate / 100));
                $custom_subtotal += $line_total_excl_tax;
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
<ul style="font-size:13px">
    <li>Service type: <?php echo $service_type ?></li>
    <?php  if ($service_type == "Hourly/Disposal") { ?>
        <li>Usage time: <?php echo $time_use; ?> Hours</li>
    <?php } ?>
    <li>Pick up date: <?php echo get_post_meta($order_id, "pick_up_date", true) ?></li>
    <li>Pick up time: <?php echo get_post_meta($order_id, "pick_up_time", true) ?></li>
    <?php if ($service_type == "Airport Arrival Transfer" || $service_type = "Airport Departure Transfer") { ?>
        <li>Flight details: <?php echo get_post_meta($order_id, "flight_details", true) ?></li>
        <li><?php echo $eta_label ?>: <?php echo get_post_meta($order_id, "eta_time", true) ?></li>
    <?php } ?>
    <li>Pick up location: <?php echo get_post_meta($order_id, "pick_up_location", true) ?></li>
    <li>Drop off location: <?php echo get_post_meta($order_id, "drop_off_location", true) ?></li>
    <li>No of pax: <?php echo get_post_meta($order_id, "no_of_passengers", true) ?></li>
    <li>No of luggages: <?php echo get_post_meta($order_id, "no_of_baggage", true) ?></li>
    <li>Special requests: <?php echo get_post_meta($order_id, "special_requests", true) ?></li>
    <?php if($member_type == 1){ ?>
        <li>Staff name: <?php echo get_post_meta($order_id, "staff_name", true) ?></li>
    <?php } ?>
</ul>

<h3>Preferred Contact Method:</h3>
<ul style="font-size:13px">
    <li>OFFICE TELEPHONE +65 6734 0428 (24Hours)</li>
    <li>EMAIL: impls@singnet.com.sg</li>
    <li>Best regards,</li>
    <li>Imperial Chauffeur Services Pte. Ltd</li>
    <li>Email: impls@singnet.com.sg</li>
    <li>Website: <a href='https://imperialchauffeur.sg/'>imperialchauffeur.sg</a></li>
</ul>