<?php
function disable_password_reset()
{
  return false;
}

add_filter('allow_password_reset', 'disable_password_reset');

function remove_processing_status($statuses)
{
  if (isset($statuses['wc-processing'])) {
    unset($statuses['wc-processing']);
  }
  return $statuses;
}
add_filter('wc_order_statuses', 'remove_processing_status');

add_action('woocommerce_thankyou', 'custom_woocommerce_auto_complete_order');
function custom_woocommerce_auto_complete_order($order_id)
{
  if (! $order_id) {
    return;
  }

  $order = wc_get_order($order_id);

  if ($order->has_status('processing')) {
    $order->update_status('completed');
  }
}


add_filter('woocommerce_order_actions', 'confirm_email_woocommerce_order_actions', 10, 2);

add_action('woocommerce_process_shop_order_meta', 'confirm_email_woocommerce_order_action_execute', 50, 2);

function confirm_email_woocommerce_order_actions($actions, $order)
{
  $is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);

  $status = $order->get_status();

  if (!$order->get_customer_id()) {
    $actions['send_order_details'] =  __('Send order details to Visitor', 'send_order_details');
  }

  if ($is_monthly_payment_order || $status != 'on-hold') return $actions;

  unset($actions['send_order_details']);

  $actions['send-confirmation-email'] = __('Send Confirmation Email', 'send-confirmation-email');

  return $actions;
}

/**
 * Save meta box data.
 *
 * @param int $post_id Post ID.
 * @param WP_Post $post Post Object.
 */
function confirm_email_woocommerce_order_action_execute($post_id)
{
  if (filter_input(INPUT_POST, 'wc_order_action') !== 'send-confirmation-email') {
    return;
  }

  $order = wc_get_order($post_id);
  $user_email = $order->get_user()->user_email;

  //function send email to customer when website has new order
  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    'From: Imperial <impls@singnet.com.sg>'
  ];

  $subject = 'Thank you for your order. Your booking has been confirmed – Imperial Chauffeur Services Pte. Ltd';

  $message = "<p>Thank you for your order! We're excited to let you know that your order has been successfully received and is now being processed.</p>";

  $message .= "<br><h3>Preferred Contact Method:</h3>";
  $message .= "<p>OFFICE TELEPHONE +65 6734 0428 (24Hours)</p>";
  $message .= "<p>EMAIL: impls@singnet.com.sg</p>";
  $message .= "<br><p>Our team will review your request and respond within 24 hours. If you have any urgent concerns, feel free to contact us.</p>";
  $message .= "<p>We appreciate your patience and look forward to assisting you.</p><br>";
  $message .= "<p>Best regards,</p>";
  $message .= "<p>Imperial Chauffeur Services Pte. Ltd</p>";
  $message .= "<p>Email: impls@singnet.com.sg</p>";
  $message .= "<p>Website: <a href='https://imperialchauffeur.sg/'>imperialchauffeur.sg</a></p>";

  wp_mail($user_email, $subject, $message, $headers);

  $order->add_order_note(__('Sent confirmation email to customer', 'send-confirmation-email'));
}
