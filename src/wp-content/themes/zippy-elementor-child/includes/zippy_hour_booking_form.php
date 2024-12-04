<?php

function hour_booking_form(){
    global $product;
    if (is_product()){
    $today = date('d-m-Y');
    $key_member = 0;
    if(is_user_logged_in()){
        $key_member = 1;
    }
    ?>
    <div id="popup" class="popup">
        <div class="popup-content">
            <div class="calendar-box-custom">
                <div class="calendar-box"><div id="tab_hour_picker"></div></div>
                <button class="close-btn" id="closePopup">Continute Booking</button>
            </div>
        </div>
    </div>
    <form method="POST">
        <div class="box-pickup-information">
            <div class="input-text-pickup-information">
                <div class="row-form-custom">
                    <input name="id_product" type="hidden" value="<?php echo $product->get_id();?>">
                    <input name="key_member" type="hidden" value="<?php echo $key_member;?>">
                    <input name="hbk_type" type="hidden" value="hour">
                    <input name="hbk_service_fees" id="hbk_service_fees" type="hidden" value="20">
                </div>
                <!-- Get product categories & check min hour -->
                <?php 
                    $category_ids = $product->get_category_ids();
                    $isMin3h = true;
                    if (empty($category_ids)) {
                        return;
                    }
                    foreach ($category_ids as $category_id) {
                        $category = get_term($category_id, 'product_cat');
                        if ($category->slug === 'min-4-hours') {
                            $isMin3h = false;
                        }
                    }
                ?>
                <div class="row-form-custom col-2">
                            <div class="col-form-custom">
                                <label for="hbk_pickup_date">Pick Up Date & Time</label>
                                <div class="d-flex" id="openPopup">
                                    <input type="text" id="hbk_pickup_date" name="hbk_pickup_date" placeholder="Select date" readonly required/>
                                    <input type="text" id="hbk_pickup_time" name="hbk_pickup_time"readonly required/>
                                </div>
                            </div>
                            <div class="col-form-custom">
                                <label for="hbk_pickup_type">Pick Up type</label>
                                <select class="" id="hbk_pickup_type" name="hbk_pickup_type">
                                    <option value="domestic" data-price="20" selected>Domestic (+ $20)</option>
                                    <option value="international" data-price="50">International (+ $50)</option>
                                </select>
                            </div>
                        </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="hbk_time_value">Time</label>
                        <select class="" id="hbk_time_value" name="hbk_time_value">
                            <option value="" selected>Please choose an option</option>
                            <?php 
                                if ($isMin3h) {
                                    echo ('<option value="3-hours">3 hours</option>');
                                }
                            ?>
                            <option value="4" >4 hours</option>
                            <option value="5">5 hours</option>
                            <option value="6">6 hours</option>
                            <option value="7">7 hours</option>
                            <option value="8">8 hours</option>
                            <option value="9">9 hours</option>
                            <option value="10">10 hours</option>
                            <option value="11">11 hours</option>
                            <option value="12">12 hours</option>
                        </select>
                    </div>
                    <div class="col-form-custom">
                        <label for="hbk_flight_details">Flight Details<span style="color:red;">*</span></label>
                        <input size="40" maxlength="400" class="" id="hbk_flight_details" aria-required="true" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="hbk_flight_details">
                    </div>
                </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="hbk_pickup_location">Pick Up Location</label>
                        <input size="40" maxlength="60" class="" id="hbk_pickup_location" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="hbk_pickup_location">
                    </div>
                    <div class="col-form-custom">
                        <label for="hbk_dropoff_location">Drop Off Location</label>
                        <input size="40" maxlength="50" class="" id="hbk_dropoff_location" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="hbk_dropoff_location">
                    </div>
                </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="hbk_number_of_passengers">Number of Passengers</label>
                        <input size="40" maxlength="60" class="" id="hbk_number_of_passengers" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="hbk_number_of_passengers">
                    </div>
                    <div class="col-form-custom">
                        <label for="hbk_number_of_baggages">Number of Baggages</label>
                        <input size="40" maxlength="50" class="" id="hbk_number_of_baggages" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="hbk_number_of_baggages">
                    </div>
                </div>
                
                <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                        <label for="hbk_special_requests">Special Requests</label>
                        <input size="40" maxlength="400" class="" id="hbk_special_requests" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="hbk_special_requests">
                    </div>
                </div>
            </div>
            <div class="col-total-price-information">
                <!-- <label>Total Price: </label><span > $<span id="price-total"><?php echo $current_price = $product->get_price();?></span><span id="default-price" style="display:none"><?php echo $current_price = $product->get_price();?></span></span> -->
                <label>Total Price: </label>
                <span > $
                    <span id="hbk_total_price" data-product-price="<?php echo $current_price = $product->get_price();?>">
                        <?php echo ($current_price = $product->get_price() + 20);?>
                    </span>
                </span>
            </div>
            <div class="row-form-custom col-1">
                <div class="col-form-custom">
                    <input class="" id="btnReserve" name="submit_hour_booking_form" type="submit" value="Payment Booking">
                </div>
            </div>
        </div>
    </form>
    <?php
    }
}
add_shortcode('hour_booking_form', 'hour_booking_form');

// function process submit pick up information
function process_hbk_form(){
    $status_redirect = false;
    if (isset($_POST['submit_hour_booking_form'])) {
        $id_product = sanitize_text_field($_POST['id_product']);
        $pickup_date = sanitize_text_field($_POST['hbk_pickup_date']);
        $pickup_time = sanitize_text_field($_POST['hbk_pickup_time']);
        $time_value = sanitize_text_field($_POST['hbk_time_value']);

        if (!empty($pickup_date)) {
            try {
                $pickup_date = new DateTime($pickup_date);
        
            } catch (Exception $e) {
                // Handle any potential errors
                error_log('Date calculation error: ' . $e->getMessage());
            }
        }
        
        $cart = WC()->cart;
        $cart->empty_cart();
        $cart->add_to_cart($id_product, $time_value);
        $status_redirect = true;
    }
    if($status_redirect == true){
        wp_redirect(wc_get_checkout_url());
        exit;
    }
}
add_action('init', 'process_hbk_form');

//function add pick up information to cart
add_filter('woocommerce_add_cart_item_data', 'hbk_add_custom_cart_item_data_time');
function hbk_add_custom_cart_item_data_time($cart_item_data)
{
    if (isset($_POST['submit_hour_booking_form'])) {
        $key_member = sanitize_text_field($_POST['key_member']);
        $pickup_date = sanitize_text_field($_POST['hbk_pickup_date']);
        $pickup_time = sanitize_text_field($_POST['hbk_pickup_time']);
        $pickup_type = sanitize_text_field($_POST['hbk_pickup_type']);
        $time_val = sanitize_text_field($_POST['hbk_time_value']);
        $flight_details = sanitize_text_field($_POST['hbk_flight_details']);
        $pickup_location = sanitize_text_field($_POST['hbk_pickup_location']);
        $dropoff_location = sanitize_text_field($_POST['hbk_dropoff_location']);
        $number_of_passengers = sanitize_text_field($_POST['hbk_number_of_passengers']);
        $number_of_baggages = sanitize_text_field($_POST['hbk_number_of_baggages']);
        $special_requests = !empty($_POST['hbk_special_requests']) ? sanitize_text_field($_POST['hbk_special_requests']) : '';
        $service_type = sanitize_text_field($_POST['hbk_type']);
        $service_fee = sanitize_text_field($_POST['hbk_service_fees']);

        $cart_item_data['time_booking'] = array(
            'key_member' => $key_member,
            'pickup_date' => $pickup_date,
            'pickup_time' => $pickup_time,
            'pickup_type' => $pickup_type,
            'time_val' => $time_val,
            'flight_details' => $flight_details,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'number_of_passengers' => $number_of_passengers,
            'number_of_baggages' => $number_of_baggages,
            'special_requests' => $special_requests,
            'service_type' => $service_type,
            'service_fee' => $service_fee,
        );
    }
    return $cart_item_data;
}

