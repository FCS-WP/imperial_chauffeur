<?php

function disable_password_reset()
{
  return false;
}

add_filter('allow_password_reset', 'disable_password_reset');

add_filter('send_password_change_email', '__return_false');

add_action('woocommerce_order_list_table_restrict_manage_orders', 'show_is_first_order_checkbox', 20);
function show_is_first_order_checkbox()
{
  $selected = isset($_GET['metadata']) ? esc_attr($_GET['metadata']) : '';
  $options = array(
    '' => __('By order type', 'woocommerce'),
    '0' => __('Visitor Orders', 'woocommerce'),
    '1' => __('Member Orders', 'woocommerce')
  );

  echo '<select name="metadata" id="dropdown_shop_order_metadata">';
  foreach ($options as $value => $label_name) {
    printf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label_name);
  }
  echo '</select>';
}

add_filter('woocommerce_order_query_args', 'filter_woocommerce_orders_in_the_table');
function filter_woocommerce_orders_in_the_table($query_args)
{
  if (isset($_GET['metadata']) && is_numeric($_GET['metadata'])) {
    $meta_query[] = array(
      'key' => 'member_type',
      'value' => intval($_GET['metadata']),
      'compare' => 'AND'
    );
    $query_args['meta_query'] = $meta_query;
  }
  return $query_args;
}

function is_on_wc_orders_page_without_email()
{
  if (!is_admin())
    return false;
  if (isset($_REQUEST['wc_order_action']) && !empty($_REQUEST['wc_order_action']))
    return false;

  if (function_exists('get_current_screen')) {
    $screen = get_current_screen();
    return $screen && $screen->id === 'woocommerce_page_wc-orders';
  }

  return false;
}
// Change the Order ID in Order Woocommerece
add_filter('woocommerce_order_number', 'custom_order_number_display_type', 10, 2);

function custom_order_number_display_type($order_number, $order)
{
  // Skip for monthly payment orders
  $is_monthly_payment_order = $order->get_meta('is_monthly_payment_order', true);

  if ($is_monthly_payment_order) {
    return $order_number;
  }
  // Skip if not in admin or during email sending
  if (!is_on_wc_orders_page_without_email()) {
    return $order_number;
  }

  $is_member = $order->get_customer_id();

  if ($is_member) {
    return $order_number . '-Member';
  }

  return $order_number . '-Visitor';
}
// Change the Order ID in Order Woocommerece


add_action('wp_ajax_update_order_fee', 'handle_update_order_fee');
add_action('wp_ajax_nopriv_update_order_fee', 'handle_update_order_fee');

function handle_update_order_fee()
{
  check_ajax_referer('update_order_fee_nonce', 'nonce');

  $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
  $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';

  $allowed_paymentmethod = "zippy_antom_payment"; //zippy_antom_payment

  $cc_name = get_option("zippy_cc_fee_name");

  if ($order_id && $payment_method) {
    $order = wc_get_order($order_id);
    if ($order) {
      // Remove 5% CC Fee
      foreach ($order->get_items('fee') as $item_id => $item) {
        if ($item->get_name() == $cc_name) {
          $order->remove_item($item_id);
        }
      }
      $subtotal = $order->get_subtotal();
      $total_discount = $order->get_total_discount() ?? 0;
      $subtotal =  $subtotal - $total_discount;
      if (!empty($order->get_items("tax"))) {
        foreach ($order->get_items("tax") as $itm_id => $itm) {
          $gst_rate = $itm["rate_percent"];
          $gst = ($gst_rate * $subtotal) / 100;
        }
      }

      if ($payment_method == $allowed_paymentmethod && get_option("enable_cc_fee") == "yes") {
        // Remove old tax fee
        foreach ($order->get_items('tax') as $item_id => $item) {
          $order->remove_item($item_id);
        }

        $cc_fee_amount = get_option("zippy_cc_fee_amount");

        $cc_fee = ($cc_fee_amount * ($gst + $subtotal)) / 100;

        // Add 5% CC Fee
        $fee_item = new WC_Order_Item_Fee();
        $fee_item->set_name($cc_name);
        $fee_item->set_amount($cc_fee);
        $fee_item->set_total_tax(0);
        $fee_item->set_total($cc_fee);
        $fee_item->set_tax_class('');
        $order->add_item($fee_item);

        // Add tax
        $order_tax = new WC_Order_Item_Tax();

        $order_tax->set_name('9% GST');
        $order_tax->set_label('9% GST');
        $order_tax->set_rate_percent($gst_rate);
        $order_tax->set_tax_total($gst);
        $order->add_item($order_tax);
      }
      $order->save();

      $order->calculate_totals(false);

      $data = [
        "order" => $order,
      ];

      $body = render_email_template("order-detail-table", $data);

      wp_send_json_success(array(
        'body' => $body
      ));
    }
  }

  wp_send_json_error('Can not update order.');
}


add_action('wp_footer', 'add_order_pay_js');

function add_order_pay_js()
{

  if (is_wc_endpoint_url('order-pay')) {

    $order_id = absint(get_query_var('order-pay'));
    if ($order_id) {
      $nonce = wp_create_nonce('update_order_fee_nonce');
?>
      <script type="text/javascript">
        jQuery(document).ready(function($) {
          var order_id = <?php echo $order_id; ?>;
          var nonce = '<?php echo $nonce; ?>';
          $('body').on('change', 'form#order_review input[name="payment_method"]', function() {
            var payment_method = $(this).val();

            call_ajax(payment_method, order_id, nonce)
          })
          $(window).load(function() {
            if ($('form#order_review input[name="payment_method"][checked="checked"]').length == 1) {
              var payment_method = $('form#order_review input[name="payment_method"][checked="checked"]').val();

              call_ajax(payment_method, order_id, nonce)
            }
          });


          function call_ajax(payment_method, order_id, nonce) {
            $("#order_review").addClass("tp_loading");
            $.ajax({
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              type: 'POST',
              data: {
                action: 'update_order_fee',
                order_id: order_id,
                payment_method: payment_method,
                nonce: nonce
              },
              success: function(response) {
                if (response.success) {
                  let table = response.data.body;
                  $("#order_review .shop_table").html(table).show();
                  $("#order_review").removeClass("tp_loading");
                }
              }
            });
          }
        });
      </script>
<?php
    }
  }
}


function get_fee($order, $fee_type)
{
  $fee = [];
  if ($fee_type == "tax") {
    foreach ($order->get_items($fee_type) as $itm_id => $itm) {
      $fee[] = [
        "label" => $itm->get_label(),
        "gst_percent" => $itm["rate_percent"],
      ];
    }
  } elseif ($fee_type == "fee") {
    foreach ($order->get_items($fee_type) as $itm_id => $itm) {
      if ($order->get_items($fee_type) && $itm->get_name() != get_option("zippy_cc_fee_name")) {
        $fee[] = [
          "label" =>  $itm->get_name(),
          "fee" => $itm->get_total(),
        ];
      }
    }
  } elseif ($fee_type == "cc_fee") {
    foreach ($order->get_items("fee") as $itm_id => $itm) {
      if ($order->get_items("fee") && $itm->get_name() == get_option("zippy_cc_fee_name")) {
        $fee[] = [
          "label" =>  $itm->get_name(),
          "fee_amount" => get_option("zippy_cc_fee_amount"),
        ];
      }
    }
  }
  return $fee;
}



add_filter('gettext', 'change_pay_order_notice_text', 20, 3);
function change_pay_order_notice_text($new_text, $text, $domain)
{
  if (is_admin() || $domain !== 'woocommerce') {
    return $new_text;
  }

  if ($text === 'You are paying for a guest order. Please continue with payment only if you recognize this order.') {
    $new_text = 'You are paying for a Visitor order. If you have made this booking, please continue with payment.';
  }

  return $new_text;
}


function add_customer_v2_role()
{
  $customer_role = get_role('customer');

  if ($customer_role && ! get_role('customer_v2')) {
    add_role(
      'customer_v2',
      __('Customer V2', 'your-text-domain'),
      $customer_role->capabilities
    );
  }
}
add_action('init', 'add_customer_v2_role');
