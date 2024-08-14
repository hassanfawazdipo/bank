<?php
// Database connection
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bank"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from query string
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .header img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }

        .header h1 {
            margin: 0;
            color: #0033a0; /* Royal blue */
        }

        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #0033a0; /* Royal blue */
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .back-button:hover {
            background-color: #002080; /* Darker shade of royal blue */
            transform: scale(1.05);
        }

        .details {
            margin-top: 20px;
        }

        .details h2 {
            color: #0033a0; /* Royal blue */
            margin-bottom: 15px;
        }

        .details p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
            color: #333;
        }

        .details p label {
            font-weight: bold;
        }

        .button-container {
            margin-top: 20px;
            text-align: right;
        }

        .button-container a {
            background-color: #0033a0; /* Royal blue */
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-container a:hover {
            background-color: #002080; /* Darker shade of royal blue */
            transform: scale(1.05);
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
                margin: 10px;
            }

            .header img {
                width: 80px;
                height: 80px;
            }

            .details h2 {
                font-size: 18px;
            }

            .details p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php" class="back-button">Go Back</a>
        <div class="header">
            <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="User Image">
            <h1><?php echo htmlspecialchars($user['fullName']); ?></h1>
        </div>
        <div class="details">
            <h2>User Details</h2>
            <p><label>Username:</label> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><label>Email:</label> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><label>Full Name:</label> <?php echo htmlspecialchars($user['fullName']); ?></p>
            <p><label>Phone Number:</label> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><label>Address:</label> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><label>Status:</label> <?php echo htmlspecialchars($user['status']); // Assuming status field exists ?></p>
        </div>
    </div>
</body>
</html>
