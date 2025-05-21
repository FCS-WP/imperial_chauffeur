<?php

function render_email_template($template_name, $data = array()) {
  ob_start();

  $template_path = get_template_directory() . '-child' . '/site-structure/blocks/mail/' . $template_name . '.php';

  if (file_exists($template_path)) {
    extract($data);
    include $template_path;
  }

  return ob_get_clean();
}



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
    'From: Imperial Chauffeur Services <impls@singnet.com.sg>'
  ];

  $subject = 'Thank you for your order. Your booking has been confirmed – Imperial Chauffeur Services Pte. Ltd';

  $message = "<p style='font-size:13px;color:#000'>Thank you for your order! We're excited to let you know that we have successfully received and confirmed your order.</p>";
  $message .= "<br><p style='font-size:13px;color:#000'>Our team will review your request and respond within 24 hours. If you have any urgent concerns, feel free to contact us.</p>";
  $message .= "<p style='font-size:13px;color:#000'>We appreciate your patience and look forward to assisting you.</p><br>";

  echo get_email_signature();

  wp_mail($user_email, $subject, $message, $headers);

  if ($order->has_status('on-hold')) {
    $order->update_status('confirmed');
  }
  $order->add_order_note(__('Sent confirmation email to customer', 'send-confirmation-email'));
}

add_action('woocommerce_process_shop_order_meta', 'completed_email_woocommerce_order_action_execute', 50, 2);
add_filter('woocommerce_email_enabled_customer_completed_order', '__return_false');


function completed_email_woocommerce_order_action_execute($order_id)
{
  if (filter_input(INPUT_POST, 'wc_order_action') !== 'send_completed_order') {
    return;
  }

  $order = wc_get_order($order_id);

  $user = $order->get_user();

  $user_email = $order->get_billing_email();

  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    'From: Imperial <impls@singnet.com.sg>'
  ];

  $subject = "Thank you for your order. Your payment has been received  – Imperial Chauffeur Services Pte. Ltd";

  $data = [
    "user" => $user,
    "order" => $order,
  ];

  $body = render_email_template('complete-email', $data);

  $mail = wp_mail($user_email, $subject, $body, $headers);

  if($mail){
    $order->add_order_note(__('Sent completed email to customer', 'send-confirmation-email'));
  }
}




function send_notify_email($order, $old_data, $new_data){

  $user_email = $order->get_billing_email();

  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    'From: Imperial <impls@singnet.com.sg>'
  ];

  $subject = "Your order information has been updated";

  $data = [
    "old_data" => $old_data,
    "new_data" => $new_data,
    "order" => $order,
  ];

  $body = render_email_template("order-edit-email", $data);

  return wp_mail($user_email, $subject, $body, $headers);

}


function get_email_signature(){
  return "
    <p style='font-size:13px;color:#000;margin-top:50px;'>Kind regards,</p>
    <h3 style='font-size:15px;color:#000'>Imperial Chauffeur Services Pte Ltd</h3>
    <p style='font-size:13px;color:#000'>Office telephone: <a href='tel:+6567340428'>+65 67340428</a></p>
    <p style='font-size:13px;color:#000'>Email: impls@singnet.com.sg</p>
    <p style='font-size:13px;color:#000'>Website: imperialchauffeur.sg </p>";
}