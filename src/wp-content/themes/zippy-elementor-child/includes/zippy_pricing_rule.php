<?php

use WDP_Functions;

function get_product_pricing_rules($product, $quantity)
{
    if (!class_exists(WDP_Functions::class)) {
        return null;
    }

    $adp = new WDP_Functions();
    $product_price = $adp->get_discounted_product_price($product, $quantity, true);
    return $product_price;
}
