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

function get_config_price_for_customer_v2()
{
    return [
        '48' => 180,
        '46' => 130,
        '44' => 55,
        '52' => 55,
        '50' => 55,
        '54' => 55,
        '38' => 75,
        '40' => 90,
    ];
}
