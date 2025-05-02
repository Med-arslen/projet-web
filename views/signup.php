<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MovieVibe</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <div class="header-container">
            <a href="page.html"><img src="../assets/images/logo.png" alt="MovieVibe Logo" class="logo"></a>
        </div>
    </header>

    <div class="signup-container">
        <h2>Create Account</h2>
        <form method="POST" action="../controllers/signupController.php" class="signup-form">
    <input type="text" name="name" placeholder="Full name" required class="input-field" />
    <input type="email" name="email" placeholder="Email" required class="input-field" />
    <input type="password" name="password" placeholder="Password" required class="input-field" />
    <input type="text" name="phone" placeholder="Phone Number" class="input-field" />
    <input type="hidden" name="admin_id" value="0" />

    <select name="role" required class="input-field">
        <option value="" disabled selected>Choose a role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit" class="submit-btn">Sign Up</button>
</form>

        <div class="footer">
            <p>Already have an account? <a href="login.php" class="link">Sign in</a></p>
        </div>
    </div>

    <style>
        /* General page layout */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('cinema.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        header {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        .header-container .logo {
            max-width: 200px;
        }

        /* Signup container */
        .signup-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .signup-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .signup-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Form field styles */
        .input-field {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #444;
            border-radius: 5px;
            background-color: #222;
            color: white;
            outline: none;
            transition: border 0.3s ease-in-out;
        }

        .input-field:focus {
            border-color: #e50914;
        }

        /* Button styling */
        .submit-btn {
            background-color: #e50914;
            padding: 15px;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #b20710;
        }

        /* Footer and links */
        .footer {
            margin-top: 15px;
            font-size: 14px;
        }

        .link {
            color: #e50914;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .signup-container {
                padding: 20px 15px;
            }

            .input-field {
                font-size: 14px;
            }

            .submit-btn {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</body>
</html>
