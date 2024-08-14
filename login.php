<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);

    // Error checking
    $errors = [];

    // Check for empty fields
    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // If no errors, proceed with login
    if (empty($errors)) {
        // Prepare and bind
        $stmt = $conn->prepare("SELECT id, username, password, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if email is registered
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashedPassword, $status);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashedPassword)) {
                // Set session variables
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["status"] = $status;

                // Redirect based on user status
                if ($status == "user") {
                    header("Location: home.php");
                } elseif ($status == "admin") {
                    header("Location: admin.php");
                }
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "Email not registered.";
        }

        $stmt->close();
    }

    // Display errors
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.header {
    background-color: #0033a0; /* Royal blue */
    color: #ffffff;
    padding: 10px;
    text-align: center;
}

.header .logo {
    font-size: 24px;
    font-weight: bold;
}

.header nav a {
    color: #ffffff;
    text-decoration: none;
    margin: 0 10px;
    font-weight: bold;
}

.header nav a:hover {
    text-decoration: underline;
}

main {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 80px); /* Adjust based on header height */
    margin: 20px 0; /* Margin at the top and bottom */
}

.login-form {
    background-color: #ffffff;
    border: 2px solid #0033a0; /* Royal blue */
    border-radius: 10px;
    max-width: 400px;
    width: 100%;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px; /* Margin to ensure it doesn't touch the edges */
}

.login-form h2 {
    color: #0033a0; /* Royal blue */
    text-align: center;
}

.login-form label {
    color: #0033a0; /* Royal blue */
    display: block;
    margin-bottom: 5px;
}

.login-form input[type="email"],
.login-form input[type="password"] {
    border: 1px solid #0033a0; /* Royal blue */
    border-radius: 5px;
    padding: 10px;
    width: 100%;
    margin-bottom: 15px;
    box-sizing: border-box;
}

.login-form button {
    background-color: #0033a0; /* Royal blue */
    border: none;
    border-radius: 5px;
    color: #ffffff;
    cursor: pointer;
    padding: 10px 20px;
    width: 100%;
    font-size: 16px;
}

.login-form button:hover {
    background-color: #002080; /* Darker shade of royal blue */
}

.login-form p {
    text-align: center;
}

.login-form p a {
    color: #0033a0; /* Royal blue */
    text-decoration: none;
}

.login-form p a:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    .login-form {
        width: 90%;
        padding: 15px;
        margin: 20px auto; /* Center the form and add vertical margins */
    }
}

@media (min-width: 601px) {
    .login-form {
        margin: 20px auto; /* Center the form and add vertical margins */
    }
}

</style>
<body>
    
    <main>
        <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Login</h2>
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>

            <p>Don't have an account? <a href="registeration.php">Register here</a></p>
            <p><a href="#">Forgot Password?</a></p>
        </form>
    </main>
</body>
</html>
