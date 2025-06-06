<?php
if (! defined('ABSPATH')) exit;

$is_monthly = $order->get_meta('is_monthly_payment_order');
if (! $is_monthly) return;

$items = $order->get_items();
$subtotal = $order->get_subtotal();
$gst = $subtotal * 0.09;
$cc_fee = $subtotal * 0.05;
$total = $subtotal + $gst + $cc_fee;
?>
<div style="padding:70px 0;">
    <div style="color:#000;width:600px;margin:0 auto">
        <div style="color:#000;background:#e91e21;color:#fff;padding:36px 48px;font-size:30px;border-radius: 3px 3px 0 0;">
            <strong>Thank you for booking with us</strong>
        </div>

        <div style="color:#000;background:#fff;padding:48px 48px 32px;font-family: Arial, sans-serif; font-size:13px; color:#000;margin:0 auto;text-align:left;border-radius: 0 0 3px 3px;">
            <p style="color:#000;font-size:13px;">Hi <?php echo esc_html($order->get_billing_first_name()); ?>,</p>
            <p style="color:#000;font-size:13px;">We have finished processing your order.</p>

            <h2 style="color:#000;color:#e91e21">
                [Order #<?php echo $order->get_order_number(); ?>] (<?php echo $order->get_date_created()->setTimezone(new DateTimeZone('Asia/Singapore'))->format('d/m/Y'); ?>)
            </h2>

            <table cellspacing="0" cellpadding="6" style="color:#000;width:100%;border:2px solid #e5e5e5;border-collapse:collapse;text-align:left" border="2">
                <thead>
                    <tr>
                        <th style="color:#000;font-size:13px;padding:12px;text-align:left">Product</th>
                        <th style="color:#000;font-size:13px;padding:12px;text-align:left">Quantity</th>
                        <th style="color:#000;font-size:13px;padding:12px;text-align:left">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) :
                        $product = $item->get_product();
                        $item_name = $item->get_name();
                        $qty = $item->get_quantity();
                        $price = $item->get_total();
                    ?>
                        <tr>
                            <td style="color:#000;font-size:13px;padding:12px;"><?php echo esc_html($item_name); ?></td>
                            <td style="color:#000;font-size:13px;text-align:left;padding:12px;"><?php echo esc_html($qty); ?></td>
                            <td style="color:#000;font-size:13px;text-align:left;padding:12px;">$<?php echo number_format($price, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="color:#000;font-size:13px;text-align:left;padding:12px;"><strong>Subtotal:</strong></td>
                        <td style="color:#000;font-size:13px;text-align:left;padding:12px;">$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <h3 style="color:#000;font-size:13px;color:#e91e21;margin-top: 40px;">Billing address</h3>
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
