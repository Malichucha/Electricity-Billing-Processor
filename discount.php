<?php
session_start();

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
    header('Location: discount.php');
    exit();
}

if (isset($_POST['submit_reading']) || isset($_POST['send_data_usage'])) {
    $customer_id = $_POST['customer_id'] ?? ($_SESSION['usage_data']['customer_id'] ?? null);
    $date = $_POST['date'] ?? ($_SESSION['usage_data']['date'] ?? null);

    // Validate the customer ID exists in the database
    if ($customer_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        $customer_exists = $stmt->fetchColumn();

        if (!$customer_exists) {
            $_SESSION['error_message'] = "Customer ID $customer_id does not exist in the database.";
            header('Location: discount.php');
            exit();
        }
    }

    if (isset($_POST['submit_reading'])) {
        $start_reading = $_POST['start_reading'];
        $end_reading = $_POST['end_reading'];

        // Validate that readings are not negative
        if ($start_reading < 0 || $end_reading < 0) {
            $_SESSION['error_message'] = "Invalid readings. Ensure the starting and ending reading are positive numbers.";
        } elseif ($end_reading <= $start_reading) {
            $_SESSION['error_message'] = "Invalid readings. Ensure the ending reading is greater than the starting reading.";
        } else {
            // Calculate electricity usage
            $total_usage = $end_reading - $start_reading;

            // Save the data into SQL database (ensure date is passed, not just month)
            $stmt = $pdo->prepare("INSERT INTO usage_data (customer_id, date, start_reading, end_reading, total_usage) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$customer_id, $date, $start_reading, $end_reading, $total_usage]);

            // Automatically validate the discount based on usage
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

            if ($discount_rate > 0) {
                $discount_amount = $total_usage * $discount_rate;
                $message = "Discount of " . ($discount_rate * 100) . "% (RM" . round($discount_amount, 2) . ") applied successfully.";

                // Save discount data (ensure date is passed)
                $stmt = $pdo->prepare("INSERT INTO discount_data (customer_id, date, discount_rate, discount_amount) VALUES (?, ?, ?, ?)");
                $stmt->execute([$customer_id, $date, $discount_rate, round($discount_amount, 2)]);

                $_SESSION['discount_data'] = [
                    'customer_id' => $customer_id,
                    'date' => $date, // Store full date here
                    'total_usage' => $total_usage,
                    'discount_rate' => $discount_rate * 100, // Convert to percentage
                    'discount_amount' => round($discount_amount, 2),
                ];

                $_SESSION['success_message'] = $message;
            } else {
                $_SESSION['success_message'] = "No discount applicable for usage of $total_usage kWh.";
            }

            $_SESSION['usage_data'] = [
                'customer_id' => $customer_id,
                'date' => $date, // Store full date here
                'start_reading' => $start_reading,
                'end_reading' => $end_reading,
                'total_usage' => $total_usage
            ];
        }
    } elseif (isset($_POST['send_data_usage'])) {
        // Ensure that session data exists and the customer ID is valid
        if (!empty($_SESSION['usage_data']) && isset($_SESSION['usage_data']['customer_id'])) {
            $customer_id = $_SESSION['usage_data']['customer_id'];

            // Check if the customer exists in the database
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE customer_id = ?");
            $stmt->execute([$customer_id]);
            $customer_exists = $stmt->fetchColumn();

            if (!$customer_exists) {
                $_SESSION['error_message'] = "Failed to send data usage. The customer does not exist in the database.";
            } else {
                $_SESSION['success_message'] = "Data usage has been successfully sent to customer ID: $customer_id.";
                unset($_SESSION['usage_data']); // Clear the usage data
            }
        } else {
            $_SESSION['error_message'] = "Failed to send data usage. Customer ID is missing.";
        }
    }

    header('Location: discount.php');
    exit();
}

if (isset($_POST['save_discount'])) {
    $discount_data = $_SESSION['discount_data'];

    // Save discount data to SQL database
    $stmt = $pdo->prepare("INSERT INTO discount_data (customer_id, date, discount_rate, discount_amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([$discount_data['customer_id'], $discount_data['date'], $discount_data['discount_rate'] / 100, $discount_data['discount_amount']]);

    $_SESSION['success_message'] = 'Discount data saved successfully under Customer ID: ' . $discount_data['customer_id'];
    unset($_SESSION['discount_data']); // Clear discount data from session
    header('Location: discount.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Discount Validation and Usage</title>
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
        .form-group label {
            font-weight: bold;
        }
        .btn {
            margin-top: 10px;
        }
        .alert {
            margin-top: 20px;
        }
        .usage-data, .discount-data {
            margin-top: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .usage-data h5, .discount-data h5 {
            font-size: 1.3rem;
            color: #007bff;
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
        <h1 class="display-4">Electricity Discount and Usage Management</h1>
        <p class="lead">Manage, Validate, and Apply Discounts for Electricity Usage</p>
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

        <!-- Form for Reading Input and Discount Validation -->
        <form method="post" action="">
            <h4>Enter Electricity Usage Data</h4>
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
                <label for="date">Select Date:</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="start_reading">Starting Reading (kWh):</label>
                <input type="number" name="start_reading" class="form-control" placeholder="Enter Starting Reading" required>
            </div>
            <div class="form-group">
                <label for="end_reading">Ending Reading (kWh):</label>
                <input type="number" name="end_reading" class="form-control" placeholder="Enter Ending Reading" required>
            </div>
            <button type="submit" name="submit_reading" class="btn btn-success mb-4">Calculate Usage and Validate Discount</button>
        </form>

        <?php if (isset($_SESSION['usage_data'])): ?>
            <div class="usage-data">
                <h5>Electricity Usage Data</h5>
                <p><strong>Customer ID:</strong> <?= htmlspecialchars($_SESSION['usage_data']['customer_id']); ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($_SESSION['usage_data']['date']); ?></p>
                <p><strong>Starting Reading:</strong> <?= htmlspecialchars($_SESSION['usage_data']['start_reading']); ?> kWh</p>
                <p><strong>Ending Reading:</strong> <?= htmlspecialchars($_SESSION['usage_data']['end_reading']); ?> kWh</p>
                <p><strong>Total Usage:</strong> <?= htmlspecialchars($_SESSION['usage_data']['total_usage']); ?> kWh</p>

                <!-- Send Data Usage Form -->
                <form method="post" action="" class="mt-2">
                    <button type="submit" name="send_data_usage" class="btn btn-info">Send Data Usage to Customer</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Display Discount Data -->
        <?php if (isset($_SESSION['discount_data'])): ?>
            <div class="discount-data">
                <h5>Discount and Usage Details</h5>
                <p><strong>Customer ID:</strong> <?= htmlspecialchars($_SESSION['discount_data']['customer_id']); ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($_SESSION['discount_data']['date']); ?></p>
                <p><strong>Total Usage:</strong> <?= htmlspecialchars($_SESSION['discount_data']['total_usage']); ?> kWh</p>
                <p><strong>Discount Rate:</strong> <?= htmlspecialchars($_SESSION['discount_data']['discount_rate']); ?>%</p>
                <p><strong>Discount Amount:</strong> RM<?= htmlspecialchars($_SESSION['discount_data']['discount_amount']); ?></p>
                <form method="post" action="">
                    <button type="submit" name="save_discount" class="btn btn-success">Save Discount and Usage</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
