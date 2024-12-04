import { Calendar, Options } from "vanilla-calendar-pro";

$(document).ready(function () {
  // Init date picker for hour booking
  const options = {
    disableDatesPast: true,
    selectedTheme: "dark",
    selectionTimeMode: 24,
    onClickDate(self) {
      const selectedDate = self.context.selectedDates[0];
      $("#hbk_pickup_date").val(selectedDate);
      $("#hbk_pickup_time").val(self.context.selectedTime);

      if (isToday(selectedDate)) {
        let today = new Date();
        self.set({
          timeMinHour: today.getHours(),
          timeMaxHour: 23,
        });
      } else {
        self.set({
          timeMinHour: 0,
        });
      }
    },
    onChangeTime(self) {
      $("#hbk_pickup_time").val(self.context.selectedTime);
    },
  };
  if ($("#tab_hour_picker").length > 0) {
    const tabHourPicker = new Calendar("#tab_hour_picker", options);
    tabHourPicker.init();
  }

  // Function display price with domestic:
  if ($("#hbk_pickup_type").length > 0) {
    $("#hbk_pickup_type").on("change", function () {
      let getPrice = $(this).find(":selected").data("price");
      $("#hbk_service_fees").val(getPrice);
      calcHbkPrices();
    });
  }
  if ($("#hbk_time_value").length > 0) {
    $("#hbk_time_value").on("change", function () {
      calcHbkPrices();
    });
  }

  const openPopupButton = document.getElementById("openPopup");
  const closePopupButton = document.getElementById("closePopup");
  const popup = document.getElementById("popup");

  // Open popup
  openPopupButton.addEventListener("click", () => {
    popup.style.display = "flex";
  });

  // Close popup
  closePopupButton.addEventListener("click", () => {
    popup.style.display = "none";
  });

  // Close popup when clicking outside the content
  popup.addEventListener("click", (event) => {
    if (event.target === popup) {
      popup.style.display = "none";
    }
  });
});

function calcHbkPrices() {
  let productPrice = $("#hbk_total_price").data("product-price");
  let timeValue =
    $("#hbk_time_value").val() !== "" ? $("#hbk_time_value").val() : 1;
  let pickupFee = $("#hbk_service_fees").val();
  let totalPrice =
    parseFloat(productPrice) * parseInt(timeValue) + parseFloat(pickupFee);
  $("#hbk_total_price").text(totalPrice);
}

function isToday(compareDate) {
  let date1 = new Date();
  let date2 = new Date(compareDate);

  date1.setHours(0, 0, 0, 0);
  date2.setHours(0, 0, 0, 0);

  if (date1 > date2 || date1 < date2) {
    return false;
  }
  return true;
}
