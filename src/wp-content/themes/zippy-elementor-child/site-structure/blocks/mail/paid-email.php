<?php
    $order_id = $order->get_id();
    $subtotal = $order->get_subtotal();
    $user_name = !empty($user->display_name) ? $user->display_name : $order->get_formatted_billing_full_name();
    $user_email = !empty($user->user_email) ? $user->user_email : $order->get_billing_email();
    $service_type = $order->get_meta("service_type");
    $member_type = $order->get_meta("member_type");

    $customer_type = $member_type == 1 ? "Member" : "Visitor";

    $eta_label = $service_type == "Airport Departure Transfer" ? "ETD" : "ETA";

    $pick_up_location = $order->get_meta("pick_up_location");
    $flight_details = $order->get_meta("flight_details");
    $drop_off_location = $order->get_meta("drop_off_location");
    $eta_time = $order->get_meta("eta_time");

    $items = $order->get_items();
    
?>

<style>
    p{
        margin: 0 0 16px;
        font-size: 13px;
        color: #000;
    }
</style>
<p style="font-size:13px;color:#000">Dear Admin</p>
<p style="font-size:13px;color:#000">You have received payment for order <?php echo $order_id;?></p>
<p style="font-size:13px;color:#000">Here are the details:</p>

<h3 style="color:#e91e21;font-size:15px">
    [Order #<?php echo $order->get_order_number(); ?>] (<?php echo $order->get_date_created()->setTimezone( new DateTimeZone('Asia/Singapore') )->format('d/m/Y'); ?>)
</h3>

<?php 
    $data = [
        "order" => $order,
        "type" => "complete_email"
    ];
    echo render_email_template("order-detail-table", $data) 
?>

<h3 style="font-size:15px;color:#000">Order Information:</h3>

<p style="font-size:13px;color:#000">Order no: <?php echo "#" . $order_id; ?></p>
<p style="font-size:13px;color:#000">Customer Type: <?php echo $customer_type; ?></p>
<p style="font-size:13px;color:#000">Customer: <?php echo $order->get_formatted_billing_full_name() . " / " . $user_email . " & " . $order->get_billing_phone(); ?></p>
<p style="font-size:13px;color:#000">Service type: <?php echo $service_type ?></p>
<p style="font-size:13px;color:#000">Vehicle Type: <?php echo reset($items)->get_name() ?></p>

<?php  if ($service_type == "Hourly/Disposal") { ?>
    <p style="font-size:13px;color:#000">Usage time: <?php echo reset($items)->get_quantity(); ?> Hours</p>
<?php } ?>

<p style="font-size:13px;color:#000">Pick up date: <?php echo $order->get_meta("pick_up_date") ?></p>
<p style="font-size:13px;color:#000">Pick up time: <?php echo $order->get_meta("pick_up_time") ?></p>


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

<p style="font-size:13px;color:#000">No of pax: <?php echo $order->get_meta("no_of_passengers") ?></p>
<p style="font-size:13px;color:#000">No of luggages: <?php echo $order->get_meta("no_of_baggage") ?></p>
<p style="font-size:13px;color:#000">Special requests: <?php echo $order->get_meta("special_requests") ?></p>

<?php if($member_type == 1){ ?>
    <p style="font-size:13px;color:#000">Staff name: <?php echo $order->get_meta("staff_name") ?></p>
<?php } ?>


<?php
    echo get_email_signature();
?>
