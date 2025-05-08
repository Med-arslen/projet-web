const ctx = document.querySelector('.donut-chart').getContext('2d');
new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Returning Viewers', 'New Viewers'],
    datasets: [{
      data: [75, 25],
      backgroundColor: ['#28a745', '#e0e0e0'],
      borderWidth: 2
    }]
  },
  options: {
    plugins: {
      legend: { display: false },
      tooltip: { enabled: false }
    },
    cutout: '70%',
  }
});
 /*reservation*/










/*function updatePrice() {

const numPeople = document.getElementById('people').value;

const maxSpots = 50;

if (numPeople > maxSpots) {

  document.getElementById('errorMessage').style.display = 'block';
} else {

  document.getElementById('errorMessage').style.display = 'none';

  document.getElementById('price').innerText = numPeople * 15; 
}
}*/









/*function updatePrice() {
const numPeople = document.getElementById("people").value;
const price = numPeople * 15; // Assuming the price is 15 DT per person
document.getElementById("price").innerText = price;
}

// Handle form submission and store the reservation data in localStorage
document.getElementById('reservationFormForm').addEventListener('submit', function(e) {
e.preventDefault();

// Get input values
const name = document.getElementById('name').value;
const email = document.getElementById('email').value;
const phone = document.getElementById('phone').value;
const people = document.getElementById('people').value;
const price = document.getElementById('price').innerText;

// Create a reservation object
const reservation = {
  name: name,
  email: email,
  phone: phone,
  people: people,
  price: price
};

// Get existing reservations from localStorage or create an empty array if none exists
let reservations = JSON.parse(localStorage.getItem('reservations')) || [];

// Add the new reservation to the list
reservations.push(reservation);

// Save the updated reservations array back to localStorage
localStorage.setItem('reservations', JSON.stringify(reservations));

// Show success message
document.getElementById('successMessage').style.display = 'block';

// Reset the form
document.getElementById('reservationFormForm').reset();
});*/


function showReservationForm(eventData) {
    
    document.getElementById("reservationForm").style.display = "flex";

  
 
    document.getElementById("eventId").value = eventData.id_event;
  
  
    document.getElementById("eventDate").textContent = eventData.date_event;
    document.getElementById("price").textContent = eventData.price_event;
  
   
    document.getElementById("reservationForm").dataset.basePrice = eventData.price_event;
    document.getElementById("reservationForm").dataset.totalPlaces = eventData.total_places;
    document.getElementById("reservationForm").dataset.user_id=eventData.id_client;

  
  
    document.getElementById("people").value = 1;
    document.getElementById("errorMessage").style.display = "none";

    
  }
  
  function closeReservationForm() {
    document.getElementById("reservationForm").style.display = "none";
  }
  
  function updatePrice() {
    window.addEventListener('DOMContentLoaded', updatePrice);

    const basePrice = parseFloat(document.getElementById("reservationForm").dataset.basePrice || 0);
    const nb_place=parseInt(document.getElementById("reservationForm").dataset.totalPlaces || 1);
    const people = parseInt(document.getElementById("people").value || 1);
    const total = basePrice * people;
    document.getElementById("price").textContent = total.toFixed(2);
    document.getElementById("price_input").value = total.toFixed(2);
    const input_place=document.getElementById("people");
    input_place.setAttribute('max',nb_place+1);
    if(nb_place<people){
      document.getElementById("notavailableplace").innerHTML="<strong>max places available is "+nb_place+"</strong>";
      document.getElementById("submit_button").disabled=true;
    /*input_place.addEventListener("keydown",function (e){
      const value=parseInt(input_place.value)||1;
      if((e.key=="ArrowUp" || e.key=='+' || e.key=='=')&& value>=nb_place){
        input_place.value=nb_place;
      }
     });
      input_place.addEventListener("input",()=>{

        if(parseInt(input_place.value>nb_place)){
          input_place.value=nb_place;
        }
      });
     // document.getElementById("people").disabled=true;*/

    }
    else{
      document.getElementById("notavailableplace").innerHTML="";
     document.getElementById("submit_button").disabled=false;

    }
     
  }


  


 





