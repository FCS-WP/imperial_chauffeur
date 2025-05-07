<?php
    $order_id = $order->get_id();
    $items = $order->get_items();
    $subtotal = $order->get_subtotal();
    $user_name = !empty($user->display_name) ? $user->display_name : $order->get_formatted_billing_full_name();
    $service_type = get_post_meta($order_id, "service_type", true);
    $member_type = $order->get_meta("member_type");
?>
<p>Hi <?php echo $user_name ?></p>
<p>Your payment has been received and we will send the driver details to you one day before the booking.</p>
<p>If you have any queries, kindly contact us.</p>

<h3 style="color:#e91e21">
    [Order #<?php echo $order->get_order_number(); ?>] (<?php echo $order->get_date_created()->setTimezone( new DateTimeZone('Asia/Singapore') )->format('d/m/Y'); ?>)
</h3>
<table cellspacing="0" cellpadding="6" style="width:600px;border:2px solid #e5e5e5;border-collapse:collapse;text-align:left" border="2">
    <thead>
        <tr>
            <th style="padding: 12px;text-align:left">Product</th>
            <th style="padding: 12px;text-align:center">Quantity</th>
            <th style="padding: 12px;text-align:right">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $items as $item ) :
            $product = $item->get_product();
            $item_name = $item->get_name();
            $qty = $item->get_quantity();
            $price = $item->get_total();
        ?>
            <tr>
                <td style="padding: 12px;"><?php echo esc_html( $item_name ); ?></td>
                <td style="text-align:center;padding: 12px;"><?php echo esc_html( $qty ); ?></td>
                <td style="text-align:right;padding: 12px;">$<?php echo number_format( $price, 2 ); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:left;padding: 12px;"><strong>Subtotal:</strong></td>
            <td style="text-align:right;padding: 12px;">$<?php echo number_format( $subtotal, 2 ); ?></td>
        </tr>
</table>

<h3>Order Information:</h3>
<ul>
    <li>Service Type: <?php echo $service_type ?></li>
    <?php  if ($service_type == "Hourly/Disposal") { ?>
        <li>Usage time: $time_use Hours</li>";
    <?php } ?>
    <li>Pick up Date: <?php echo get_post_meta($order_id, "pick_up_date", true) ?></li>
    <li>Pick up Time: <?php echo get_post_meta($order_id, "pick_up_time", true) ?></li>
    <?php if ($service_type == "Airport Arrival Transfer" || $service_type = "Airport Departure Transfer") { ?>
        <li>Flight details: <?php echo get_post_meta($order_id, "flight_details", true) ?></li>
        <li>ETA: <?php echo get_post_meta($order_id, "eta_time", true) ?></li>
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
<ul>
    <li>OFFICE TELEPHONE +65 6734 0428 (24Hours)</li>
    <li>EMAIL: impls@singnet.com.sg</li>
    <li>Best regards,</li>
    <li>Imperial Chauffeur Services Pte. Ltd</li>
    <li>Email: impls@singnet.com.sg</li>
    <li>Website: <a href='https://imperialchauffeur.sg/'>imperialchauffeur.sg</a></li>
</ul>