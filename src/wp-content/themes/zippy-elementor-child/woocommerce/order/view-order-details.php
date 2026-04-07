	<div class="field-columns">

	  <?php foreach ($custom_fields as $key => $label): ?>

	    <?php $value = $order->get_meta($key); ?>
	    <?php if (! empty($value)): ?>
	      <?php
	      // Convert pick_up_date from Y-m-d to d-m-Y for display
	      if ($key === 'pick_up_date' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
	        $value = date('d-m-Y', strtotime($value));
	      }
	      ?>
	      <p><strong><?php echo esc_html($label); ?>:</strong> <?php echo esc_html($value); ?></p>
	    <?php endif; ?>
	  <?php endforeach; ?>

	  <?php if ($service_type == "Hourly/Disposal") {
      echo "<p><strong>Duration: </strong> $order_quantity Hours</p>";
    } ?>

	</div>
	<h2 class="woocommerce-order-details__title">Customer Information:</h2>
	<div class="field-columns">
	  <p><label><strong>Name:</strong></label><br />
	    <?php echo $order->get_billing_first_name() ?>
	  </p>
	  <p><strong>Phone number:</strong><br />
	    <?php echo $order->get_billing_phone() ?>
	  </p>
	</div>
	<div style="text-align:left;margin:30px 0px 20px 0px;">
	  <?php
    if (!is_wc_endpoint_url('order-received')) {
      if (!in_array($order->get_status(), ['completed', 'cancelled'])) {
        try {
          $canEdit = can_edit_order($order->get_id());

          if ($canEdit) {
            echo '<a class="button button-black red" href="' . esc_url(add_query_arg('edit_order', $order->get_id())) . '">Edit</a>';
          } else {
            echo '<p style="font-style: italic;">This order is scheduled in less than 24 hours and can no longer be edited or changed.<br>For any enquiries, please contact us directly. Thank you for your understanding!</p>';
          }
        } catch (Exception $e) {
          echo '<p style="font-style: italic;">An error occurred while checking the order time.<br>Please contact support for further assistance.</p>';
        }
      }
    }
    ?>

	</div>
