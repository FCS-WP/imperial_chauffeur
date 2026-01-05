import "../lib/flatpickr/flatpickr.min.js";
import "./wc-order-add-product-type.js";

jQuery(document).ready(function ($) {
  const initFlatpickr = function () {
    const $pickUp = $("#pick_up_time");
    const $eta = $("#eta_time");
    if (typeof flatpickr !== "undefined") {
      if ($pickUp.length) {
        $pickUp.flatpickr({
          enableTime: true,
          noCalendar: true,
          dateFormat: "H:i",
          time_24hr: true,
        });
      }

      if ($eta.length) {
        $eta.flatpickr({
          enableTime: true,
          noCalendar: true,
          dateFormat: "H:i",
          time_24hr: true,
        });
      }

      if ($(".js-datepicker").length) {
        $(".js-datepicker").flatpickr({
          dateFormat: "d-m-Y",
        });
      }
    } else {
      setTimeout(initFlatpickr, 300);
    }
  };

  initFlatpickr();
});
