<?php
session_start(); // Start the session for managing error or success messages

// Database connection
$host = 'localhost';
$db = 'electricity_billing';
$username = 'root';  // Default XAMPP MySQL username
$password = '';  // Default XAMPP MySQL password (empty)
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header('Location: final_bill.php');
    exit();
}

$results = [];
$error = null;

// Retrieve selected customer data when "Select Customer" is clicked
if (isset($_POST['select_customer'])) {
    $customer_id = $_POST['customer_id'];
    $selected_date = $_POST['selected_date'];  // The full date selected by the user (YYYY-MM-DD)

    // Step 1: Query the current_monthbill table for the selected customer and date
    $stmt1 = $pdo->prepare("SELECT * FROM current_monthbill WHERE customer_id = ? AND date = ?");
    $stmt1->execute([$customer_id, $selected_date]);
    $customer_bill_data = $stmt1->fetch(PDO::FETCH_ASSOC);

    // Step 2: Query the customer_payment table for the outstanding balance
    $stmt2 = $pdo->prepare("SELECT * FROM customer_payments WHERE customer_id = ? AND date =?");
    $stmt2->execute([$customer_id, $selected_date]);
    $payment_data = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Debugging: Output the customer data fetched from both tables
    echo '<pre>';
    print_r($customer_bill_data);  // Check the fetched bill data
    print_r($payment_data);        // Check the fetched payment data
    echo '</pre>';

    // Check if we got the data from both tables
    if ($customer_bill_data && $payment_data) {
        // Populate the session with both bill and payment data
        $_SESSION['selected_customer'] = $customer_bill_data;
        $_SESSION['selected_customer']['outstanding_balance'] = $payment_data['outstanding']; // Corrected column name
        $_SESSION['selected_customer']['customer_id'] = $customer_id;
        $_SESSION['selected_customer']['date'] = $selected_date;  // Store the full date
    } else {
        $_SESSION['error'] = "Customer ID or date not found.";
    }

    header('Location: final_bill.php');
    exit();
}

// Calculate the final bill when "Calculate Bill" is clicked
if (isset($_POST['calculate_bill'])) {
    // Check if the session data for selected_customer exists before accessing it
    if (isset($_SESSION['selected_customer'])) {
        $customer_data = $_SESSION['selected_customer'];

        // Debugging: Output the session data for inspection
        echo '<pre>';
        print_r($customer_data);  // Check the session data
        echo '</pre>';

        // Safely access the keys in session data with a check for existence
        $current_month_bill = isset($customer_data['bill_amount']) ? $customer_data['bill_amount'] : 0;
        $outstanding_balance = isset($customer_data['outstanding_balance']) ? $customer_data['outstanding_balance'] : 0;
    } else {
        // Default values or error handling
        $current_month_bill = 0;
        $outstanding_balance = 0;
        $_SESSION['error'] = "No customer data found in session.";
    }

    // Calculate the final bill with and without outstanding balance
    $final_bill_with_outstanding = $current_month_bill + $outstanding_balance;
    $final_bill_without_outstanding = $current_month_bill;

    // Store the bill data in session
    $_SESSION['final_bill'] = [
        'customer_id' => $_SESSION['selected_customer']['customer_id'],
        'date' => $_SESSION['selected_customer']['date'],  // Store full date here
        'current_month_bill' => $current_month_bill,
        'outstanding_balance' => $outstanding_balance,
        'final_bill_with_outstanding' => $final_bill_with_outstanding,
        'final_bill_without_outstanding' => $final_bill_without_outstanding
    ];

    $_SESSION['success_message'] = "Bill calculated successfully.";
    header('Location: final_bill.php');
    exit();
}

// Send the final bill data to the manager and store it in the "outstanding" table
if (isset($_POST['send_to_manager'])) {
    if (isset($_SESSION['final_bill'])) {
        $final_bill_data = $_SESSION['final_bill'];

        // Save the final bill data to the "outstanding" table
        $stmt = $pdo->prepare("INSERT INTO outstanding (customer_id, date, current_month_bill, outstanding_balance, final_bill_with_outstanding, final_bill_without_outstanding) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $final_bill_data['customer_id'],
            $final_bill_data['date'],  // Store full date
            $final_bill_data['current_month_bill'],
            $final_bill_data['outstanding_balance'],
            $final_bill_data['final_bill_with_outstanding'],
            $final_bill_data['final_bill_without_outstanding']
        ]);

        $_SESSION['message'] = "Final bill data sent to the Manager successfully.";
        unset($_SESSION['final_bill']); // Clear the final bill data from session
        unset($_SESSION['selected_customer']); // Clear selected customer data

        header('Location: final_bill.php');
        exit();
    } else {
        $_SESSION['error'] = "No final bill data available to send.";
        header('Location: final_bill.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Bill Calculation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .jumbotron {
            background: linear-gradient(90deg, rgba(0, 123, 255, 0.8), rgb(0, 86, 179));
            color: white;
            padding: 50px 20px;
        }
        .jumbotron h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .jumbotron p {
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .list-group {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .list-group-item {
            padding: 15px;
            text-align: left;
        }
        .receipt {
            border: 1px solid #007bff;
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }
        .receipt h5 {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }
        .receipt p {
            margin: 5px 0;
            font-size: 1.1rem;
        }
        .btn-success {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Electricity Billing System</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Calculation</a></li>
                <li class="nav-item"><a class="nav-link" href="Check_outstanding.php">Check Outstanding</a></li>
                <li class="nav-item"><a class="nav-link" href="discount.php">Discount and Usage Management</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron text-center">
        <h1 class="display-4">Final Bill Calculation</h1>
        <p class="lead">Process electricity bills with and without outstanding balances</p>
    </div>

    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info" role="alert">
                <?= htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="customer_id">Select Customer ID:</label>
                <input list="customer_ids" name="customer_id" class="form-control" required>
                <datalist id="customer_ids">
                    <?php
                    // Get all customer IDs from the database
                    $stmt = $pdo->query("SELECT customer_id FROM customers");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['customer_id']) . "'>";
                    }
                    ?>
                </datalist>
            </div>
            <div class="form-group">
                <label for="selected_date">Select Billing Date:</label>
                <input type="date" name="selected_date" class="form-control" required>
            </div>
            <button type="submit" name="select_customer" class="btn btn-primary">Retrieve Data</button>
        </form>

        <?php if (isset($_SESSION['selected_customer'])): ?>
            <div class="mt-4">
                <h5>Customer Details</h5>
                <ul class="list-group">
                    <li class="list-group-item">Customer ID: <?= htmlspecialchars($_SESSION['selected_customer']['customer_id']); ?></li>
                    <li class="list-group-item">Billing Date: <?= htmlspecialchars($_SESSION['selected_customer']['date']); ?></li>
                    <li class="list-group-item">Current Month's Bill: RM<?= htmlspecialchars($_SESSION['selected_customer']['bill_amount']); ?></li>
                    <li class="list-group-item">Outstanding Balance: RM<?= htmlspecialchars($_SESSION['selected_customer']['outstanding_balance']); ?></li>
                </ul>

                <form method="post" action="" class="mt-3">
                    <button type="submit" name="calculate_bill" class="btn btn-primary mb-4">Generate Receipt</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['final_bill'])): ?>
        <div class="mt-4 receipt">
            <h5>Receipt</h5>
            <p><strong>Customer ID:</strong> <?= htmlspecialchars($_SESSION['final_bill']['customer_id']); ?></p>
            <p><strong>Billing Date:</strong> <?= htmlspecialchars($_SESSION['final_bill']['date']); ?></p>
            <p><strong>Current Month's Bill:</strong> RM<?= htmlspecialchars($_SESSION['final_bill']['current_month_bill']); ?></p>
            <p><strong>Outstanding Balance:</strong> RM<?= htmlspecialchars($_SESSION['final_bill']['outstanding_balance']); ?></p>
            <p><strong>Final Bill (With Outstanding):</strong> RM<?= htmlspecialchars($_SESSION['final_bill']['final_bill_with_outstanding']); ?></p>
            <p><strong>Final Bill (Without Outstanding):</strong> RM<?= htmlspecialchars($_SESSION['final_bill']['final_bill_without_outstanding']); ?></p>
        </div>
        <form method="post" action="" class="mt-3 text-center">
            <button type="submit" name="send_to_manager" class="btn btn-success mb-4">Send to Manager</button>
        </form>
        <?php endif; ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
