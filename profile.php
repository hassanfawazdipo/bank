<?php
session_start();
require 'config.php'; // Include your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$query = "SELECT fullName, dob, email, phone, address, username, image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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

        .main-content .profile-section {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
            /* Added margin top for spacing */
            text-align: center;
        }

        .profile-section h3 {
            color: #0033a0;
            /* Royal blue */
        }

        .profile-section img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .profile-section table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .profile-section table th,
        .profile-section table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .profile-section table th {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
        }

        .profile-section table tr:nth-child(even) {
            background-color: #f2f2f2;
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
        <section class="profile-section">
            <?php if (!empty($user['image'])) : ?>
                <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="User Profile Picture">
            <?php else : ?>
                <img src="default-profile.png" alt="Default Profile Picture">
            <?php endif; ?>
            <h3>Profile Information</h3>
            <table>
                <tbody>
                    <tr>
                        <th>Username:</th>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Full Name:</th>
                        <td><?php echo htmlspecialchars($user['fullName']); ?></td>
                    </tr>
                    <tr>
                        <th>Phone Number:</th>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                    </tr>
                </tbody>
            </table>
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