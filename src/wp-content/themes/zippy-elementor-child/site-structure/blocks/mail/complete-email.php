<?php 
    $order = wc_get_order($order_id);
    $items = $order->get_items();
    $subtotal = $order->get_subtotal();
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

<h3>Preferred Contact Method:</h3>
<ul>
    <li>OFFICE TELEPHONE +65 6734 0428 (24Hours)</li>
    <li>EMAIL: impls@singnet.com.sg</li>
    <li>Best regards,</li>
    <li>Imperial Chauffeur Services Pte. Ltd</li>
    <li>Email: impls@singnet.com.sg</li>
    <li>Website: <a href='https://imperialchauffeur.sg/'>imperialchauffeur.sg</a></li>
</ul>