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

// Start session and get the logged-in user's ID
session_start();
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = $_POST['full-name'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $username = $_POST['username'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_error = $_FILES['image']['error'];
    $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    $allowed_exts = array('jpg', 'jpeg', 'png', 'gif');
    $max_size = 10 * 1024 * 1024; // 10 MB

    if ($image_error === 0) {
        if (in_array($image_ext, $allowed_exts) && $image_size <= $max_size) {
            $image_path = 'uploads/' . uniqid('', true) . '.' . $image_ext;
            move_uploaded_file($image_tmp, $image_path);

            // Update user details in the database
            $sql = "UPDATE users SET fullName = ?, dob = ?, email = ?, phone = ?, address = ?, username = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $full_name, $dob, $email, $phone, $address, $username, $image_path, $user_id);
        } else {
            echo "<p>Invalid file type or size. Please upload an image (JPG, JPEG, PNG, GIF) up to 10MB.</p>";
            exit;
        }
    } else {
        // No file uploaded, just update other details
        $sql = "UPDATE users SET fullName = ?, dob = ?, email = ?, phone = ?, address = ?, username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $full_name, $dob, $email, $phone, $address, $username, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: home.php");
        exit;
    } else {
        echo "<p>Error updating details: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch user details
$sql = "SELECT fullName, dob, email, phone, address, username, image FROM users WHERE id = ?";
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
    <title>Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header .desktop-nav a {
            color: #ffffff;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            margin-right: 30px;
        }

        .header .desktop-nav a:hover {
            text-decoration: underline;
        }

        .header .mobile-nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: #ffffff;
            cursor: pointer;
        }

        .header .mobile-nav {
            display: none;
            position: absolute;
            top: 60px;
            /* Adjust based on header height */
            left: 0;
            background-color: #0033a0;
            /* Royal blue */
            width: 100%;
            padding: 10px;
        }

        .header .mobile-nav a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
        }

        .header .mobile-nav a:hover {
            background-color: #002080;
            /* Darker shade of royal blue */
            border-radius: 5px;
        }

        .header .mobile-nav.active {
            display: block;
        }

        .sidebar {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            width: 250px;
            position: fixed;
            top: 60px;
            /* Adjust based on header height */
            bottom: 0;
            overflow-y: auto;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 15px;
            margin: 5px 0;
        }

        .sidebar a:hover {
            background-color: #002080;
            /* Darker shade of royal blue */
            border-radius: 5px;
        }

        .main-content {
            margin-left: 250px;
            /* Width of the sidebar */
            padding: 20px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .main-content .settings-section {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
            /* Added margin top for spacing */
        }

        .settings-section h3 {
            color: #0033a0;
            /* Royal blue */
        }

        .settings-section form {
            display: flex;
            flex-direction: column;
        }

        .settings-section label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .settings-section input[type="text"],
        .settings-section input[type="email"],
        .settings-section input[type="password"],
        .settings-section select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .settings-section button {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .settings-section button:hover {
            background-color: #002080;
            /* Darker shade of royal blue */
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
            border: 4px solid #0033a0;
            /* Royal blue border */
        }

        .profile-image-container input[type="file"] {
            margin-top: 10px;
        }

        .footer {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .header .desktop-nav {
                display: none;
            }

            .header .mobile-nav-toggle {
                display: block;
                margin-right: 30px;
            }

            .sidebar {
                width: 100%;
                position: relative;
                top: 0;
                bottom: auto;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .sidebar a {
                padding: 10px;
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 600px) {
            .header .mobile-nav.active {
                display: block;
            }

            .sidebar {
                display: none;
                /* Hide sidebar on very small screens */
            }
        }
    </style>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('imagePreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>

<body>
    <header class="header">
        <div class="logo">Bank Logo</div>
        <button class="mobile-nav-toggle" aria-label="Toggle navigation">&#9776;</button>
        <nav class="desktop-nav">
            <a href="home.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="setting.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>
        <nav class="mobile-nav">
            <a href="home.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="setting.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <aside class="sidebar">
        <a href="home.php">Overview</a>
        <a href="profile.php">Profile</a>
        <a href="setting.php">Settings</a>
        <a href="logout.php">Logout</a>
    </aside>
    <main class="main-content">
        <section class="settings-section">

            <h3>Account Settings</h3>
            <form method="POST" enctype="multipart/form-data" action="">
                <div class="profile-image-container">
                    <img src="<?php echo htmlspecialchars($user['image']); ?>" id="imagePreview" alt="Profile Image">
                    <input type="file" id="image" onchange="previewImage(event)" name="image" accept="image/*">
                </div>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">

                <label for="full-name">Full Name:</label>
                <input type="text" id="full-name" name="full-name" value="<?php echo htmlspecialchars($user['fullName']); ?>">

                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
                <br>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">

                <button type="submit">Save Changes</button>
            </form>
        </section>
    </main>
    <footer class="footer">
        <p>&copy; 2024 Bank. All rights reserved.</p>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
            const mobileNav = document.querySelector('.mobile-nav');

            mobileNavToggle.addEventListener('click', function() {
                mobileNav.classList.toggle('active');
            });
        });
    </script>
</body>

</html>