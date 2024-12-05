import { Calendar, Options } from 'vanilla-calendar-pro';

//option vanilla calendar js
const options = {
  selectionTimeMode: 24,
  layouts: {
    default: `
      <h5 class="heading-custom-vanilla">Pick Up Date</h5>
      <div class="vc-header" data-vc="header" role="toolbar" aria-label="Calendar Navigation">
        <#ArrowPrev />  
        <div class="vc-header__content" data-vc-header="content">
          <#Year /> | <#Month />
        </div>
        <#ArrowNext />
      </div>
      <div class="vc-wrapper" data-vc="wrapper">
        <#WeekNumbers />
        <div class="vc-content" data-vc="content">
          <#Week />
          <#Dates />
          <#DateRangeTooltip />
        </div>
        </div>
      <#ControlTime />
      <div class="time-avail">
        <div class="time-avail__item">
          <p>Pick up time</p><p id="get_time_pickup">00:00</p>
        </div>
        <div class="time-avail__item">
          <p>Pick up date</p><p id="get_date_pickup">04-12-2024</p>
        </div>
      </div>
      
    `,
  },
  onClickDate(self) {
    var date = self.context.selectedDates;
    
    if (date[0] !== undefined) {
      const pickupdate = document.getElementById('pickupdate');
      pickupdate.value = convertDate(date);
      const get_date_pickup = document.getElementById('get_date_pickup');
      get_date_pickup.innerText = convertDate(date);
      midnightCheck(self.context.selectedTime);
    }
  },
  onChangeTime(self) {
    var time = self.context.selectedTime;
    const get_time_pickup = document.getElementById('get_time_pickup');
    get_time_pickup.innerText = time;
    const pickuptime = document.getElementById('pickuptime');
    pickuptime.value = time;
    midnightCheck(self.context.selectedTime);
  },

};

const calendar = new Calendar('#calendar', options);
calendar.init();

//function covert format date from yyyy-mm-dd to dd-mm-yyyy
function convertDate(inputDate) {
  const date = new Date(inputDate);
  const day = String(date.getDate()).padStart(2, '0'); // Ensure two digits
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
  const year = date.getFullYear();

 return `${day}-${month}-${year}`;
}

//function caculater booking total price after change date range

const selectElement = document.getElementById('additional_stop');
const result_price = document.getElementById('price-total');
const productPrice = result_price.getAttribute('data-product-price');

var total_price = 0;
var additional_stop = 0;
var result_price_number = 0;

const openPopupButton = document.getElementById('openPopup');
const closePopupButton = document.getElementById('closePopup');
const popup = document.getElementById('popup');

// Open popup
openPopupButton.addEventListener('click', () => {
    popup.style.display = 'flex';
});

// Close popup
closePopupButton.addEventListener('click', () => {
    popup.style.display = 'none';
});

// Close popup when clicking outside the content
popup.addEventListener('click', (event) => {
    if (event.target === popup) {
        popup.style.display = 'none';
    }
});


document.getElementById('servicetype').addEventListener('change', function() {
  var selectedValue = this.value; 
  var inputFlightDiv = document.getElementById('input-flight'); 
  
  if (selectedValue === 'Point-to-point Transfer') {
      inputFlightDiv.style.display = 'none'; 
  } else {
      inputFlightDiv.style.display = 'flex';
  }
});

function midnightCheck(time) {
  const [hours, minutes] = time.split(":").map(Number);
  if (hours > 22 || hours < 7) {
    $(".note-trip-midnight").show();
    $("#trip_midnight_fee").val(1);
    
  } else {
    $(".note-trip-midnight").hide();
    $("#trip_midnight_fee").val(0);
    
  }
  cacul_midnight_time($("#trip_midnight_fee").val());
  return true;
}

selectElement.addEventListener('change', function() {
  
  const selectedOption = selectElement.options[selectElement.selectedIndex];

  
  const value = selectedOption.value;
  
  if(value == 0){
    result_price_number = result_price.textContent;
    result_price.innerHTML = (Number(result_price_number) - 25);
  }
  if(value == 1){
    result_price_number = result_price.textContent;
    result_price.innerHTML = (Number(result_price_number) + 25);
  }
  
});


function cacul_midnight_time(val){
  result_price_number = result_price.textContent;
    
  if(val == 1){
    result_price.innerHTML = (Number(productPrice) + 25);
  }
  if(val == 0){
    result_price.innerHTML = (Number(productPrice));
  }
  return;
}

