<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';

$filmController = new FilmController($pdo);
$films = $filmController->getAllFilms();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chatbot - MovieVibe</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      background-color: var(--background);
      color: var(--light);
      font-family: 'Netflix Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .chatbot-container {
      width: 100%;
      max-width: 600px;
      background: var(--surface);
      padding: 20px;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .chatbot-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .chatbot-header h1 {
      color: var(--primary);
    }

    .chatbot-messages {
      height: 300px;
      overflow-y: auto;
      background: rgba(255, 255, 255, 0.05);
      padding: 10px;
      border-radius: var(--border-radius);
      margin-bottom: 20px;
    }

    .chatbot-messages .message {
      margin-bottom: 10px;
    }

    .chatbot-messages .message.user {
      text-align: right;
      color: var(--primary);
    }

    .chatbot-messages .message.bot {
      text-align: left;
      color: var(--light);
    }

    .chatbot-input {
      display: flex;
      gap: 10px;
    }

    .chatbot-input input {
      flex: 1;
      padding: 10px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--border-radius);
      background: rgba(255, 255, 255, 0.05);
      color: var(--light);
    }

    .chatbot-input button {
      background-color: var(--primary);
      color: var(--light);
      border: none;
      padding: 10px 20px;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
    }

    .chatbot-input button:hover {
      background-color: #f40612;
    }
  </style>
</head>
<body>
  <div class="chatbot-container">
    <div class="chatbot-header">
      <h1>Chatbot</h1>
      <p>Posez une question sur un film</p>
    </div>
    <div class="chatbot-messages" id="chatbotMessages"></div>
    <div class="chatbot-input">
      <input type="text" id="chatbotInput" placeholder="Posez votre question...">
      <button onclick="sendMessage()">Envoyer</button>
    </div>
  </div>

  <script>
    const messagesContainer = document.getElementById('chatbotMessages');
    const inputField = document.getElementById('chatbotInput');

    inputField.addEventListener('keypress', function (event) {
      if (event.key === 'Enter') {
        sendMessage();
      }
    });

    function sendMessage() {
      const userMessage = inputField.value.trim();
      if (!userMessage) return;

      addMessage(userMessage, 'user');
      inputField.value = '';

      fetch('../../Handler/ChatbotHandler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userMessage })
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => addMessage(data.response, 'bot'))
        .catch(error => {
          console.error('Error:', error);
          addMessage("Une erreur s'est produite. Veuillez réessayer.", 'bot');
        });
    }

    function addMessage(message, sender) {
      const messageDiv = document.createElement('div');
      messageDiv.className = `message ${sender}`;
      
      // Check if the message contains HTML (e.g., a button)
      if (message.includes('<a')) {
        messageDiv.innerHTML = message; // Use innerHTML for HTML content
      } else {
        messageDiv.textContent = message; // Use textContent for plain text
      }
      
      messagesContainer.appendChild(messageDiv);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
  </script>
</body>
</html>
