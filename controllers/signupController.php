<?php
// controllers/signupController.php

require_once '../models/User.php';
require_once '../config/Database.php'; // Assuming you have a database config file

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $db = new Database();
    $userModel = new User($db->getConnection());

    // Check if email already exists
    if ($userModel->findByEmailStatic($email)) {
        echo "This email is already registered.";
    } else {
        // Create new user
          
        // Use positional arguments instead of named arguments
        if ($userModel->create($admin_id = 1, $name, $email, $password)) { // Admin ID is 1 as an example
            echo "Account created successfully.";
            // Redirect to login page or home page
            header("Location: ../views/login.php");
        } else {
            echo "There was an error. Please try again.";
        }
    }
}
