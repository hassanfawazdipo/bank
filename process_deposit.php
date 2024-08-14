<?php
// Database connection details
$host = 'localhost';
$db = 'bank';
$user = 'root'; // Update with your database username
$pass = ''; // Update with your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Retrieve form data
$account_number = $_POST['account_number'];
$amount = $_POST['amount'];
$payment_method = $_POST['payment_method'];
$regdate = date('Y-m-d H:i:s'); // Current timestamp

// Start a transaction
$pdo->beginTransaction();

try {
    // Insert deposit transaction into the database
    $stmt = $pdo->prepare("INSERT INTO transaction (account_number, amount, payment_method, regdate, status) 
                            VALUES (:account_number, :amount, :payment_method, :regdate, 'Pending')");
    $stmt->execute([
        ':account_number' => $account_number,
        ':amount' => $amount,
        ':payment_method' => $payment_method,
        ':regdate' => $regdate
    ]);

    // Update user balance
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE account_number = :account_number");
    $stmt->execute([
        ':amount' => $amount,
        ':account_number' => $account_number
    ]);

    // Commit the transaction
    $pdo->commit();
    header('Location: admin.php'); // Redirect to home page after success
    exit;
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $pdo->rollBack();
    die("Failed to process deposit: " . $e->getMessage());
}
?>
