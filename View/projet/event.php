<!DOCTYPE html>
<?php
session_start();

require_once "../../config/database.php";

require_once '../../controllers/eventcontroller.php';
$conn = config::getConnexion();
/*if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}*/
$id_client = $_SESSION['id_user']?? 1; // Default to 1 if not set
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <style>
    * {
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.menu-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* General container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
  padding: 25px 20px;
}
/* Translate Widget Container */
.translate-box {
  display: inline-block;
  background-color: #ffffff;
  border-radius: 8px;
  padding: 4px 8px;
  border: 1px solid #ccc;
  margin-left: 15px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
  font-size: 12px;
  transition: box-shadow 0.2s ease, background-color 0.2s;
}

.translate-box:hover {
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  background-color: #f9f9f9;
}

/* Remove "Powered by Google Translate" */
.goog-te-gadget span {
  display: none !important;
}

/* Smaller dropdown */
.goog-te-combo {
  background-color: transparent;
  border: none;
  font-size: 12px;
  font-family: 'Segoe UI', sans-serif;
  color: #333 !important;
  padding: 4px 8px;
  border-radius: 8px;
  outline: none;
  cursor: pointer;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: background-color 0.2s ease;
}

/* Hover effect */
.goog-te-combo:hover {
  background-color: #f0f0f0;
}



.container .heading {
  font-size: 40px;
  margin-bottom: 20px;
  color: #334;
}

/* Event list */
.event-list-item {
  margin-bottom: 15px;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #fafafa;
  transition: box-shadow 0.3s ease;
}

.event-list-item:hover {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.event-list-item .event-name {
  font-weight: bold;
  color: #1a1a1a;
  font-size: 18px;
}

.event-list-item .event-date {
  color: #555;
}

.event-list-item .film-title {
  color: #777;
  font-style: italic;
}

/* Event button */
.event-button {
  background-color: #4CAF50;
  color: white;
  padding: 10px 16px;
  margin: 5px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}

.event-button:hover {
  background-color: #45a049;
}

/* Chatbot Icon */
#chatbot-icon {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 60px;
  height: 60px;
  background-color: #d32f2f;
  color: white;
  font-size: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
  cursor: pointer;
  transition: transform 0.2s ease, background-color 0.2s ease;
}

#chatbot-icon:hover {
  transform: scale(1.1);
  background-color: #c62828;
}

/* Chatbot container */
#chatbot-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 320px;
  height: 450px;
  background-color: #fefefe;
  border-radius: 12px;
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.2);
  display: none;
  flex-direction: column;
  z-index: 1000;
}

#chatbot-header {
  padding: 12px;
  background-color: #b71c1c;
  color: white;
  font-weight: bold;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
}

/* Close button */
#close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 22px;
  cursor: pointer;
  width:10px;
}

/* Chat body */
#chatbot-body {
  flex: 1;
  padding: 10px;
  background-color: #fff;
  overflow-y: auto;
  font-size: 14px;
  line-height: 1.5;
  scroll-behavior: smooth;
}

/* Custom scroll */
#chatbot-body::-webkit-scrollbar {
  width: 6px;
}
#chatbot-body::-webkit-scrollbar-thumb {
  background-color: #b71c1c;
  border-radius: 10px;
}

/* Input section */
#chatbot-input-container {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ccc;
  background-color: #f0f0f0;
}

#chatbot-input {
  flex: 1;
  padding: 10px;
  border: 1px solid #aaa;
  border-radius: 8px;
  font-size: 14px;
  background-color: white;
  color: #333;
}

#send-btn {
  margin-left: 10px;
  padding: 10px 15px;
  background-color: #b71c1c;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}

#send-btn:hover {
  background-color: #a31818;
}

/* Messages */
.message {
  margin-bottom: 12px;
  padding: 10px 14px;
  border-radius: 10px;
  max-width: 85%;
  word-wrap: break-word;
}

.user-message {
  background-color: #d32f2f;
  color: white;
  align-self: flex-end;
  text-align: right;
}

.bot-message {
  background-color: #eeeeee;
  color: #222;
  align-self: flex-start;
  border: 1px solid #ccc;
}

/* Question buttons */
.clickable-question {
  background-color: #fff;
  border: 1px solid #d32f2f;
  color: #d32f2f;
  padding: 8px 12px;
  border-radius: 6px;
  margin: 5px 0;
  cursor: pointer;
  width: 100%;
  text-align: left;
  font-size: 14px;
  transition: background-color 0.3s, color 0.3s;
}

.clickable-question:hover {
  background-color: #d32f2f;
  color: white;
}

.question-container {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.container .box-container{
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap:20px;
}

.container .box-container .box{
  background-color: #fff;
  padding:20px;
  border-radius: 5px;
  box-shadow: 0 5px 10px rgba(0,0,0,.2);
  display: none;
}
.container .box-container .box {
  display: block; /* or whatever display you want */
}


.container .box-container .box .image{
  margin-bottom: 20px;
  overflow: hidden;
  height: 250px;
  border-radius: 5px;
}

.container .box-container .box .image img{
  height: 100%;
  width: 100%;
  object-fit: cover;
}

.container .box-container .box:hover .image img{
  transform: scale(1.1);
}

.container .box-container .box .content h3{
  font-size: 20px;
  color:#334;
}

.container .box-container .box .content p{
  font-size: 15px;
  color:#777;
  line-height: 2;
  padding:15px 0;
}

.container .box-container .box .content .btn{
  display: inline-block;
  padding:10px 30px;
  border:1px solid #334;
  color:#334;
  font-size: 16px;
}

.container .box-container .box .content .btn:hover{
  background-color: crimson;
  border-color: crimson;
  color:#fff;
}

.container .box-container .box .content .icons{
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 20px;
  padding-top: 15px;
  border-top:1px solid #334;
}

.container .box-container .box .content .icons span{
  font-size: 14px;
  color:#777;
}

.container .box-container .box .content .icons span i{
  color:crimson;
  padding-right: 5px;
}

#load-more{
  margin-top: 20px;
  display: inline-block;
  padding:13px 30px;
  border:1px solid #334;
  color:#334;
  font-size: 16px;
  background-color: #fff;
  cursor: pointer;
}

#load-more:hover{
  background-color: crimson;
  border-color: crimson;
  color:#fff;
}

@media (max-width:450px){

  .container .heading{
    font-size: 25px;
  }

  .container .box-container{
    grid-template-columns: 1fr;
  }

  .container .box-container .box .image{
    height: 200px;
  }

  .container .box-container .box .content p{
    font-size: 12px;
  }

  .container .box-container .box .content .icons span{
    font-size: 12px;
  }

}
.box-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: flex-start; /* Prevent equal height boxes */
}

.box {
  flex: 0 1 300px;
  border: 1px solid #ccc;
  padding: 15px;
  box-sizing: border-box;
  transition: all 0.3s ease;
  position: relative;
}

.event-details {
  display: none;
  margin-top: 10px;
  background-color: #f9f9f9;
  padding: 10px;
  border-top: 1px solid #ccc;
}

.event-details.visible {
  display: block;
}



</style>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<!--<link rel="stylesheet" href="../../public/css/style.css">-->
    <link rel="stylesheet" href="../projet/css/event.css">
 
    <title>Document</title>
</head>
<b style="background: url('../backoffice/cinema.jpg') no-repeat center center fixed; background-size: cover;">
  <header>
    <div class="menu-bar">
    
 
        <div class="netflixLogo">
            <a id="logo" href="#home">
                <img src="../projet/assets/imgs/logo.png" alt="Logo Image" id="logo1">
            </a>
        </div>    

        <ul>
            <li><a href="../projet/page.html">Home</a></li>
            <li><a href="#">Catalog</a></li>
            <li>
                <a href="../projet/event.php">Events <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-menu">
                    <ul>
                        <li><a href="./historique.php">History</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="#">Purchases</a></li>
            <li><a href="#">Support</a></li>
        </ul>

        <nav class="sub-nav">
        <div id="google_translate_element" class="translate-box"></div>

          <a href="#"><i class="fas fa-search sub-nav-logo"></i></a>
          <a href="#"><i class="fas fa-bell sub-nav-logo"></i></a>
          <a href="#"><i class="fas fa-user sub-nav-logo"></i></a> <!-- Account icon here -->
      </nav>
    </div>
</header>
<section id="fond2">
    <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
          </div>
        <div class="carousel-inner">
          <div class="carousel-item active c-item">
            <img src="../projet/assets/imgs/pexels-quark-studio-2507025_720x.webp" class="d-block w-100 c-img" alt="slide 1">
            
              <div class="carousel-caption top-0 mt-4 d-none d-md-block text-center">
                <p class="mt-5 fs-3 text-uppercase text-warning" style="font-family: 'Arial', sans-serif;">Watch Movies Under the Stars</p>
                <h1 class="display-5 fw-bolder text-capitalize text-light" style="font-family: 'Roboto', sans-serif;">
                  Bring a blanket, grab some snacks, and enjoy a magical outdoor cinema experience.
                </h1>
                
              </div>
              
          </div>
          <div class="carousel-item c-item">
            <img src="../projet/assets/imgs/popcorn.jpg" class="d-block w-100 c-img" alt="slide 2">
            <div class="carousel-caption top-0 mt-4 d-none d-md-block text-center">
                <p class="mt-5 fs-3 text-uppercase text-warning" style="font-family: 'Arial', sans-serif;">Snacks, Lights, Action!</p>
                <h1 class="display-5 fw-bolder text-capitalize text-light" style="font-family: 'Roboto', sans-serif;">
                    Grab your favorite treats and enjoy the perfect movie night atmosphere.
                </h1>
               
              </div>
              
          </div>
          <div class="carousel-item c-item">
            <img src="../projet/assets/imgs/peoplewatching.webp" class="d-block w-100 c-img" alt="slide 3">
            <div class="carousel-caption top-0 mt-4 d-none d-md-block text-center">
                <p class="mt-5 fs-3 text-uppercase text-warning" style="font-family: 'Arial', sans-serif; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">
                  Capture Moments, Create Memories
                </p>
                <h1 class="display-4 fw-bolder text-capitalize text-light" style="font-family: 'Roboto', sans-serif; line-height: 1.4; letter-spacing: 1px; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7);">
                  Your next favorite memory starts here.
                  Check Out Our Exciting Events!
                </h1>
              </div>
              
              
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
      </div>

      <div class="events ">
        <h5>Explore Our Upcoming Events!</h5>
      </div>

     
      <div class="container">
   <input type="hidden" id="id_client" value="<?= $id_client ?>">

   <div class="box-container">
      <?php
      $sql = "SELECT event.*, film.title, film.photo_path 
              FROM event 
              JOIN film ON event.id_film = film.id_film";
      $stmt = $conn->query($sql);
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <div class="image">
            <img src="<?= $row['photo_path'] ?>" alt="Event Image">
         </div>
         <div class="content">
            <h3><?= $row['name_event'] ?></h3>
            <p><strong>Film Title:</strong> <?= $row['title'] ?></p>
            <a href="#" class="btn" >Read More</a>
            <div class="event-details" >
    <p><strong>Description:</strong> <?= $row['description'] ?></p>
    <p><strong>Price:</strong> <?= $row['price_event'] ?> DT</p>
  
    <p><strong>Date:</strong> <?= $row['date_event'] ?></p> 
    <p><strong>Location:</strong> <?= $row['location'] ?></p>
    <p><strong>Total Places:</strong> <?= $row['total_places'] ?></p>
    <button onclick='showReservationForm(<?= json_encode($row) ?>)'>Reserve NOW</button>
</div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>


<!-- Reservation Form (Initially Hidden) -->
<?php

require_once '../../controllers/eventcontroller.php';


if (isset($_POST["event_id"])) {
  $eventId = $_POST['event_id'];
    $eventController = new EventController();
    $result=$eventController->placeAvailability($eventId);
}
  
  
?>


<div id="reservationForm" class="reservation-form" style="display: none;">
  <div class="form-container">
    <span class="close-btn" onclick="closeReservationForm()">&times;</span>
    <h2>Reserve Your Spot</h2>
    
    <form action="add_reservation.php" method="POST" id="reservationFormContent">
      <input type="hidden" id="eventId" name="event_id">

      <label for="name">Name:</label>
      <input type="text" id="name" name="name" disabled value="Mira">

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" disabled value="mirabk@gmail.com">

      <label for="phone">Phone:</label>
      <input type="text" id="phone" name="phone" disabled value="123654789">
      <input type="hidden" id="id_client" name="id_client" value="<?= $id_client ?>">
      <label for="type_reservation">Type of Reservation:</label>
      <select id="type_reservation" name="type_reservation" onchange="updatePrice()">
        <option value="vip">VIP</option>
        <option value="standard">Standard</option>
        <option value="premium">Premium</option>
        <option value="special_event">Special Event</option>
      </select>

      <label for="people">Number of Tickets:</label>
      <input type="number" action="add_reservation.php" method="POST"  id="people" name="num_people" value="1" min="1" onchange="updatePrice()" required>

      <p id="errorMessage" style="color:red; display:none;">No available spots for this reservation.</p>
      
      <p>  <span id="notavailableplace"></span></p>
      <p>Date: <span id="eventDate"></span></p>
      <p>Price: <span id="price"></span> DT</p>
      <input type="hidden" id="price_input" name="price"/>

      <button type="submit" id="submit_button" name="submit" >Submit Reservation</button>
    </form>
</div>
</div>

    <!-- Success Message -->
    <div id="successMessage" style="display:none; text-align:center; font-size:18px; color:#4CAF50;">
        <p>Success! Your reservation has been successfully submitted.</p>
    </div>
</div>

     

        <div class="content-box">
            <div class="projection-grid">
              <!-- Introduction -->
              <div class="grid-item intro">
                <h2>Key Figures of Our Projections</h2>
                <p class="lead">
                  Our commitment to innovation, performance, and outdoor cinematic experiences drives us to excel in organizing cultural events.
                </p>
              </div>
        
              <!-- First Stat Block -->
              <div class="grid-item stat-block">
                <p class="stat-number">500,000 Viewers</p>
                <p>
                  Our viewers have enjoyed unforgettable moments through our unique and immersive film screenings.
                </p>
                <div class="progress-bar-container">
                  <div class="progress-bar" style="width: 80%"></div>
                </div>
              </div>
        
              <!-- Second Stat Block -->
              <div class="grid-item stat-block">
                <p class="stat-number">+300,000</p>
                <p>
                  We're proud to have welcomed over 50,000 happy attendees at our open-air film screenings.
                </p>
                <div class="progress-bar-container">
                  <div class="progress-bar" style="width: 45%"></div>
                </div>
              </div>
        
              <!-- Donut Chart Block -->
              <div class="grid-item chart-block">
                <canvas class="donut-chart"></canvas>
                <p class="chart-percentage">75%</p>
                <p class="chart-description">
                  80% of our audience returns every summer, showing their love for our outdoor screenings.
                </p>
              </div>
            </div>
          </div>

</section>

<div id="chatbot-icon">💬</div>

<!-- Chatbot Container -->
<div id="chatbot-container">
  <div id="chatbot-header">
    <span>Chatbot</span>
    <button id="close-btn">&times;</button>
  </div>
  <div id="chatbot-body">
    <div id="chatbot-messages"></div>
  </div>
  <div id="chatbot-input-container">
    <input type="text" id="chatbot-input" placeholder="Type a message..." />
    <button id="send-btn">Send</button>
  </div>
</div>

<script src="script.js"></script>
</body>
</html>

 
  
  
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement(
            {pageLanguage: 'en'},
            'google_translate_element'
        );
    }
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  
  
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="./js/event.js"></script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatbotIcon = document.getElementById('chatbot-icon');
    const chatbotContainer = document.getElementById('chatbot-container');
    const sendBtn = document.getElementById('send-btn');
    const chatbotBody = document.getElementById('chatbot-body');
    const chatbotInput = document.getElementById('chatbot-input');
    const closeBtn = document.getElementById('close-btn');

    const questionsAnswers = [
        {
            question: "How do I reserve a ticket?",
            answer: "You can reserve your spot by clicking the 'Reserve' button on our homepage or contacting us directly."
        },
        {
            question: "Can I bring my own food?",
            answer: "Yes! You are welcome to bring your own snacks and drinks. 🍿🥤"
        },
        {
            question: "Is there a discount for kids or groups?",
            answer: "Yes, we offer discounts for groups of 5 or more and children under 12."
        },
        {
            question: "Can I cancel my reservation?",
             answer: `You can cancel up to 24 hours before the event for a full refund. Please visit <a href="./historique.php" target="_blank">Historique Page</a>.`
        },
        {
            question: "What should I bring with me?",
            answer: "Bring a blanket or low chair, your ticket (printed or digital), and some snacks for a great experience!"
        },
        {
            question: "What events are available?",  // Special Question
            special: true
        }
    ];

    let remainingQuestions = [...questionsAnswers];
    let answeredQuestions = [];

    chatbotIcon.addEventListener('click', () => {
        chatbotContainer.style.display = 'flex';
        chatbotIcon.style.display = 'none';
        showQuestions();
    });

    closeBtn.addEventListener('click', () => {
        chatbotContainer.style.display = 'none';
        chatbotIcon.style.display = 'flex';
    });

    sendBtn.addEventListener('click', sendMessage);
    chatbotInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        const userInput = chatbotInput.value.trim();
        if (userInput === '') return;

        addMessage(userInput, 'user');
        chatbotInput.value = '';

        setTimeout(() => {
            botResponse(userInput);
        }, 500);
    }

    function addMessage(message, sender) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender === 'user' ? 'user-message' : 'bot-message');
        messageElement.innerHTML = message;
        chatbotBody.appendChild(messageElement);
        chatbotBody.scrollTop = chatbotBody.scrollHeight;
    }

    function clearChatbotBody() {
        chatbotBody.innerHTML = '';
    }

    function showQuestions() {
        clearChatbotBody();
        addMessage("Welcome! How can I help you today?", 'bot');
        updateRemainingQuestionButtons();
    }

    function updateRemainingQuestionButtons() {
        const existingContainer = document.querySelector('.question-container');
        if (existingContainer) {
            existingContainer.remove();
        }

        if (remainingQuestions.length > 0) {
            const otherQuestionsTitle = document.createElement('div');
            otherQuestionsTitle.classList.add('bot-message');
            otherQuestionsTitle.innerHTML = '<strong>Other questions:</strong>';
            chatbotBody.appendChild(otherQuestionsTitle);

            const questionContainer = document.createElement('div');
            questionContainer.classList.add('question-container');

            remainingQuestions.forEach((qa, i) => {
                const button = document.createElement('button');
                button.classList.add('clickable-question');
                button.innerText = qa.question;
                button.addEventListener('click', () => handleQuestionClick(i));
                questionContainer.appendChild(button);
            });

            chatbotBody.appendChild(questionContainer);
        }
    }

    function handleQuestionClick(index) {
    const selectedQA = remainingQuestions[index];

    // Remove from remaining and add to answered
    remainingQuestions.splice(index, 1);
    answeredQuestions.unshift(selectedQA);

    // Show answer
    const answerBlock = document.createElement('div');
    answerBlock.classList.add('bot-message');

    if (selectedQA.special) {
        answerBlock.innerHTML = `<strong>Q: ${selectedQA.question}</strong><br>A: Here are the available events:`;
        chatbotBody.appendChild(answerBlock);
        fetchAllEvents().then(() => {
            updateRemainingQuestionButtons(); // 👈 Call AFTER events are shown
            chatbotBody.scrollTop = chatbotBody.scrollHeight;
        });
    } else {
        answerBlock.innerHTML = `<strong>Q: ${selectedQA.question}</strong><br>A: ${selectedQA.answer}`;
        chatbotBody.appendChild(answerBlock);
        updateRemainingQuestionButtons(); // 👈 Call after appending answer
        chatbotBody.scrollTop = chatbotBody.scrollHeight;
    }
}
function fetchAllEvents() {
    return fetch('listEvents.php')
        .then(response => response.json())
        .then(events => {
            if (events.error) {
                addMessage("Sorry, I couldn't fetch the events. Please try again later.", 'bot');
                console.error(events.error);
                return;
            }

            if (Array.isArray(events) && events.length > 0) {
                events.forEach(event => {
                    let eventDetails = `
                        <h3>${event.name_event}</h3>
                        <p><strong>Date:</strong> ${event.date_event}</p>
                        <p><strong>Film:</strong> ${event.title}</p>
                        <img src="${event.photo_path}" alt="${event.title}" style="width: 200px;" />
                    `;
                    addMessage(eventDetails, 'bot');
                });
            } else {
                addMessage("No events available.", 'bot');
            }
        })
        .catch(error => {
            addMessage("Sorry, I couldn't fetch the events. Please try again later.", 'bot');
            console.error('Error fetching events:', error);
        });
}

    function botResponse(userInput) {
        const match = questionsAnswers.find(q => userInput.toLowerCase().includes(q.question.toLowerCase()));
        if (match) {
            addMessage(`A: ${match.answer}`, 'bot');
        } else {
            addMessage("I'm not sure how to answer that. Try selecting one of the suggested questions!", 'bot');
        }
    }
});










// script.js

/*function toggleDetails(row, btn) {
   const details = btn.parentElement.querySelector('.event-details');
   details.style.display = details.style.display === 'none' ? 'block' : 'none';
   btn.textContent = details.style.display === 'block' ? 'Hide Details' : 'Read More';
}

let currentItem = 3;
const boxes = [...document.querySelectorAll('.box-container .box')];
//const loadMoreBtn = document.getElementById('load-more');
const loadMoreBtn = btn.querySelector('#load-more');
loadMoreBtn.style.display = 'inline-block';

loadMoreBtn.onclick = () => {
  for (let i = currentItem; i < currentItem + 3; i++) {
    if (boxes[i]) {
      boxes[i].style.display = 'inline-block';
    }
  }
  currentItem += 3;

  if (currentItem >= boxes.length) {
    loadMoreBtn.style.display = 'none';
  }
};*/

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const box = this.closest('.box');
      const details = box.querySelector('.event-details');
      const isVisible = details.classList.contains('visible');

      details.classList.toggle('visible');
      this.textContent = isVisible ? 'Read More' : 'Hide Details';
    });
  });
});


</script>


  

  











</html>