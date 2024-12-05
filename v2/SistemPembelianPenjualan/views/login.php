<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400&family=Open+Sans:wght@400&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            background: url('purple.jpg') no-repeat center center fixed;
            /* Replace with your image path */
            background-size: cover;
            /* Make sure the image covers the whole background */
            color: #fff;
        }

        /* Main Login Container */
        .login-container {
            background: rgba(155, 89, 182, 0.4);
            /* Semi-transparent purple background */
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            /* Increased width for a bigger container */
            text-align: center;
            backdrop-filter: blur(15px);
            /* Smooth blur for the background */
        }

        /* Styling for WELCOME Title */
        .login-container h3 {
            margin-bottom: 10px;
            font-family: 'Lora', serif;
            /* Elegan dan klasik */
            font-size: 2rem;
        }

        /* Styling for Login to your account Text */
        .login-container p:first-of-type {
            font-family: 'Lora', serif;
            /* Konsisten dengan WELCOME */
            font-size: 1rem;
            margin-bottom: 25px;
        }

        /* Form Group Styling */
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 50px;
            /* Larger padding for bigger input fields */
            border: 2px solid transparent;
            /* Transparent border */
            border-radius: 30px;
            background: linear-gradient(to right, #9b59b6, #8e44ad);
            /* Gradient purple background */
            color: #fff;
            outline: none;
            font-size: 1rem;
            /* Larger font for inputs */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:hover {
            border-color: #fff;
            /* White border on hover */
        }

        .form-group input:focus {
            box-shadow: 0 0 10px rgba(155, 89, 182, 0.7);
            /* Purple glow effect */
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.8);
            /* Light white for placeholder text */
        }

        .form-group .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.5rem;
            /* Bigger icon size */
        }

        /* Button Styling */
        .btn-primary {
            width: 100%;
            border: none;
            border-radius: 30px;
            padding: 15px;
            font-size: 1rem;
            background: linear-gradient(45deg, #ff758c, #844685);
            /* Gradient Pink to Purple */
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #844685, #ff758c);
            /* Hover Effect */
            transform: translateY(-2px);
            /* Subtle lift effect */
        }

        /* Footer Styling */
        .footer-text {
            margin-top: 20px;
            font-family: 'Open Sans', sans-serif;
            /* Bersih dan modern */
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .footer-text a {
            color: #fff;
            text-decoration: underline;
        }

        .footer-text a:hover {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h3>WELCOME</h3>
        <p>Login to your account</p>
        <form action="../process/login.php" method="POST">
            <div class="form-group">
                <span class="icon">&#128100;</span>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <span class="icon">&#128274;</span>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>
        <p class="footer-text">Don't you have an account? <a href="registrasi.php">Sign up</a></p>
    </div>
</body>

</html>