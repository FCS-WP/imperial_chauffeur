import { Calendar, Options } from 'vanilla-calendar-pro';

//option vanilla calendar js
const options = {
    
  type: 'multiple',
  displayMonthsCount: 2,
  monthsToSwitch: 2,
  displayDatesOutside: false,
  disableDatesPast: true,
  enableEdgeDatesOnly: true,
  selectionDatesMode: 'multiple-ranged',
  onClickDate(self) {
    
    var date_range = self.context.selectedDates;
    const pickupdate = document.getElementById('pickupdate');
    const DropOffDate = document.getElementById('DropOffDate');
    pickupdate.value = convertDate(date_range[0]);
    console.log("check");
    if (date_range[1] === undefined) {
      
    } else {
      DropOffDate.value = convertDate(date_range[1]);
      caculater_booking_total_price();
    }
    
    
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
function caculater_booking_total_price(){
  const DefaultpriceText = document.getElementById("default-price").textContent;
  const priceElement = document.getElementById("price-total");
  const DefaultpriceNumber = parseFloat(DefaultpriceText.replace(/,/g, ''));
  const pickupDateValue = document.getElementById("pickupdate").value;
  const dropOffDateValue = document.getElementById("DropOffDate").value;
  const [pickupDay, pickupMonth, pickupYear] = pickupDateValue.split('-').map(Number);
  const [dropOffDay, dropOffMonth, dropOffYear] = dropOffDateValue.split('-').map(Number);

  const pickupDate = new Date(pickupYear, pickupMonth - 1, pickupDay); 
  const dropOffDate = new Date(dropOffYear, dropOffMonth - 1, dropOffDay);

  const differenceInTime = dropOffDate - pickupDate;

  const differenceInDays = differenceInTime / (1000 * 60 * 60 * 24);  
  
  var priceNumber = DefaultpriceNumber;

  
  var total_price = priceNumber * differenceInDays;
  
  priceElement.innerText = total_price;

}

