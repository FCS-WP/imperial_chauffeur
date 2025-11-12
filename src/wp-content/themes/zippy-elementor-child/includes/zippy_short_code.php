<?php
add_shortcode('descrition_product', 'render_descrition_product_shortcode');
function render_descrition_product_shortcode()
{
    global $product;

    if (!$product) {
        return '';
    }

    $product_id = $product->get_id();
    $is_role_customer_v2 = false;
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if ($user->ID) {
            if (in_array('customer_v2', (array) $user->roles)) {
                $is_role_customer_v2 = true;
            }
        }
    }

    $discounted_price = get_product_pricing_rules($product, 1);
    $regular_price = !empty($discounted_price) ? $discounted_price : $product->get_price();

    $price_per_hour_for_v2 = get_config_price_for_customer_v2();
    if ($is_role_customer_v2 && array_key_exists($product_id, $price_per_hour_for_v2)) {
        $price_per_hour = $price_per_hour_for_v2[$product_id];
    } else {
        $price_per_hour = get_post_meta($product_id, '_price_per_hour', true);
        $price_per_hour = (!empty($price_per_hour) && is_numeric($price_per_hour)) ? (float) $price_per_hour : $regular_price;
    }

    return '<table class="description-product-table">
            <tr>
              <td><strong>Transfer</strong></td>
              <td>SGD ' . $regular_price . ' / Trip</td>
            </tr>
            <tr>
              <td><strong>Disposal (Min. 3 Hours)</strong></td>
              <td>SGD ' . $price_per_hour . ' / Hour</td>
            </tr>
          </table>';
}
