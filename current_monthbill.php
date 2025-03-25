<?php
session_start(); // Start the session for managing messages and data

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
    $_SESSION['error_message'] = "Database connection failed: " . $e->getMessage();
    header('Location: current_monthbill.php');
    exit();
}

$results = [];
$error = null;

if (isset($_POST['calculate_bill'])) {
    $customer_id = $_POST['customer_id'];
    $date = $_POST['date'];  // Full date (YYYY-MM-DD)

    // Validate if Customer ID is provided
    if (empty($customer_id)) {
        $_SESSION["error_message"] = "Customer ID is required.";
        header("Location: current_monthbill.php");
        exit();
    }

    // Validate if Date is provided
    if (empty($date)) {
        $_SESSION["error_message"] = "Date is required.";
        header("Location: current_monthbill.php");
        exit();
    }

    // Query the database for the customer data (starting reading, ending reading, usage)
    $stmt = $pdo->prepare("SELECT * FROM usage_data WHERE customer_id = ? AND date = ?");
    $stmt->execute([$customer_id, $date]);  // Use full date (YYYY-MM-DD)
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        $_SESSION["error_message"] = "No data found for Customer ID: " . htmlspecialchars($customer_id) . " and Date: " . htmlspecialchars($date) . ".";
        header("Location: current_monthbill.php");
        exit();
    }

    // Retrieve customer data
    $customer_data = $results[0];
    $start_reading = $customer_data['start_reading'];
    $end_reading = $customer_data['end_reading'];

    // Calculate electricity usage
    $total_usage = $end_reading - $start_reading;

    // Store the data in session to display in the table
    $_SESSION['usage_table'] = [
        'customer_id' => $customer_id,
        'date' => $date, // Store full date here
        'start_reading' => $start_reading,
        'end_reading' => $end_reading,
        'total_usage' => $total_usage
    ];

    // Determine discount rate
    $discount_rate = 0;
    if ($total_usage > 600) {
        $discount_rate = 0.20;
    } elseif ($total_usage > 400) {
        $discount_rate = 0.15;
    } elseif ($total_usage > 200) {
        $discount_rate = 0.10;
    } elseif ($total_usage >= 80) {
        $discount_rate = 0.05;
    }

    $discount_amount = $total_usage * $discount_rate;

    // Calculate bill
    $tariff_rate = 0.218; // Default tariff rate
    if ($total_usage > 900) {
        $tariff_rate = 0.571;
    } elseif ($total_usage > 600) {
        $tariff_rate = 0.546;
    } elseif ($total_usage > 300) {
        $tariff_rate = 0.516;
    } elseif ($total_usage > 200) {
        $tariff_rate = 0.334;
    }

    $fixed_charge = 3.00; // Fixed charge
    $bill_amount = round(($total_usage * $tariff_rate + $fixed_charge - $discount_amount), 2);

    // Store the bill data in session
    $_SESSION['bill_data'] = [
        'customer_id' => $customer_id,
        'date' => $date, // Store full date here
        'total_usage' => $total_usage,
        'tariff_rate' => $tariff_rate,
        'fixed_charge' => $fixed_charge,
        'discount_amount' => $discount_amount,
        'bill_amount' => $bill_amount
    ];

    $_SESSION['success_message'] = "Bill calculated successfully for Customer ID: " . htmlspecialchars($customer_id) . " and Date: " . htmlspecialchars($date) . ".";

    header('Location: current_monthbill.php');
    exit();
}

if (isset($_POST['save_bill'])) {
    // Save the bill data into the current_monthbill table
    if (isset($_SESSION['bill_data'])) {
        $bill_data = $_SESSION['bill_data'];
        
        // Prepare the SQL query to insert the data
        $stmt = $pdo->prepare("INSERT INTO current_monthbill (customer_id, date, total_usage, tariff_rate, fixed_charge, discount_amount, bill_amount) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $bill_data['customer_id'], 
            $bill_data['date'], 
            $bill_data['total_usage'], 
            $bill_data['tariff_rate'], 
            $bill_data['fixed_charge'], 
            $bill_data['discount_amount'], 
            $bill_data['bill_amount']
        ]);

        $_SESSION['success_message'] = "Bill saved successfully to the database.";
        unset($_SESSION['bill_data']); // Clear bill data from session after saving
    } else {
        $_SESSION['error_message'] = "No bill data available to save.";
    }

    header('Location: current_monthbill.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Month Bill</title>
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
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            padding: 15px;
            text-align: center;
        }
        .receipt {
            border: 1px solid #007bff;
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
            margin-top: 20px;
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
            margin-top: 20px; /* Adds spacing above the button */
            margin-bottom: 20px; /* Adds spacing below the button */
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
        <h1 class="display-4">Current Month Electricity Bill</h1>
        <p class="lead">Calculate and Generate Your Monthly Bill</p>
    </div>

    <div class="container mt-4">
        <!-- Display Messages -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_SESSION['error_message']); ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Electricity Usage Form -->
        <form method="post" action="">
            <h4>Retrieve and Calculate Electricity Bill</h4>
            <div class="form-group">
                <label for="customer_id">Customer ID:</label>
                <input list="customers" name="customer_id" class="form-control" required>
                <datalist id="customers">
                    <option value="CUS1234">
                    <option value="CUS5678">
                    <option value="CUS9012">
                    <option value="CUS3456">
                    <option value="CUS7890">
                </datalist>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <button type="submit" name="calculate_bill" class="btn btn-primary mb-4">Calculate Bill</button>
        </form>

        <!-- Display Usage Data Table -->
        <?php if (isset($_SESSION['usage_table'])): ?>
            <div class="mt-4">
                <h5>Electricity Usage Data</h5>
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Customer ID</th>
                            <th>Date</th>
                            <th>Start Reading</th>
                            <th>End Reading</th>
                            <th>Total Usage (kWh)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($_SESSION['usage_table']['customer_id']); ?></td>
                            <td><?= htmlspecialchars($_SESSION['usage_table']['date']); ?></td>
                            <td><?= htmlspecialchars($_SESSION['usage_table']['start_reading']); ?></td>
                            <td><?= htmlspecialchars($_SESSION['usage_table']['end_reading']); ?></td>
                            <td><?= htmlspecialchars($_SESSION['usage_table']['total_usage']); ?> kWh</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Display Bill Data -->
        <?php if (isset($_SESSION['bill_data'])): ?>
            <div class="receipt">
                <h5>Current Month Details Bill</h5>
                <hr>
                <p><strong>Customer ID:</strong> <?= htmlspecialchars($_SESSION['bill_data']['customer_id']); ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($_SESSION['bill_data']['date']); ?></p>
                <p><strong>Total Usage:</strong> <?= htmlspecialchars($_SESSION['bill_data']['total_usage']); ?> kWh</p>
                <p><strong>Tariff Rate:</strong> RM<?= htmlspecialchars($_SESSION['bill_data']['tariff_rate']); ?> per kWh</p>
                <p><strong>Fixed Charge:</strong> RM<?= htmlspecialchars($_SESSION['bill_data']['fixed_charge']); ?></p>
                <p><strong>Discount Amount:</strong> RM<?= htmlspecialchars($_SESSION['bill_data']['discount_amount']); ?></p>
                <p><strong>Current Month Bill Amount:</strong> RM<?= htmlspecialchars($_SESSION['bill_data']['bill_amount']); ?></p>
                <hr>
            </div>
        <?php endif; ?>

        <!-- Save Bill Data -->
        <?php if (isset($_SESSION['bill_data'])): ?>
            <form method="post" action="">
                <button type="submit" name="save_bill" class="btn btn-success mb-4">Save Bill</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
