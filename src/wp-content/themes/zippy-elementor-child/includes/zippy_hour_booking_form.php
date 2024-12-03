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
    <form method="POST">
        <div class="box-pickup-information">
            <div class="layout-row">
                <div class="layout-col-6">
                    <div class="calendar-box-custom">
                        <div class="calendar-box"><div id="tab_hour_picker"></div></div>
                    </div>
                </div>
                <div class="layout-col-6">
                    <div class="summary-information no-flex">
                        <div class="col-summary-information">
                            <!-- <label>To</label><input class="d-flex" id="picked_date" value="<?php echo $today;?>" type="text" name="picked_date"> -->
                             <label> Date:</label>
                             <input id="hbk_selected_date" name="hbk_selected_date" value="<?php echo $today;?>"/>
                        </div>
                        <div class="col-summary-information">
                            <label>Pick Up:</label><input type="time" id="hbk_pickup_time" name="hbk_pickup_time" min="00:00" max="24:00" value="<?php echo date("H:i"); ?>" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="input-text-pickup-information">
                <div class="row-form-custom">
                    <input name="id_product" type="hidden" value="<?php echo $product->get_id();?>">
                    <input name="key_member" type="hidden" value="<?php echo $key_member;?>">
                </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="servicetype">Time</label>
                        <select class="" id="service_type" name="service_type">
                            <option value="">Please choose an option</option>
                            <option value="3-hours">3 hours</option>
                            <option value="4-hours">4 hours</option>
                            <option value="5-hours">5 hours</option>
                            <option value="6-hours">6 hours</option>
                            <option value="7-hours">7 hours</option>
                            <option value="8-hours">8 hours</option>
                            <option value="9-hours">9 hours</option>
                            <option value="10-hours">10 hours</option>
                            <option value="11-hours">11 hours</option>
                            <option value="12-hours">12 hours</option>
                        </select>
                    </div>
                    <div class="col-form-custom">
                        <label for="hbk_flight_detail">Flight Details<span style="color:red;">*</span></label>
                        <input size="40" maxlength="400" class="" id="hbk_flight_detail" aria-required="true" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="hbk_flight_detail">
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
                        <label for="hbk_special_request">Special Requests</label>
                        <input size="40" maxlength="400" class="" id="hbk_special_request" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="hbk_special_request">
                    </div>
                </div>
            </div>
            <div class="col-total-price-information">
                <label>Total Price: </label><span > $<span id="price-total"><?php echo $current_price = $product->get_price();?></span><span id="default-price" style="display:none"><?php echo $current_price = $product->get_price();?></span></span>
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

//function process submit pick up information
// function process_booking_time(){
//     $status_redirect = false;
//     if (isset($_POST['submit_hour_booking_form'])) {
//         $id_product = sanitize_text_field($_POST['id_product']);
//         $pickupdate = sanitize_text_field($_POST['pickupdate']);
//         $pickuptime = sanitize_text_field($_POST['pickuptime']);
//         $DropOffDate = sanitize_text_field($_POST['DropOffDate']);
//         $DropOffTime = sanitize_text_field($_POST['DropOffTime']);
        
//         $time_use = 0;

//         if (!empty($pickupdate) && !empty($DropOffDate)) {
//             try {
//                 $pickup_date = new DateTime($pickupdate);
//                 $dropoff_date = new DateTime($DropOffDate);
        
//                 // Calculate the difference
//                 $interval = $pickup_date->diff($dropoff_date);
//                 $time_use = $interval->days; // Total days difference
//             } catch (Exception $e) {
//                 // Handle any potential errors
//                 error_log('Date calculation error: ' . $e->getMessage());
//             }
//         }
        
//         $cart = WC()->cart;
//         $cart->empty_cart();
//         $cart->add_to_cart($id_product, $time_use);
//         $status_redirect = true;
        
//     }
//     if($status_redirect == true){
//         wp_redirect(wc_get_checkout_url());
//         exit;
//     }
// }
// add_action('init', 'process_booking_time');

//function add pick up information to cart
// add_filter('woocommerce_add_cart_item_data', 'add_custom_cart_item_data_time');
// function add_custom_cart_item_data_time($cart_item_data)
// {
//     if (isset($_POST['submit_car_booking_time'])) {
//         $key_member = sanitize_text_field($_POST['key_member']);
//         $pickupdate = sanitize_text_field($_POST['pickupdate']);
//         $pickuptime = sanitize_text_field($_POST['pickuptime']);
//         $DropOffDate = sanitize_text_field($_POST['DropOffDate']);
//         $DropOffTime = sanitize_text_field($_POST['DropOffTime']);
//         $pickuplocation = sanitize_text_field($_POST['pickuplocation']);
//         $dropofflocation = sanitize_text_field($_POST['dropofflocation']);
//         $servicetype = sanitize_text_field($_POST['servicetype']);
//         $flight_details = sanitize_text_field($_POST['flight_details']);

//         $cart_item_data['time_booking'] = array(
//             'pickupdate' => $pickupdate,
//             'pickuptime' => $pickuptime,
//             'DropOffDate' => $DropOffDate,
//             'DropOffTime' => $DropOffTime,
//             'pickuplocation' => $pickuplocation,
//             'dropofflocation' => $dropofflocation,
//             'servicetype' => $servicetype,
//             'flight_details' => $flight_details,
//             'key_member' => $key_member,
//         );
//     }

//     return $cart_item_data;
// }

