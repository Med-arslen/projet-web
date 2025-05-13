<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controller/FilmController.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $message = strtolower(trim($input['message'] ?? ''));

        $filmController = new FilmController($pdo);
        $films = $filmController->getAllFilms();

        $responses = [
            'bonjour' => [
                'fr' => 'Bonjour! Comment puis-je vous aider?',
                'en' => 'Hello! How can I assist you?'
            ],
            'merci' => [
                'fr' => 'Avec plaisir! Si vous avez d\'autres questions, n\'hésitez pas.',
                'en' => 'You\'re welcome! If you have more questions, feel free to ask.'
            ],
            'aide' => [
                'fr' => 'Vous pouvez me demander des informations sur un film en mentionnant son titre, ou poser des questions comme "Quels sont les films disponibles?" ou "Quel est le genre du film [titre] ?".',
                'en' => 'You can ask me for information about a film by mentioning its title, or ask questions like "What films are available?" or "What is the genre of the film [title] ?".'
            ],
            'films disponibles' => [
                'fr' => function () use ($films) {
                    $titles = array_map(fn($film) => $film->getTitre(), $films);
                    return 'Les films disponibles sont : ' . implode(', ', $titles) . '.';
                },
                'en' => function () use ($films) {
                    $titles = array_map(fn($film) => $film->getTitre(), $films);
                    return 'The available films are: ' . implode(', ', $titles) . '.';
                }
            ],
            'retour' => [
                'fr' => '<a href="../../View/backoffice/index.php" class="btn btn-primary">Retour à l\'index</a>',
                'en' => '<a href="../../View/backoffice/index.php" class="btn btn-primary">Return to Index</a>'
            ]
        ];

        // Function to calculate similarity
        function getBestMatch($message, $phrases) {
            $bestMatch = null;
            $highestSimilarity = 0;

            foreach ($phrases as $phrase => $response) {
                similar_text($message, $phrase, $similarity);
                if ($similarity > $highestSimilarity) {
                    $highestSimilarity = $similarity;
                    $bestMatch = $phrase;
                }
            }

            return $highestSimilarity >= 70 ? $bestMatch : null;
        }

        // Detect language
        $language = preg_match('/[a-z]/i', $message) ? 'en' : 'fr';

        // Check if the user is asking about a specific film
        foreach ($films as $film) {
            if (strpos($message, strtolower($film->getTitre())) !== false) {
                $response = "Détails du film \"{$film->getTitre()}\":\n" .
                    "- Genre: {$film->getGenre()}\n" .
                    "- Année de sortie: {$film->getAnneeSortie()}\n" .
                    "- Durée: {$film->getDuree()}\n" .
                    "- Âge recommandé: {$film->getAgeRecommande()}+\n" .
                    "- Photo: " . ($film->getPhoto() ? "Disponible ({$film->getPhoto()})" : "Pas de photo disponible");
                echo json_encode(['response' => $response]);
                exit;
            }
        }

        // Find the best match
        $bestMatch = getBestMatch($message, $responses);

        if ($bestMatch && isset($responses[$bestMatch][$language])) {
            $response = is_callable($responses[$bestMatch][$language])
                ? $responses[$bestMatch][$language]()
                : $responses[$bestMatch][$language];
        } else {
            $response = $language === 'fr'
                ? 'Je suis désolé, je ne comprends pas votre question. Essayez de poser une question comme : "Quel est le genre du film [titre] ?" ou "Quels sont les films disponibles ?".'
                : 'I\'m sorry, I don\'t understand your question. Try asking something like: "What is the genre of the film [title]?" or "What films are available?".';
        }

        echo json_encode(['response' => $response]);
        exit;
    }
} catch (Exception $e) {
    error_log("Error in ChatbotHandler: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['response' => "Une erreur interne s'est produite. Veuillez réessayer plus tard."]);
    exit;
}

http_response_code(400);
echo json_encode(['response' => 'Invalid request.']);
