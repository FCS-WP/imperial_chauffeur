<?php

function remove_processing_status($statuses)
{
  if (isset($statuses['wc-processing'])) {
    unset($statuses['wc-processing']);
  }
  return $statuses;
}

function add_confirmed_status($statuses)
{

  $statuses['wc-confirmed'] = __('Confirmed', 'send_order_details');
  return $statuses;
}

add_filter('wc_order_statuses', 'remove_processing_status');
add_filter('wc_order_statuses', 'add_confirmed_status');


function register_custom_order_status()
{
  register_post_status('wc-confirmed', array(
    'label'                     => 'Confirmed',
    'public'                    => true,
    'exclude_from_search'       => false,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop('Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>')
  ));
}
add_action('init', 'register_custom_order_status');

add_action('woocommerce_thankyou', 'custom_woocommerce_auto_complete_order');
function custom_woocommerce_auto_complete_order($order_id)
{
  if (! $order_id) {
    return;
  }

  $order = wc_get_order($order_id);

  if ($order->has_status('processing') || $order->has_status('on-hold')) {
    $order->update_status('completed');
  }
}


add_filter('woocommerce_order_actions', 'confirm_email_woocommerce_order_actions', 10, 2);

function confirm_email_woocommerce_order_actions($actions, $order)
{
  $is_monthly = $order->get_meta('is_monthly_payment_order', true);

  $status = $order->get_status();
  $is_guest = !$order->get_customer_id();
  unset($actions['regenerate_download_permissions']);
  unset($actions['send_order_details_admin']);

  if ($is_guest) {
    $actions['send_order_details'] = __('Send order details to Visitor', 'send_order_details');
  }

  if ($is_monthly || $status !== 'on-hold' || $is_guest) {
    if ($status == 'completed') {
      $actions['send_completed_order'] = $is_guest
        ? __('Send completed order to Visitor', 'send_completed_order')
        : __('Send completed order to Member', 'send_completed_order');
    }
    return apply_filters('custom_wc_order_actions', $actions, $order);
  }

  unset($actions['send_order_details']);

  $actions['send-confirmation-email'] = __('Send confirmation email to Member', 'send-confirmation-email');


  return apply_filters('custom_wc_order_actions', $actions, $order);
}


add_action('woocommerce_process_shop_order_meta', 'confirm_email_woocommerce_order_action_execute', 50, 2);

function confirm_email_woocommerce_order_action_execute($post_id)
{


  if (filter_input(INPUT_POST, 'wc_order_action') !== 'send-confirmation-email') {

    return;
  }

  $order = wc_get_order($post_id);

  $user_email = $order->get_user()->user_email;

  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    'From: Imperial <impls@singnet.com.sg>'
  ];

  $subject = 'Thank you for your order. Your booking has been confirmed – Imperial Chauffeur Services Pte. Ltd';

  $message = "<p>Thank you for your order! We're excited to let you know that we have successfully received and confirmed your order.</p>";

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
  if ($order->has_status('on-hold')) {
    $order->update_status('confirmed');
  }
  $order->add_order_note(__('Sent confirmation email to customer', 'send-confirmation-email'));
}

add_action('woocommerce_process_shop_order_meta', 'completed_email_woocommerce_order_action_execute', 50, 2);


function completed_email_woocommerce_order_action_execute($post_id)
{
  if (filter_input(INPUT_POST, 'wc_order_action') !== 'send_completed_order') {
    return;
  }

  $order = wc_get_order($post_id);

  $order_id = $order->get_id();

  // Make sure the order status is completed (optional check)
  if ($order->get_status() !== 'completed') {
    $order->update_status('completed', __('Manually marked as completed for email trigger', 'your-textdomain'));
  }

  add_filter('woocommerce_email_enabled_customer_completed_order', '__return_true');

  // Load the WooCommerce email class
  $mailer = WC()->mailer();
  $mails = $mailer->get_emails();

  if (!empty($mails['WC_Email_Customer_Completed_Order'])) {
    $mails['WC_Email_Customer_Completed_Order']->trigger($order_id, $order);
  }
  $order->add_order_note(__('Sent completed email to customer', 'send-confirmation-email'));
}

add_filter('woocommerce_email_enabled_customer_completed_order', '__return_false');
