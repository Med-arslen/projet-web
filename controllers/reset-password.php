<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=films', 'root', ''); // Remplacez par vos informations de connexion

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Vérification de l'email dans la base de données
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // L'email est valide, affichage du formulaire pour réinitialiser le mot de passe
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
            $password = $_POST['password']; // Nouveau mot de passe
            // Hacher le mot de passe
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Mise à jour du mot de passe dans la base de données
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->execute([$passwordHash, $email]);

            echo "Votre mot de passe a été réinitialisé avec succès.";
        }
    } else {
        echo "Aucun compte trouvé avec cet email.";
    }
}
?>
