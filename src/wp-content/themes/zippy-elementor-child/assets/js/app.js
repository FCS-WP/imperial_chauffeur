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
    }
  },
  onChangeTime(self) {
    var time = self.context.selectedTime;
    const get_time_pickup = document.getElementById('get_time_pickup');
    get_time_pickup.innerText = time;
    const pickuptime = document.getElementById('pickuptime');
    pickuptime.value = time;
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

const insideradioInput = document.getElementById('inside_additional_stop');
const outsideradioInput = document.getElementById('outside_additional_stop');
const oneradioInput = document.getElementById('1perway');
const tworadioInput = document.getElementById('2perway');
const default_price = document.getElementById('default-price');
const result_price = document.getElementById('price-total');
var default_price_number = default_price.textContent;
var total_price = 0;
var additional_stop = 0;
var result_price_number = 0;

insideradioInput.addEventListener('change', function (event) {
  if (event.target.checked) {
    additional_stop = 0;
    result_price_number = result_price.textContent;
    result_price.innerHTML = (Number(result_price_number) -25);
    
  }
});

outsideradioInput.addEventListener('change', function (event) {
  if (event.target.checked) {
    additional_stop = 25;
    result_price_number = result_price.textContent;
    result_price.innerHTML = (Number(result_price_number) + 25);
  }
});


oneradioInput.addEventListener('change', function (event) {
  if (event.target.checked) {
    total_price = Number(default_price_number)*1 + additional_stop ;
    result_price.innerHTML = total_price;
  }
});


tworadioInput.addEventListener('change', function (event) {    
  if (event.target.checked) {
    total_price = Number(default_price_number)*2 + additional_stop ;
    result_price.innerHTML = total_price;
  }
});


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