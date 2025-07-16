<form method="post">
  <?php

  wp_nonce_field('save_custom_fields_' . $order->get_id()); ?>
  <div class="field-columns">

    <?php foreach ($custom_fields as $key => $label) : ?>
      <?php $value = get_post_meta($order->get_id(), $key, true); ?>

      <?php if (!empty($value)) : ?>
        <p>
          <label for="<?php echo esc_attr($key); ?>">
            <strong><?php echo esc_html($label); ?>:</strong>
          </label><br />

          <?php
          $field_types = [
            'pick_up_date'     => 'date',
            'pick_up_time'     => 'text',
            'eta_time'         => 'text',
            'no_of_passengers' => 'number',
            'no_of_baggage'    => 'number',
            'service_type'     => 'options'
          ];

          $type = $field_types[$key] ?? 'text';

          // Handle service type options
          if ($type === 'options') :
            $service_options = [
              'Airport Arrival Transfer',
              'Airport Departure Transfer',
              'Point-to-point Transfer',
              'Hourly/Disposal'
            ];
          ?>
            <select
              style="width: 400px; background: none; margin-top: 10px;"
              id="<?php echo esc_attr($key); ?>"
              name="<?php echo esc_attr($key); ?>"
              required>
              <option value="">-- Select Service Type --</option>
              <?php foreach ($service_options as $option) : ?>
                <option value="<?php echo esc_attr($option); ?>" <?php selected($value, $option); ?>>
                  <?php echo esc_html($option); ?>
                </option>
              <?php endforeach; ?>
            </select>

          <?php elseif ($key === 'pick_up_date') :
            $pickupdate = date('d-m-Y', strtotime($value));
          ?>
            <input
              class="js-datepicker"
              id="<?php echo esc_attr($key); ?>"
              type="text"
              name="<?php echo esc_attr($key); ?>"
              value="<?php echo esc_attr($pickupdate); ?>"
              style="width: 100%;" />

          <?php else : ?>
            <input
              id="<?php echo esc_attr($key); ?>"
              type="<?php echo esc_attr($type); ?>"
              name="<?php echo esc_attr($key); ?>"
              value="<?php echo esc_attr($value); ?>"
              style="width: 100%;" />
          <?php endif; ?>
        </p>
      <?php endif; ?>
    <?php endforeach; ?>


  </div>
  <!-- Customer edit -->
  <h2 class="woocommerce-order-details__title">Customer Information:</h2>
  <div class="field-columns">
    <p><label><strong>Customer Name:</strong></label><br />
      <input
        id="billing_first_name"
        type="text"
        name="billing_first_name"
        value="<?php echo $order->get_billing_first_name() ?>"
        style="width: 100%;" />
    </p>
     <p><label><strong>Customer phone:</strong></label><br />
      <input
        id="billing_phone"
        type="text"
        name="billing_phone"
        value="<?php echo $order->get_billing_phone() ?>"
        style="width: 100%;" />
    </p>
  </div>
  <p><button type="submit" class="button button-black ">Save</button></p>
</form>
