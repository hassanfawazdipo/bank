<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Funds</title>
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
            max-width: 600px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #0033a0; /* Royal blue */
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group input[type="number"] {
            -moz-appearance: textfield;
        }

        .form-group input[type="number"]::-webkit-inner-spin-button,
        .form-group input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            background-color: #0033a0; /* Royal blue */
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-container button:hover {
            background-color: #002080; /* Darker shade of royal blue */
            transform: scale(1.05);
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
                margin: 10px;
            }

            .header h1 {
                font-size: 24px;
            }

            .form-group input, .form-group select {
                padding: 8px;
            }

            .button-container button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Deposit Funds</h1>
        </div>
        <form method="POST" action="process_deposit.php">
            <div class="form-group">
                <label for="account-number">Account Number:</label>
                <input type="text" id="account-number" name="account_number" placeholder="Enter your account number" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount to Deposit:</label>
                <input type="number" id="amount" name="amount" placeholder="Enter deposit amount" min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
            <label for="payment_method">Payment Method:</label>
                <input type="text" id="amount" name="payment_method"  value="Bank Deposit" readonly min="0.01" step="0.01" required>
            </div>
            <div class="button-container">
                <button type="submit">Deposit</button>
            </div>
        </form>
    </div>
</body>''
</html>
