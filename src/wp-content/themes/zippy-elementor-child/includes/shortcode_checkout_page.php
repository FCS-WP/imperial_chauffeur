<?php
//shortcode information_basic_booking_car on checkout page
function information_basic_booking_car(){
    $cart = WC()->cart;
    $total_quantity = $cart->get_cart_contents_count();
    
    $total_price = $cart->get_total();

    foreach ($cart->get_cart() as $cart_item){
        $product_name = $cart_item['data']->get_name();  
         
    ?>
    <div class="box-order-booking">
        <h4 class="box-header">Your Booking</h4>
        <div class="row-checkout-form">
            <div class="dropdown-form">
                <label>Car Booking</label>
                <div class="wrap-form">
                    <span class="value"><?php echo $product_name;?></span>
                </div>
            </div>
            <div class="dropdown-form">
                <label>Type</label>
                <div class="wrap-form">
                    <span class="value"><?php echo $cart_item['booking_information']['service_type']; ?></span>
                </div>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Time Use</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['booking_hour']['time_use']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>No. of Passengers</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['booking_information']['no_of_passengers']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>No. of Baggage</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['booking_information']['no_of_baggage']; ?></span>
            </div>
        </div>
        <div class="date-time-form ">
            <div class="form-item">
                <label>Pick Up Information</label>
                <div class="wrap-date-time">
                    <div class="wrap-date">
                        <span class="value">Date: <?php echo $cart_item['booking_information']['pick_up_date']; ?></span>
                    </div>
                    <div class="wrap-time">
                        <span class="value">Time: <?php echo $cart_item['booking_information']['pick_up_time']; ?></span>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php
        if($cart_item['booking_trip']['flight_details'] != NULL){
            ?>
            <div class="dropdown-form">
                <label>Flight Details</label>
                <div class="wrap-form">
                    <span class="value"><?php echo $cart_item['booking_trip']['flight_details'];?></span>
                </div>
            </div>
            <div class="dropdown-form">
                <label>ETE/ETA Time</label>
                <div class="wrap-form">
                    <span class="value"><?php echo $cart_item['booking_trip']['eta_time'];?></span>
                </div>
            </div>

            <?php
        }
        ?>
        <div class="dropdown-form">
            <label>Additional Stop</label>
            <div class="wrap-form">
                <span class="value"><?php 
                if( $cart_item['booking_information']['additional_stop'] == 1){
                    echo "Outside Singapore";
                }else{
                    echo "Inside Singapore";
                }
                ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Pick Up Location</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['booking_information']['pick_up_location']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Drop Off Location</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['booking_information']['drop_off_location']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Special Reuest</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['booking_information']['special_requests']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Total Price</label>
            <div class="wrap-form">
                <span class="value"><?php echo $total_price; ?></span>
            </div>
        </div>
    </div>
    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
    <?php
    }
    
}
add_shortcode('information_basic_booking_car', 'information_basic_booking_car');

