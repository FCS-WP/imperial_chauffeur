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

  if ($order->has_status('processing')) {
    $order->update_status('completed');
  }
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

  $message = "<p>Thank you for your order! We're excited to let you know that your order successfully received and confirmed.</p>";

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
