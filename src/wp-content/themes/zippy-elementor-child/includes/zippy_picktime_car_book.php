<?php
//shortcode vanilla_booking_car_custom create form booking on single product page
function vanilla_booking_car_custom(){
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
                </div>
                <div class="row-form-custom col-2"  id="openPopup">
                    <div class="col-form-custom">
                        <label for="pickupdate">Pick Up Date</label>
                        <input class="pickupdate" id="pickupdate" value="<?php echo $today;?>" type="text" name="pick_up_date" required>
                        
                    </div>
                    <div class="col-form-custom">
                        <label for="pickuptime">Pick Up Time</label>
                        <input type="text" id="pickuptime" name="pick_up_time" min="00:00" max="24:00" value="<?php echo date("H:i"); ?>" required>
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
                    <lable>How many per way?</lable>
                        <div class="input-radio-box">    
                            <div class="input-radio-box-col">
                                <input class="" id="1perway" aria-required="true" aria-invalid="false" value="1" type="radio" name="per_way" checked="checked" required >
                                <label for="1perway">1 Per Way</label>
                            </div>
                            <div class="input-radio-box-col">
                                <input class="" id="2perway" aria-required="true" aria-invalid="false" value="2" type="radio" name="per_way" required>
                                <label for="2perway">2 Per Ways</label> 
                            </div>
                        </div>
                    </div>
                    <div class="col-form-custom">
                        <lable>Additional Stop</lable>
                        <div class="input-radio-box">    
                            <div class="input-radio-box-col">
                                <input class="" id="inside_additional_stop" aria-required="true" aria-invalid="false" value="0" type="radio" name="additional_stop" checked="checked" required>
                                <label for="inside_additional_stop">Inside Singapore</label>
                            </div>
                            <div class="input-radio-box-col">
                                <input class="" id="outside_additional_stop" aria-required="true" aria-invalid="false" value="1" type="radio" name="additional_stop" required>
                                <label for="outside_additional_stop">Outside Singapore</label> 
                            </div>
                        </div>
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
                        <label for="servicetype">ETE/ETA</label>
                        <input type="text" name="time_flight" id="time_flight" placeholder="Enter time">
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
add_shortcode('vanilla_booking_car_custom', 'vanilla_booking_car_custom');

//function process submit pick up information
function process_booking_time(){
    $status_redirect = false;
    
    if (isset($_POST['submit_car_booking_time'])) {
        $id_product = sanitize_text_field($_POST['id_product']);
        $per_way = sanitize_text_field($_POST['per_way']);
        
        
        $cart = WC()->cart;
        $cart->empty_cart();
        $cart->add_to_cart($id_product, $per_way);
       
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
    if (isset($_POST['submit_car_booking_time'])) {
        $key_member = sanitize_text_field($_POST['key_member']);
        $pick_up_date = sanitize_text_field($_POST['pick_up_date']);
        $pick_up_time = sanitize_text_field($_POST['pick_up_time']);
        $pick_up_location = sanitize_text_field($_POST['pick_up_location']);
        $drop_off_location = sanitize_text_field($_POST['drop_off_location']);
        $service_type = sanitize_text_field($_POST['service_type']);
        $flight_details = sanitize_text_field($_POST['flight_details']);
        $per_way = sanitize_text_field($_POST['per_way']);
        $no_of_passengers = sanitize_text_field($_POST['no_of_passengers']);
        $no_of_baggage = sanitize_text_field($_POST['no_of_baggage']);
        $additional_stop = sanitize_text_field($_POST['additional_stop']);

        $cart_item_data['time_booking'] = array(
            'pick_up_date' => $pick_up_date,
            'pick_up_time' => $pick_up_time,
            'pick_up_location' => $pick_up_location,
            'drop_off_location' => $drop_off_location,
            'service_type' => $service_type,
            'flight_details' => $flight_details,
            'per_way' => $per_way,
            'no_of_passengers' => $no_of_passengers,
            'no_of_baggage' => $no_of_baggage,
            'key_member' => $key_member,
            'additional_stop' => $additional_stop,
        );
    }

    return $cart_item_data;
}




