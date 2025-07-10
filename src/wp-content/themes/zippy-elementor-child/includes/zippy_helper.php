<?php

use Automattic\WooCommerce\StoreApi\Routes\V1\CheckoutOrder;

function slugify($string)
{
  // Convert the string to lowercase
  $string = strtolower($string);

  // Replace spaces and special characters with dashes
  $string = preg_replace('/[^a-z0-9]+/', '_', $string);

  // Remove leading and trailing dashes
  $string = trim($string, '_');

  return $string;
}

function pr($data)
{
  echo '<style>
  #debug_wrapper {
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 999;
    background: #fff;
    color: #000;
    overflow: auto;
    width: 100%;
    height: 100%;
  }</style>';
  echo '<div id="debug_wrapper"><pre>';

  print_r($data); // or var_dump($data);
  echo "</pre></div>";
  die;
}


// Check order enable to edit 

function can_edit_order($order_id)
{
  $booking_date = get_post_meta($order_id, 'pick_up_date', true);
  $booking_time = get_post_meta($order_id, 'pick_up_time', true);

  $original = trim($booking_date . ' ' . $booking_time);
  try {
    $booking_datetime = new DateTime($original, wp_timezone());
    $current_time = new DateTime('now', wp_timezone());

    $threshold = clone $current_time;
    $threshold->modify('+24 hours');
    if ($booking_datetime > $threshold) return true;
    return false;
  } catch (\Throwable $th) {
    return false;
  }
}
