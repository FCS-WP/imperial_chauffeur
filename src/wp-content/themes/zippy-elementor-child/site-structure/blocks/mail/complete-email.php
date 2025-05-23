<?php
    $order_id = $order->get_id();
    $items = $order->get_items();
    $subtotal = $order->get_subtotal();
    $user_name = !empty($user->display_name) ? $user->display_name : $order->get_formatted_billing_full_name();
    $user_email = !empty($user->user_email) ? $user->user_email : $order->get_billing_email();
    $service_type = get_post_meta($order_id, "service_type", true);
    $member_type = $order->get_meta("member_type");

    $customer_type = $member_type == 1 ? "Member" : "Visitor";

    $eta_label = $service_type == "Airport Departure Transfer" ? "ETD" : "ETA";
    $total_fee = 0;

    $pick_up_location = get_post_meta($order_id, "pick_up_location", true);
    $flight_details = get_post_meta($order_id, "flight_details", true);
    $drop_off_location = get_post_meta($order_id, "drop_off_location", true);
    $eta_time = get_post_meta($order_id, "eta_time", true);
?>

<style>
    p{
        margin: 0 0 16px;
        font-size: 13px;
        color: #000;
    }
</style>
<p style="font-size:13px;color:#000">Hi <?php echo $user_name ?></p>
<p style="font-size:13px;color:#000">Your payment has been received and we will send the driver details to you one day before the booking.</p>
<p style="font-size:13px;color:#000">If you have any queries, kindly contact us.</p>

<h3 style="color:#e91e21;font-size:15px">
    [Order #<?php echo $order->get_order_number(); ?>] (<?php echo $order->get_date_created()->setTimezone( new DateTimeZone('Asia/Singapore') )->format('d/m/Y'); ?>)
</h3>
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
        foreach ( $items as $item ) :
            $product = $item->get_product();
            $item_name = $item->get_name();
            $qty = $item->get_quantity();
            $price = $item->get_total();
        ?>
            <tr>
                <td align="left" style="border:1px solid #e5e5e5;padding:12px;font-size:13px;color:#000;"><?php echo esc_html( $item_name ); ?></td>
                <td align="left" style="border:1px solid #e5e5e5;text-align:left;padding:12px;font-size:13px;color:#000;"><?php echo esc_html( $qty ); ?></td>
                <td align="left" style="border:1px solid #e5e5e5;text-align:left;padding:12px;font-size:13px;color:#000;">$<?php echo number_format( $price, 2 ); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>

        <!-- Subtotal -->
        <?php
            $custom_subtotal = 0;
            foreach ($items as $item_id => $item) {
                $pne_total = $item->get_total();
                $custom_subtotal += $pne_total;
            }
            $order_total = $order->get_total();
        ?>
        <tr>
            <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left;border-top-width:4px" align="left">
                <?php esc_html_e('Subtotal', 'woocommerce'); ?>
            </th>
            <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left;border-top-width:4px" align="left">
                <?php echo wc_price($custom_subtotal); ?>
            </td>
        </tr>
        
        <!-- CC Fee -->
        <?php
            if(!empty($order->get_items("fee"))){
                foreach ($order->get_items("fee") as $itm_id => $itm) {
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
        <?php }}; ?>

        <!-- GST -->
        <?php
            if(!empty($order->get_items("tax"))){
                foreach ($order->get_items("tax") as $itm_id => $itm) {
        ?>
        <tr>
            <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                <?php echo esc_html($itm->get_label()); ?>
            </th>
            <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                <?php 
                    echo wc_price($itm->get_tax_total()); 
                ?>
            </td>
        </tr>
        <?php }}; ?>

        <!-- Total -->
        <tr>
            <th colspan="2" style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                <?php esc_html_e('Grand Total', 'woocommerce'); ?>
            </th>
            <td style="border:1px solid #e5e5e5;vertical-align:middle;padding:12px;color:#000;font-size:13px;text-align:left" align="left">
                <?php echo wc_price($order_total); ?>
            </td>
        </tr>
    </tfoot>
</table>

<h3 style="font-size:15px;color:#000">Order Information:</h3>

<p style="font-size:13px;color:#000">Order no: <?php echo "#" . $order_id; ?></p>
<p style="font-size:13px;color:#000">Customer Type: <?php echo $customer_type; ?></p>
<p style="font-size:13px;color:#000">Customer: <?php echo $order->get_formatted_billing_full_name() . " / " . $user_email . " & " . $order->get_billing_phone(); ?></p>
<p style="font-size:13px;color:#000">Service type: <?php echo $service_type ?></p>
<p style="font-size:13px;color:#000">Vehicle Type: <?php echo reset($items)->get_name() ?></p>

<?php  if ($service_type == "Hourly/Disposal") { ?>
    <p style="font-size:13px;color:#000">Usage time: <?php echo reset($items)->get_quantity(); ?> Hours</p>
<?php } ?>

<p style="font-size:13px;color:#000">Pick up date: <?php echo get_post_meta($order_id, "pick_up_date", true) ?></p>
<p style="font-size:13px;color:#000">Pick up time: <?php echo get_post_meta($order_id, "pick_up_time", true) ?></p>


<?php if ($service_type == "Airport Arrival Transfer") { ?>
    <p style='font-size:13px;color:#000'>Pick up location: <?php echo $pick_up_location ?></p>
    <p style='font-size:13px;color:#000'>Flight details: <?php echo $flight_details ?></p>
    <p style='font-size:13px;color:#000'>ETA: <?php echo $eta_time ?></p>
    <p style='font-size:13px;color:#000'>Drop off location: <?php echo $drop_off_location ?></p>
<?php } elseif ($service_type == "Airport Departure Transfer") { ?>
      <p style='font-size:13px;color:#000'>Pick up location: <?php echo $pick_up_location ?></p>
      <p style='font-size:13px;color:#000'>Drop off location: <?php echo $drop_off_location ?></p>
      <p style='font-size:13px;color:#000'>Flight details: <?php echo $flight_details ?></p>
      <p style='font-size:13px;color:#000'>ETD: <?php echo $eta_time ?></p>
<?php } else { ?>
      <p style='font-size:13px;color:#000'>Pick up location: <?php echo $pick_up_location ?></p>
      <p style='font-size:13px;color:#000'>Drop off location: <?php echo $drop_off_location ?></p>
 <?php } ?>

<p style="font-size:13px;color:#000">No of pax: <?php echo get_post_meta($order_id, "no_of_passengers", true) ?></p>
<p style="font-size:13px;color:#000">No of luggages: <?php echo get_post_meta($order_id, "no_of_baggage", true) ?></p>
<p style="font-size:13px;color:#000">Special requests: <?php echo get_post_meta($order_id, "special_requests", true) ?></p>

<?php if($member_type == 1){ ?>
    <p style="font-size:13px;color:#000">Staff name: <?php echo get_post_meta($order_id, "staff_name", true) ?></p>
<?php } ?>


<?php
    echo get_email_signature();
?>