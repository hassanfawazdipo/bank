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

// Assuming user is logged in and user_id is available in session
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user ID from session

// Fetch user details
$sql = "SELECT fullName, balance, created_at, account_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$account_number = $user['account_number'];

// Fetch transactions for the user's account number
$sql = "SELECT account_number, amount, payment_method, regdate, status FROM transaction WHERE account_number = ? ORDER BY regdate DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $account_number);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            margin-bottom: 30px;
        }

        .main-content .overview {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
            /* Added margin top for spacing */
            margin-bottom: 30px;
        }

        .main-content .account-details .widget {
            margin-bottom: 20px;
        }

        .main-content .widget h3 {
            color: #0033a0;
            /* Royal blue */
        }

        .main-content .responsive-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-content .responsive-table th,
        .main-content .responsive-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .main-content .responsive-table th {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
        }

        .main-content .responsive-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .main-content .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-content .details-table th,
        .main-content .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .main-content .details-table th {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            text-align: left;
        }

        .main-content .quick-actions {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .main-content .action-button {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            margin-bottom: 10px;
        }

        .main-content .action-button a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .main-content .action-button:hover {
            background-color: #002080;
            /* Darker shade of royal blue */
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

        /* Style for Unlock Button */
        .unlock-button {
            background-color: #28a745;
            /* Green */
            color: #ffffff;
            /* White */
            border: none;
            padding: 10px 20px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Modal Style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 100px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #ffffff;
            /* White */
            margin: auto;
            padding: 20px;
            border: 1px solid #0033a0;
            /* Royal Blue */
            width: 80%;
            max-width: 400px;
            position: relative;
            border-radius: 8px;
            text-align: center;
        }

        /* Close Icon */
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #0033a0;
            /* Royal Blue */
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Link Style */
        .modal-content a {
            color: #0033a0;
            /* Royal Blue */
            text-decoration: none;
        }

        .modal-content a:hover {
            text-decoration: underline;
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
        <section class="overview">
            <div class="account-details">
                <div class="widget">
                    <h3>Available Credit</h3>
                    <p>$<?php echo number_format($user['balance'], 2); ?> | <span style="color: red;">locked</span> | <button id="unlockButton" class="unlock-button">Unlock</button></p>
                    <p>Joined: <?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>
                    <div id="myModal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <p>This account has been locked. Chat with the admin: <a href="mailto:admin@gmail.com">admin@gmail.com</a></p>
                        </div>
                    </div>
                </div>

                <div class="widget">
                    <h3>Account Details</h3>
                    <table class="details-table">
                        <tr>
                            <th>Account Number</th>
                            <td><?php echo htmlspecialchars($user['account_number']); ?></td>
                        </tr>
                        <tr>
                            <th>Account Name</th>
                            <td><?php echo htmlspecialchars($user['fullName']); ?></td>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td>$<?php echo number_format($user['balance'], 2); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="widget">
                    <h3>Recent Transactions</h3>
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>Account Number</th>
                                <th>Amount</th>
                                <!-- <th>Payment Method</th> -->
                                <!-- <th>Date</th> -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transactions)) : ?>
                                <?php foreach ($transactions as $transaction) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                                        <td>$<?php echo htmlspecialchars($transaction['amount']); ?></td>
                                        <!-- <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td> -->
                                        <!-- <td><?php echo htmlspecialchars($transaction['regdate']); ?></td> -->
                                        <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">No transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section class="quick-actions">
            <button id="unlockButton1" class="unlock-button">Transfer</button>
            <button id="unlockButton2" class="unlock-button">Deposit</button>
            <button id="unlockButton3" class="unlock-button">Pay Bills</button>

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
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("unlockButton");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("unlockButton1");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("unlockButton2");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("unlockButton3");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>