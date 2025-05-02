<?php
// controllers/forgotPasswordController.php

require_once '../models/User.php';  // Adjust path if necessary
require_once '../config/db.php';
require_once '../config/mail.php';  // You need to set up an email library (like PHPMailer) or use PHP's mail()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $db = new Database();
    $userModel = new User($db->getConnection());

    $user = $userModel->findByEmailStatic($email);

    if ($user) {
        // Generate a unique token for the password reset
        $token = bin2hex(random_bytes(50));
        $expires = date('U') + 3600; // Token expires in 1 hour

        // Store the token in the database (you can add a `reset_token` and `reset_token_expiry` fields in the `users` table)
        $stmt = $db->getConnection()->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        // Send reset email (make sure to set up your email configuration)
        $resetLink = "http://yourdomain.com/reset-password.php?token=$token";

        $subject = 'Password Reset Request';
        $message = "Click the following link to reset your password: $resetLink";
        $headers = 'From: no-reply@movievibe.com' . "\r\n" . 'Reply-To: no-reply@movievibe.com' . "\r\n";

        if (mail($email, $subject, $message, $headers)) {
            echo "A password reset link has been sent to your email.";
        } else {
            echo "There was an error sending the reset email. Please try again.";
        }
    } else {
        echo "This email is not registered.";
    }
}
?>
