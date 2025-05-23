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
  $options  = array(
    ''              => __('By order type', 'woocommerce'),
    '0'  => __('Visitor Orders', 'woocommerce'),
    '1'  => __('Member Orders', 'woocommerce')
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
  if (!is_admin()) return false;
  if (isset($_REQUEST['wc_order_action']) && !empty($_REQUEST['wc_order_action'])) return false;

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

function handle_update_order_fee() {
    check_ajax_referer('update_order_fee_nonce', 'nonce');

    $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';

    $allowed_paymentmethod = "cod"; //zippy_antom_payment

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

            wp_send_json_success(array(
                'total' => wc_price($order->get_total()),
                'fee' => ($payment_method === $allowed_paymentmethod) ? wc_price($fee) : 0,
            ));
        }
    }

    wp_send_json_error('Can not update order.');
}


add_action('wp_footer', 'add_order_pay_js');

function add_order_pay_js() {
    
    if (is_wc_endpoint_url('order-pay')) {

        $order_id = absint(get_query_var('order-pay'));
        if ($order_id) {
            $nonce = wp_create_nonce('update_order_fee_nonce');
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    
                    let params = new URLSearchParams(window.location.search),
                        current_method = params.get('current_method');
                       
                      $('form#order_review input[name="payment_method"]').each(function(index,item){
                        if($(item).val() == current_method){
                          $(item).attr("checked", "checked")
                        } else {
                          $(item).removeAttr("checked")
                        }
                      })

                    $('body').on('change', 'form#order_review input[name="payment_method"]', function() {
                        var payment_method = $(this).val();
                        var order_id = <?php echo $order_id; ?>;
                        var nonce = '<?php echo $nonce; ?>';
                        $("#payment").addClass("tp_loading");
                        
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
                                  params.set('current_method', payment_method);
                                  window.location.search = params;
                                }
                            }
                        });
                    });
                });
            </script>
            <?php
        }
    }
}