import { Calendar, Options } from "vanilla-calendar-pro";

$(document).ready(function () {
  // Init date picker for hourly booking
  const options = {
    disableDatesPast: true,
    selectionTimeMode: 24,
    onClickDate(self) {
      const selectedDate = self.context.selectedDates[0];
      const selectedTime = self.context.selectedTime;
      $("#hbk_pickup_date").val(selectedDate);
      $("#hbk_pickup_time").val(selectedTime);

      if (isToday(selectedDate)) {
        let today = new Date();
        self.set({
          selectedDates: self.context.selectedDates,
          timeMinHour: today.getHours() + 1,
          timeMaxHour: 23,
        });
        let newHours = today.getHours() + ':0'; 
        midnightCheck(newHours);
      } else {
        self.set({
          selectedDates: self.context.selectedDates,
          timeMinHour: 0,
          timeMaxHour: 23,
        });
        midnightCheck("0:0");
      }

    },
    onChangeTime(self) {
      midnightCheck(self.context.selectedTime);
      $("#hbk_pickup_time").val(self.context.selectedTime);
    },
  };
  if ($("#tab_hour_picker").length > 0) {
    const tabHourPicker = new Calendar("#tab_hour_picker", options);
    tabHourPicker.init();
  }

  // Function display price with domestic:
  if ($("#hbk_pickup_fee").length > 0) {
    $("#hbk_pickup_fee").on("change", function () {
      calcHbkPrices();
    });
  }
  if ($("#hbk_time_value").length > 0) {
    $("#hbk_time_value").on("change", function () {
      calcHbkPrices();
    });
  }
  if ($("#hbk_midnight_fee").length > 0) {
    $("#hbk_midnight_fee").on("change", function () {
      calcHbkPrices();
    });
  }

  const openPopupButtonHour = document.getElementById("openPopupHour");
  const closePopupButtonHour = document.getElementById("closePopupHour");
  const popupHour = document.getElementById("popupHour");

  // Open popup
  openPopupButtonHour.addEventListener("click", () => {
    popupHour.style.display = "flex";
  });

  // Close popup
  closePopupButtonHour.addEventListener("click", () => {
    popupHour.style.display = "none";
  });

  // Close popup when clicking outside the content
  popupHour.addEventListener("click", (event) => {
    if (event.target === popup) {
      popupHour.style.display = "none";
    }
  });
});

function calcHbkPrices() {
  let productPrice = $("#hbk_total_price").data("product-price");
  let timeValue =
    $("#hbk_time_value").val() !== "" ? $("#hbk_time_value").val() : 1;
  let pickupFee = $("#hbk_pickup_fee").val() == 1 ? 25 : 0;
  let midnightFee = $("#hbk_midnight_fee").val() == 1 ? 25 : 0;
  let totalPrice =
    parseFloat(productPrice) * parseInt(timeValue) + parseFloat(pickupFee) + parseFloat(midnightFee);
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

function midnightCheck(time) {
  const [hours, minutes] = time.split(":").map(Number);
  if (hours > 22 || hours < 7) {
    $("#hbk_midnight_fee").val("1");
    $("#note_midnight_fee").show();
  } else {
    $("#hbk_midnight_fee").val("0");
    $("#note_midnight_fee").hide();
  }
  calcHbkPrices();
  return true;
}