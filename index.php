<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Landing Page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Basic reset and global styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }

        /* Container styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header styles */
        header {
            background-color: #0033a0;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        /* Hero section styles */
        .hero {
            background: url('images/about_2.jpg') no-repeat center center/cover;
            color: #fff;
            padding: 60px 20px;
            text-align: center;
            position: relative;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay for better text readability */
            z-index: 1;
        }

        .hero .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h2 {
            font-size: 2.5em;
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            color: #fff;
            background-color: #0033a0;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #103da0;
        }

        /* Main content styles */
        .content {
            text-align: center;
            padding: 40px 20px;
        }

        .content h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .content p {
            margin-bottom: 20px;
            font-size: 1em;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .feature {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            flex: 1 1 300px;
            text-align: center;
        }

        .feature h4 {
            margin: 0 0 10px;
            font-size: 1.2em;
        }

        /* Form styles */
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-container h3 {
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-container input[type="text"],
        .form-container input[type="password"],
        .form-container input[type="email"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #0033a0;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container input[type="submit"]:hover {
            background-color: #103da0;
        }

        /* Footer styles */
        footer {
            background-color: #0033a0;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        footer p {
            margin: 0;
            font-size: 0.9em;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .features {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .hero h2 {
                font-size: 1.5em;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.9em;
            }

            .feature {
                padding: 15px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Bank Name</h1>
        </div>
    </header>

    <div class="hero">
        <div class="hero-content">
            <h2>Welcome to Our Bank</h2>
            <p>Your secure place for banking needs.</p>
            <a href="registeration.php" class="btn">Register</a>
            <a href="login.php" class="btn">Login</a>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <h3>Why Choose Us?</h3>
            <p>We offer a range of banking services designed to meet your needs.</p>

            <div class="features">
                <div class="feature">
                    <h4>Secure Transactions</h4>
                    <p>Enjoy peace of mind with our advanced security measures.</p>
                </div>
                <div class="feature">
                    <h4>24/7 Support</h4>
                    <p>Our support team is here for you around the clock.</p>
                </div>
                <div class="feature">
                    <h4>Easy Online Banking</h4>
                    <p>Access your account anytime, anywhere with our easy-to-use online portal.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Bank Name. All rights reserved.</p>
    </footer>
</body>
</html>
