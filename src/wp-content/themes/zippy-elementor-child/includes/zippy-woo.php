<?php

function disable_password_reset()
{
  return false;
}

add_filter('allow_password_reset', 'disable_password_reset');

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
      if ($payment_method == $allowed_paymentmethod && get_option("enable_cc_fee") == "yes") {
        $fee_base = $order->get_subtotal();
        $fee = $fee_base * (get_option("zippy_cc_fee_amount") / 100);

        // Add 5% CC Fee
        $fee_item = new WC_Order_Item_Fee();
        $fee_item->set_name($cc_name);
        $fee_item->set_amount($fee);
        $fee_item->set_total($fee);
        $fee_item->set_tax_class('');
        $order->add_item($fee_item);
      }
      
      $order->calculate_totals();

      // Subtotal
      $custom_subtotal = 0;
      $order_items = [];
      $i = 0;
      foreach ($order->get_items() as $item_id => $item) {
        $pne_total = $item->get_total();
        $custom_subtotal += $pne_total;
        $order_items[$i]["item_name"] = $item->get_name();
        $order_items[$i]["qty"] = $item->get_quantity();
        $order_items[$i]["price"] = $item->get_total();
        $i++;
      }
      // Additional Fee
      $additional_fee = get_fee($order, "fee");

      // Tax
      $gst = get_fee($order, "tax");

      // CC Fee
      $cc_fee = get_fee($order, "cc_fee");

      wp_send_json_success(array(
        'items' => $order_items,
        'subtotal' => $custom_subtotal,
        'additional_fee' => $additional_fee,
        'gst' => $gst,
        'cc_fee' => $cc_fee,
        'total' => wc_price($order->get_total()),
        'payment_method' => $order->get_payment_method_title(),
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
        jQuery(document).ready(function ($) {

          $('body').on('change', 'form#order_review input[name="payment_method"]', function () {
              var payment_method = $(this).val();
              var order_id = <?php echo $order_id; ?>;
              var nonce = '<?php echo $nonce; ?>';

              call_ajax(payment_method, order_id, nonce)
            })
          $(window).load(function () {
            if ($('form#order_review input[name="payment_method"][checked="checked"]').length == 1) {
              var payment_method = $('form#order_review input[name="payment_method"][checked="checked"]').val();
              var order_id = <?php echo $order_id; ?>;
              var nonce = '<?php echo $nonce; ?>';
              
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
              success: function (response) {
                
                if (response.success) {
                  let data = response.data,
                      items = data.items,
                      subtotal = +data.subtotal,
                      additional_fee = data.additional_fee,
                      gst = data.gst,
                      total = data.total,
                      cc_fee = data.cc_fee,
                      payment_method = data.payment_method,
                      item_html = ``,
                      gst_html = ``,
                      additional_fee_html = ``,
                      cc_fee_html = ``,
                      total_addition_fee = 0;
                      total_gst = 0;
                  
                  if($(items).empty().length > 0){
                    $(items).each(function(index, item){
                      item_html += `
                        <tr class="order_item">
                            <td class="product-name">${item.item_name}</td>
                            <td class="product-quantity"> <strong class="product-quantity">×&nbsp;${item.qty}</strong></td>
                            <td class="product-subtotal"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>${item.price}</bdi></span></td>
                        </tr>
                      `
                    })
                  }

                  if($(additional_fee).empty().length > 0){
                    $(additional_fee).each(function(index, item){
                      additional_fee_html += `
                        <tr>
                          <td scope="row" colspan="2">${item.label}:</td>
                          <td class="product-total"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>${item.total}</bdi></span></td>
                        </tr>
                      `;
                      total_addition_fee += +item.total;
                      subtotal = subtotal + total_addition_fee;
                    })
                  }

                  if($(gst).empty().length > 0){
                    $(gst).each(function(index, item){
                      gst_html += `
                        <tr>
                          <th scope="row" colspan="2">${item.label}:</th>
                          <td class="product-total"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>${((+item.gst_percent * subtotal) / 100).toFixed(2)}</bdi></span></td>
                        </tr>
                      `;
                      total_gst += ((+item.gst_percent * subtotal) / 100).toFixed(2);
                    })
                  }

                  if($(cc_fee).empty().length > 0){
                    cc_fee_html += `
                        <tr>
                          <th scope="row" colspan="2">${cc_fee[0].label}:</th>
                          <td class="product-total"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>${((cc_fee[0].fee_amount * (+total_gst + subtotal)) / 100).toFixed(2)}</bdi></span></td>
                        </tr>
                      `
                  }
                  let table = `
                      <thead>
                        <tr>
                          <th class="product-name">Product</th>
                          <th class="product-quantity">Qty</th>
                          <th class="product-total">Totals</th>
                        </tr>
                      </thead>
                      <tbody>
                          ${item_html}
                          ${additional_fee_html}
                      </tbody>
                      <tfoot>
                        <tr>
                            <th scope="row" colspan="2">Subtotal:</th>
                            <td class="product-total"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>${+subtotal}</bdi></span></td>
                        </tr>
                        ${gst_html}
                        ${cc_fee_html}
                        <tr>
                          <th scope="row" colspan="2">Grand Total:</th>
                          <td class="product-total">${total}</td>
                        </tr>
                      </tfoot>`;
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
  if($fee_type == "tax"){
    foreach ($order->get_items($fee_type) as $itm_id => $itm) {
      $fee[] = [
        "label" => $itm->get_label(),
        "gst_percent" => $itm["rate_percent"],
      ];
    }
  } elseif($fee_type == "fee"){
    foreach ($order->get_items($fee_type) as $itm_id => $itm) {
      if($order->get_items($fee_type) && $itm->get_name() != get_option("zippy_cc_fee_name")){
        $fee[] = [
          "label" =>  $itm->get_name(),
          "fee" => $itm->get_total(),
        ];
      }
    }
  } elseif($fee_type == "cc_fee"){
    foreach ($order->get_items("fee") as $itm_id => $itm) {
      if($order->get_items("fee") && $itm->get_name() == get_option("zippy_cc_fee_name")){
        $fee[] = [
          "label" =>  $itm->get_name(),
          "fee_amount" => get_option("zippy_cc_fee_amount"),
        ];
      }
    }
  }
  return $fee;
}