<?php
//shortcode information_basic_booking_car on checkout page
function information_basic_booking_car(){
    $cart = WC()->cart;
    $total_quantity = $cart->get_cart_contents_count();
    $total_price = $cart->get_cart_total();

    foreach ($cart->get_cart() as $cart_item){
        $product_name = $cart_item['data']->get_name();
        
    ?>
    <div class="box-order-booking">
        <h4 class="box-header">Your Booking</h4>
        <div class="heading-booking">
            <p>Car: <span><?php echo $product_name;?></span></p>
            <p><span class="">Book: <?php echo $total_quantity; ?> Day(s) - Total Price: </span><span class="inner-h"> <?php echo $total_price; ?></span></p>
            <p><span class="">Type: </span><span class="inner-h"><?php echo $cart_item['time_booking']['servicetype']; ?></span></p>
            <p><span class="">Flight Details: </span><span class="inner-h"><?php echo $cart_item['time_booking']['flight_details']; ?></span></p>
        </div>
        <div class="date-time-form ">
            <div class="form-item">
                <label>Pick Up </label>
                <div class="wrap-date-time">
                    <div class="wrap-date">
                        <span class="value"><?php echo $cart_item['time_booking']['pickupdate']; ?></span>
                    </div>
                    <div class="wrap-time">
                        <span class="value"><?php echo $cart_item['time_booking']['pickuptime']; ?></span>
                    </div>
                </div>
            </div>
            <div class="line"></div>
            <div class="form-item">
                <label>Drop Off</label>
                <div class="wrap-date-time">
                    <div class="wrap-date">
                        <span class="value"><?php echo $cart_item['time_booking']['DropOffDate']; ?></span>
                    </div>
                    <div class="wrap-time">
                        <span class="value"><?php echo $cart_item['time_booking']['DropOffTime']; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Car delivery location</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['time_booking']['pickuplocation']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Car return location</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['time_booking']['dropofflocation']; ?></span>
            </div>
        </div>
    </div>
    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
    <?php
    }
    
}
add_shortcode('information_basic_booking_car', 'information_basic_booking_car');