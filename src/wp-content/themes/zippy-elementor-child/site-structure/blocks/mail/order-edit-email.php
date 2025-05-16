<?php
    $order_id = $order->get_id();
    $custom_fields = array(
        'service_type'       => __('Service Type', 'woocommerce'),
        'flight_details'     => __('Flight Details', 'woocommerce'),
        'eta_time'           => __('ETE/ETA Time', 'woocommerce'),
        'no_of_passengers'   => __('No Of Passengers', 'woocommerce'),
        'no_of_baggage'      => __('No Of Baggage', 'woocommerce'),
        'key_member'         => __('Customer Type', 'woocommerce'),
        'pick_up_date'       => __('Pick Up Date', 'woocommerce'),
        'pick_up_time'       => __('Pick Up Time', 'woocommerce'),
        'pick_up_location'   => __('Pick Up Location', 'woocommerce'),
        'drop_off_location'  => __('Drop Off Location', 'woocommerce'),
        'staff_name'  			=> __('Staff Name', 'woocommerce'),
    );

?>
<div style="padding:70px 0;background-color:#f7f7f7">
    <div style="color:#000;width:600px;margin:0 auto">
        <div style="color:#000;background:#e91e21;color:#fff;padding:36px 48px;font-size:30px;border-radius: 3px 3px 0 0;">
            <strong>Order information updated</strong>
        </div>

        <div style="color:#000;background:#fff;padding:48px 48px 32px;font-family: Arial, sans-serif; font-size:13px; color:#000;margin:0 auto;text-align:left;border-radius: 0 0 3px 3px;">
            <p style="color:#000;font-size:13px;">Hi <?php echo esc_html($order->get_billing_first_name()); ?>,</p>
            <p style="color:#000;font-size:13px;margin-bottom:20px">The information in bold has been changed in your order.</p>
            <h3 style="font-size:15px;color:#000">Order Information:</h3>
            <div style="margin-left:20px">
                <?php
                    foreach ($custom_fields as $key => $value) {
                        echo "<p style='font-size: 13px;color:#000;'>";
                            if(trim($old_data[$key]) !== trim($new_data[$key])){
                                echo "<strong>" . $value . ": " . $old_data[$key] . " → " . $new_data[$key] . "</strong>";
                            } else {
                                echo $value . ": " . $old_data[$key];
                            }
                        echo "</p>";
                    }
                ?>
            </div>
            <h3 style="color:#000;font-size:15px;color:#e91e21;margin-top: 40px;">Billing address</h3>
            <div style="color:#000;font-size:13px;border:1px solid #e0e0e0;padding:15px;text-align:left">
                <?php echo esc_html($order->get_formatted_billing_full_name()); ?><br>
                <?php echo esc_html($order->get_billing_phone()); ?><br>
                <a href="mailto:<?php echo esc_attr($order->get_billing_email()); ?>">
                    <?php echo esc_html($order->get_billing_email()); ?>
                </a>
            </div>
            <p style="color:#000;margin-top:30px;font-size:13px;">Thanks for shopping with us.</p>
        </div>
    </div>
</div>

