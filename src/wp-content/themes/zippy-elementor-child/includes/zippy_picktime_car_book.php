<?php
//shortcode vanilla_booking_car_custom create form booking on single product page
function trip_booking_form(){
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
                <div class="calendar-box"><div id="calendar"></div></div>
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
                    <input name="midnight_fee" id="trip_midnight_fee" type="hidden" value="0">
                    <input name="time_use" id="time_use" type="hidden" value="1">
                </div>
                <div class="row-form-custom col-2">
                            <div class="col-form-custom position-relative" id="openPopup">
                                <div class="d-flex flex-wrap mb-1">
                                    <label for="hbk_pickup_date">Pick Up Date & Time <span style="color:red;">*</span></label>
                                    <span class="note-midnight-fee note-trip-midnight" style="display: none;">(Midnight fee has been applied.)</span>
                                </div>
                                <div class="d-flex">
                                <input class="pickupdate" id="pickupdate" value="<?php echo $today;?>" type="text" name="pick_up_date" required>
                                <input type="text" id="pickuptime" name="pick_up_time" min="00:00" max="24:00" value="<?php echo date("H:i"); ?>" required>
                                </div>
                            </div>
                            <div class="col-form-custom">
                                <label for="hbk_pickup_fee">Pick Up type <span style="color:red;">*</span></label>
                                <select class="" id="additional_stop" name="additional_stop">
                                    <option id="inside_additional_stop" value="0" data-price="0" selected>Inside Singapore</option>
                                    <option id="outside_additional_stop" value="1" data-price="25">Outside Singapore</option>
                                </select>
                            </div>
                        </div>
                
                <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                        <label for="servicetype">Type Services</label>
                        <select class="" id="servicetype" name="service_type" required>
                            <option value="">Please choose an option</option>
                            <option value="Airport Arrival Transfer">Airport Arrival Transfer</option>
                            <option value="Airport Departure Transfer">Airport Departure Transfer</option>
                            <option value="Point-to-point Transfer">Point-to-point Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="pickuplocation">Pick Up</label>
                        <input size="40" maxlength="60" class="" id="pickuplocation" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="pick_up_location" required>
                    </div>
                    <div class="col-form-custom">
                        <label for="doaddress">Drop Off</label>
                        <input size="40" maxlength="50" class="" id="dolocation" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="drop_off_location" required>
                    </div>
                </div>
                <div class="row-form-custom col-2" id="input-flight">
                    <div class="col-form-custom">
                        <label for="flight">Flight Details<span style="color:red;">*</span></label>
                        <input size="40" maxlength="400" class="" id="flight" aria-required="true" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="flight_details">
                    </div>
                    <div class="col-form-custom">
                        <label for="eta_time">ETE/ETA</label>
                        <input type="text" name="eta_time" id="eta_time" placeholder="Enter time">
                    </div>
                </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="noofpassengers">No. of Passengers</label>
                        <input class="" id="noofpassengers" aria-required="true" aria-invalid="false" placeholder="Enter No. of Passengers" value="" type="text" name="no_of_passengers" required>
                    </div>
                    <div class="col-form-custom">
                        <label for="noofbaggage">No. of Baggage</label>
                        <input class="" id="noofbaggage" aria-required="true" aria-invalid="false" placeholder="Enter No. of Baggage" value="" type="text" name="no_of_baggage" required>
                    </div>
                </div>
                <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                    <label for="special_requests">Special Requests</label>
                    <input size="40" maxlength="400" class="" id="hbk_special_requests" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="special_requests">
                    </div>
                    
                </div>
                
            </div>
            <div class="confirm-terms">
                <input class="terms-checkbox" type="checkbox" name="agree_terms" value="1" id="agree_terms" required>
                <label for="agree_terms">
                    <ul class="list-terms">
                        <li class="show-title">I submit this form to request for the services listed above. I understand that my booking will only be confirmed after I have received an email confirmation.</li>
                        <li class="show-title">I have read and understood the terms and conditions</li>
                    </ul>
                </label>
            </div>
            <div class="col-total-price-information">
                <label>Total Price: </label><span > $<span id="price-total"><?php echo $current_price = $product->get_price();?></span><span id="default-price" style="display:none"><?php echo $current_price = $product->get_price();?></span></span>
            </div>
            <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                        <input class="" id="btnReserve" name="submit_car_booking_time" type="submit" value="Payment Booking">
                    </div>
                </div>
        </div>
    </form>
    <?php
    }
}
add_shortcode('trip_booking_form', 'trip_booking_form');

//function process submit pick up information
function process_booking_time(){
    $status_redirect = false;
    if (isset($_POST['submit_car_booking_time']) || isset($_POST['submit_hour_booking_form'])) {
        $time_use = 1;
        $id_product = sanitize_text_field($_POST['id_product']);
        $time_use = sanitize_text_field($_POST['time_use']);
        $service_type = sanitize_text_field($_POST['service_type']);
        
        $cart = WC()->cart;

        $cart->empty_cart();
        $cart->add_to_cart($id_product, $time_use);
        
        
        $status_redirect = true;
        
    }
    
    if($status_redirect == true){
        wp_redirect(wc_get_checkout_url());
        exit;
    }
}
add_action('init', 'process_booking_time');

//function add pick up information to cart
add_filter('woocommerce_add_cart_item_data', 'add_custom_cart_item_data_time');
function add_custom_cart_item_data_time($cart_item_data)
{

    if (!isset($_POST['submit_car_booking_time']) && !isset($_POST['submit_hour_booking_form'])) return;

    
        $key_member = sanitize_text_field($_POST['key_member']);
        $pick_up_date = sanitize_text_field($_POST['pick_up_date']);
        $pick_up_time = sanitize_text_field($_POST['pick_up_time']);
        $pick_up_location = sanitize_text_field($_POST['pick_up_location']);
        $drop_off_location = sanitize_text_field($_POST['drop_off_location']);
        $no_of_passengers = sanitize_text_field($_POST['no_of_passengers']);
        $no_of_baggage = sanitize_text_field($_POST['no_of_baggage']);
        $additional_stop = sanitize_text_field($_POST['additional_stop']);
        $midnight_fee = sanitize_text_field($_POST['midnight_fee']);
        $agree_terms = sanitize_text_field($_POST['agree_terms']);
        $service_type = sanitize_text_field($_POST['service_type']);
        $special_requests = sanitize_text_field($_POST['special_requests']);


        $cart_item_data['booking_information'] = array(
            'key_member' => $key_member,
            'pick_up_date' => $pick_up_date,
            'pick_up_time' => $pick_up_time,
            'pick_up_location' => $pick_up_location,
            'drop_off_location' => $drop_off_location,
            'no_of_passengers' => $no_of_passengers,
            'no_of_baggage' => $no_of_baggage,
            'additional_stop' => $additional_stop,
            'midnight_fee' => $midnight_fee,
            'agree_terms' => $agree_terms,
            'service_type' => $service_type,
            'special_requests' => $special_requests,
            
        );

        if(isset($_POST['submit_car_booking_time'])){
            $cart_item_data['booking_trip'] = array(
                'flight_details' => sanitize_text_field($_POST['flight_details']),
                'eta_time' => sanitize_text_field($_POST['eta_time']),
            );

        }

        if(isset($_POST['submit_hour_booking_form'])){
            $cart_item_data['booking_hour'] = array(
                'time_use' => sanitize_text_field($_POST['time_use']),
            );
        }

    return $cart_item_data;
}

add_action('woocommerce_before_calculate_totals', 'custom_set_cart_item_price', 10, 1);

function custom_set_cart_item_price($cart) {
    
    
    $cart = WC()->cart;

    foreach ($cart->get_cart() as $cart_item){
        if($cart_item['booking_information']['service_type'] ==  "Hourly/Disposal"){
            foreach ($cart->get_cart() as $cart_item) {
                
                $product = $cart_item['data'];
                $_price_per_hour = get_post_meta($product->get_id(), '_price_per_hour', true);
                $product->set_price($_price_per_hour);
                if($cart_item['booking_information']['additional_stop'] == 1){
                    $cart->add_fee( 'Additional Stop Fee Purchase', 25 );
                }
                if($cart_item['booking_information']['midnight_fee'] == 1){
                    $cart->add_fee( 'Additional Midnight Fee Purchase', 25 );
                }
            }
        }
    }
}
