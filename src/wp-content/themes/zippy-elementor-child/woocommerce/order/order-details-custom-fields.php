<?php
defined('ABSPATH') || exit;

$order_id = $order->get_id();
$is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);

if ($is_monthly_payment_order) {
  return;
}

$custom_fields = array(
  'service_type'       => __('Service Type', 'woocommerce'),
  'flight_details'     => __('Flight Details', 'woocommerce'),
  'eta_time'           => __('ETD/ETA Time', 'woocommerce'),
  'no_of_passengers'   => __('No Of Passengers', 'woocommerce'),
  'no_of_baggage'      => __('No Of Baggage', 'woocommerce'),
  'key_member'         => __('Key Member', 'woocommerce'),
  'pick_up_date'       => __('Pick Up Date', 'woocommerce'),
  'pick_up_time'       => __('Pick Up Time', 'woocommerce'),
  'pick_up_location'   => __('Pick Up Location', 'woocommerce'),
  'drop_off_location'  => __('Drop Off Location', 'woocommerce'),
);

$is_editing = isset($_GET['edit_order']) && $_GET['edit_order'] == $order_id;

$old_value_arr = [];
$new_value_arr = [];

if ($is_editing && $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('save_custom_fields_' . $order_id)) {
  $changes = [];

  foreach ($custom_fields as $key => $label) {
    if (isset($_POST[$key])) {
      $old_value = get_post_meta($order_id, $key, true);
      $old_value_arr[$key] = $old_value;

      $new_value = sanitize_text_field($_POST[$key]);
      $new_value_arr[$key] = $new_value;

      if ($old_value !== $new_value && $new_value !== '') {
        update_post_meta($order_id, $key, $new_value);
        $changes[] = "{$label}: \"{$old_value}\" → \"{$new_value}\"";
      }
    }
  }

  if (!empty($changes)) {
    $order = wc_get_order($order_id);
    $note_content = "Custom fields changed:\n" . implode("\n", $changes);
    $order->add_order_note($note_content, true);

    if ($order->get_status() !== 'on-hold') {
      $order->update_status('on-hold');
    }

    $current_user = wp_get_current_user();
    $member_name = $current_user->display_name ?: $current_user->user_login;
    $edit_date = current_time('d/m/Y');

    $user_email = get_option('admin_email');
    $subject = "Member {$member_name} Edited Order #{$order_id} – Action Required";

    $headers = [
      'Content-Type: text/html; charset=UTF-8',
      'From: Imperial <impls@singnet.com.sg>'
    ];

    $message = "<p>Hi Team,</p>";
    $message .= "<p><strong>Member {$member_name}</strong> made changes to <strong>Order #{$order_id}</strong>.</p>";
    $message .= "<h4>Details of Change:</h4>";

    foreach ($changes as $change) {
      if (preg_match('/^(.*?): \"(.*?)\" → \"(.*?)\"$/', $change, $matches)) {
        $label = $matches[1];
        $old = $matches[2];
        $new = $matches[3];

        $message .= "<p><strong>Custom Field Edited:</strong> {$label}<br>";
        $message .= "<strong>Previous Value:</strong> {$old}<br>";
        $message .= "<strong>New Value:</strong> {$new}</p><hr>";
      }
    }

    $message .= "<strong>Edit Date:</strong> {$edit_date}</p>";
    $message .= "<p>Please review the changes in the backend.</p>";
    $message .= get_email_signature();

    wp_mail($user_email, $subject, $message, $headers);

    send_notify_email($order, $old_value_arr, $new_value_arr);
  }

  $redirect_url = remove_query_arg('edit_order', wp_unslash($_SERVER['REQUEST_URI']));
  echo '<script>window.location.href = "' . esc_url($redirect_url) . '";</script>';
  exit;
}

echo '<div class="woocommerce-order-custom-fields">';

if ($is_editing && can_edit_order($order_id)) {
  echo '<form method="post">';
  wp_nonce_field('save_custom_fields_' . $order_id);
  echo '<div class="field-columns">';

  $service_type_options = array(
    'Airport Arrival Transfer',
    'Airport Departure Transfer',
    'Point-to-point Transfer'
  );

  foreach ($custom_fields as $key => $label) {
    $value = get_post_meta($order_id, $key, true);
    echo '<p><label><strong>' . esc_html($label) . ':</strong><br />';
    $type = 'text';

    switch ($key) {
      case 'pick_up_date':
        $type = 'date';
        break;
      case 'no_of_passengers':
      case 'no_of_baggage':
        $type = 'number';
        break;
      case 'service_type':
        $type = 'options';
        break;
    }

    if ($type === 'options') {
      echo '<select style="width: 400px;margin-top:10px;" name="service_type">';
      foreach ($service_type_options as $option) {
        echo '<option value="' . esc_attr($option) . '" ' . selected($option, $value, false) . '>' . esc_html($option) . '</option>';
      }
      echo '</select>';
    } else {
      if ($key === 'pick_up_date' && !empty($value)) {
        $value = date('Y-m-d', strtotime($value));
      }
      echo '<input id="' . esc_attr($key) . '" type="' . esc_attr($type) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width:100%;" />';
    }

    echo '</label></p>';
  }

  echo '</div><p><button type="submit" class="button button-black">Save</button></p></form>';
} else {
  echo '<div class="field-columns">';
  foreach ($custom_fields as $key => $label) {
    $value = get_post_meta($order_id, $key, true);
    if (!empty($value)) {
      echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
    }
  }

  if (isset($service_type) && $service_type == "Hourly/Disposal") {
    $order_quantity = array_sum(wp_list_pluck($order->get_items(), 'qty'));
    echo "<p><strong>Duration:</strong> {$order_quantity} Hours</p>";
  }
  echo '</div>';

  if (!is_wc_endpoint_url('order-received') && !in_array($order->get_status(), ['completed', 'cancelled'])) {
    if (can_edit_order($order_id)) {
      echo '<div style="text-align:right;margin:30px 0px 20px 0px;"><a class="button button-black red" href="' . esc_url(add_query_arg('edit_order', $order_id)) . '">Edit</a></div>';
    } else {
      echo '<p style="font-style: italic;">This order is scheduled in less than 24 hours and can no longer be edited or changed.<br>For any enquiries, please contact us directly. Thank you for your understanding!</p>';
    }
  }
}

echo '</div>';
