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

// Handle form submission for user details update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form is for updating user details or updating transaction status
    if (isset($_POST['update_user'])) {
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
            header("Location: admin.php");
            exit;
        } else {
            echo "<p>Error updating details: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } elseif (isset($_POST['update_transaction'])) {
        // Handle transaction status update
        $transaction_id = $_POST['transaction_id'];
        $status = $_POST['status'];

        // Update transaction status in the database
        $sql = "UPDATE transaction SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $transaction_id);

        if ($stmt->execute()) {
            header("Location: admin.php"); // Redirect to the admin page after update
            exit;
        } else {
            echo "<p>Error updating status: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Fetch user details
$sql = "SELECT fullName, dob, email, phone, address, username, image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch recent transactions
$sql_transactions = "SELECT id, amount, status, payment_method FROM transaction ";
$result_transactions = $conn->query($sql_transactions);

// Debug: Check for SQL errors
if ($conn->error) {
    die("Query failed: " . $conn->error);
}

// Fetch all users for management table
$sql_users = "SELECT id, username FROM users";
$result_users = $conn->query($sql_users);

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
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

        .header .logout-button {
            background-color: #ff4747;
            /* Red */
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .header .logout-button:hover {
            background-color: #cc0000;
            /* Darker red */
            transform: scale(1.05);
        }

        .header .logout-button:focus {
            outline: none;
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
            padding-top: 80px;
            /* Added padding top for spacing */
        }

        .section {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            /* Space between sections */
        }

        .section h3 {
            color: #0033a0;
            /* Royal blue */
            margin-bottom: 20px;
        }

        .section table {
            width: 100%;
            border-collapse: collapse;
        }

        .section table,
        .section th,
        .section td {
            border: 1px solid #ddd;
        }

        .section th,
        .section td {
            padding: 10px;
            text-align: left;
        }

        .section th {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
        }

        .section td {
            background-color: #f9f9f9;
        }

        .section form {
            display: flex;
            flex-direction: column;
        }

        .section form label {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .section form input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .section form button {
            background-color: #0033a0;
            /* Royal blue */
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .section form button:hover {
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
        <div class="logo">Bank Admin</div>
        <button class="mobile-nav-toggle" aria-label="Toggle navigation">&#9776;</button>
        <nav class="desktop-nav">
            <a href="#manage-users">Manage Users</a>
            <a href="#recent-transactions">Recent Transactions</a>
            <a href="#settings">Settings</a>
        </nav>
        <nav class="mobile-nav">
            <a href="#manage-users">Manage Users</a>
            <a href="#recent-transactions">Recent Transactions</a>
            <a href="#settings">Settings</a>
        </nav>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>
    <aside class="sidebar">
        <a href="#manage-users">Manage Users</a>
        <a href="#recent-transactions">Recent Transactions</a>
        <a href="#settings">Settings</a>
    </aside>
    <main class="main-content">
        <!-- Manage Users Section -->
        <section id="manage-users" class="section">
            <h3>Manage Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Serial No</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_users->num_rows > 0) {
                        $serial_no = 1; // Initialize serial number
                        // Output data for each row
                        while ($row = $result_users->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $serial_no++ . "</td>"; // Display serial number and increment
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>
                             <a href='view.php?id=" . htmlspecialchars($row['id']) . "'>View</a> |
                            <a href='deposit.php?id=" . htmlspecialchars($row['id']) . "'>Deposit</a> |
                            <a href='edit.php?id=" . htmlspecialchars($row['id']) . "'>Edit</a>
                          </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>


      

        <!-- Recent Transactions Section -->
        <section id="recent-transactions" class="section">
    <h3>Recent Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Payment Mathod</th>
                <th>Status</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_transactions && $result_transactions->num_rows > 0) {
                // Output data for each row
                while ($row = $result_transactions->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>$" . htmlspecialchars(number_format($row['amount'], 2)) . "</td>";
                    echo "<td>";
                    echo "<form method='POST' action='update_status.php'>";
                    echo "<input type='hidden' name='transaction_id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<select name='status' required>";
                    echo "<option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                    echo "<option value='Deposit Successful'" . ($row['status'] == 'Deposit Successful' ? ' selected' : '') . ">Deposit Successful</option>";
                    echo "<option value='Withdrawal Successful'" . ($row['status'] == 'Withdrawal Successful' ? ' selected' : '') . ">Withdrawal Successful</option>";
                    echo "</select>";
                    echo "<input type='submit' value='Confirm'>";
                    echo "</form>";
                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No transactions found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>


        




        <!-- Settings Section -->
        <section id="settings" class="section">
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

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">

                <button type="submit">Save Changes</button>
            </form>
        </section>
    </main>
    <footer class="footer">
        &copy; 2024 Bank Admin Dashboard. All rights reserved.
    </footer>
    <script>
        document.querySelector('.mobile-nav-toggle').addEventListener('click', function() {
            document.querySelector('.mobile-nav').classList.toggle('active');
        });
    </script>
</body>

</html>