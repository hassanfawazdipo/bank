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

// Function to generate a random 10-digit account number
function generate_account_number($conn) {
    $account_number = '';
    $exists = true;

    while ($exists) {
        $account_number = strval(rand(1000000000, 9999999999));
        $sql = "SELECT * FROM users WHERE account_number = '$account_number'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $exists = false;
        }
    }

    return $account_number;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $fullName = sanitize_input($_POST["fullName"]);
    $dob = sanitize_input($_POST["dob"]);
    $email = sanitize_input($_POST["email"]);
    $phone = sanitize_input($_POST["phone"]);
    $address = sanitize_input($_POST["address"]);
    $username = sanitize_input($_POST["username"]);
    $password = sanitize_input($_POST["password"]);
    $confirmPassword = sanitize_input($_POST["confirmPassword"]);

    // Image upload
    $image = null;
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if ($check !== false) {
            if ($_FILES["image"]["size"] <= 10000000) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image = $target_file;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "Sorry, your file is too large.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Error checking
    $errors = [];

    // Check for empty fields
    if (empty($fullName) || empty($dob) || empty($email) || empty($phone) || empty($address) || empty($username) || empty($password) || empty($confirmPassword)) {
        $errors[] = "All fields are required.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate phone number (simple validation)
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors[] = "Invalid phone number.";
    }

    // Check password match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check username for spaces
    if (strpos($username, ' ') !== false) {
        $errors[] = "Username should not contain spaces.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Generate account number
        $account_number = generate_account_number($conn);

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (fullName, dob, email, phone, address, username, password, image, account_number, balance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00)");
        $stmt->bind_param("sssssssss", $fullName, $dob, $email, $phone, $address, $username, $hashedPassword, $image, $account_number);

        if ($stmt->execute()) {
            echo "Registration successful. Your account number is: $account_number";
        } else {
            echo "Error: " . $stmt->error;
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
    <title>Registration Page</title>
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

.registration-form {
    background-color: #ffffff;
    border: 2px solid #0033a0; /* Royal blue */
    border-radius: 10px;
    max-width: 600px;
    width: 100%;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px; /* Margin to ensure it doesn't touch the edges */
}

.registration-form h2 {
    color: #0033a0; /* Royal blue */
    text-align: center;
}

.registration-form label {
    color: #0033a0; /* Royal blue */
    display: block;
    margin-bottom: 5px;
}

.registration-form input[type="text"],
.registration-form input[type="password"],
.registration-form input[type="email"],
.registration-form textarea {
    border: 1px solid #0033a0; /* Royal blue */
    border-radius: 5px;
    padding: 10px;
    width: 100%;
    margin-bottom: 15px;
    box-sizing: border-box;
}

.registration-form button {
    background-color: #0033a0; /* Royal blue */
    border: none;
    border-radius: 5px;
    color: #ffffff;
    cursor: pointer;
    padding: 10px 20px;
    width: 100%;
    font-size: 16px;
}

.registration-form button:hover {
    background-color: #002080; /* Darker shade of royal blue */
}

.checkbox {
    margin-bottom: 15px;
}

.checkbox input[type="checkbox"] {
    margin-right: 5px;
}

.checkbox label a {
    color: #0033a0; /* Royal blue */
    text-decoration: none;
}

.checkbox label a:hover {
    text-decoration: underline;
}

.registration-form p {
    text-align: center;
}

.registration-form p a {
    color: #0033a0; /* Royal blue */
    text-decoration: none;
}

.registration-form p a:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    .registration-form {
        width: 90%;
        padding: 15px;
        margin: 20px auto; /* Center the form and add vertical margins */
    }
}

@media (min-width: 601px) {
    .registration-form {
        margin: 20px auto; /* Center the form and add vertical margins */
    }
}
.profile-image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-image-container img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #0033a0; /* Royal blue border */
        }

        .profile-image-container input[type="file"] {
            margin-top: 10px;
        }


</style>
<script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('imagePreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
<body>
    
    <main>
        <form class="registration-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <h2>Register</h2>

            <div class="profile-image-container">
                <img src="" id="imagePreview" alt=" ">
                <input type="file" id="image" onchange="previewImage(event)" name="image"  accept="image/*">
            </div>

            <label for="fullName">Full Name *</label>
            <input type="text" id="fullName" name="fullName" required>

            <label for="dob">Date of Birth *</label>
            <input type="date" id="dob" name="dob" required>

            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number *</label>
            <input type="text" id="phone" name="phone" required>

            <label for="address">Address *</label>
            <textarea id="address" name="address" rows="4" required></textarea>

            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password *</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <div class="checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="#">terms and conditions</a></label>
            </div>

            <button type="submit">Register</button>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </main>
</body>
</html>
