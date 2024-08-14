<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
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

$conn->close();
?>
