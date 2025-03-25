<?php
session_start(); // Start session for error or success messages

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
    header('Location: check_outstanding.php');
    exit();
}

$results = [];
$error = null;

if (isset($_POST['check_outstanding'])) {
    $customer_id = $_POST['customer_id'];

    // Query the database for payment history (Only fetching date & outstanding)
    $stmt = $pdo->prepare("SELECT payment_date, payment_amount FROM payment WHERE customer_id = ? ORDER BY payment_date DESC");
    $stmt->execute([$customer_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        $error = "No outstanding balance found for Customer ID: $customer_id.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Outstanding Bill</title>
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
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background: #007bff;
            color: white;
        }
        .table th, .table td {
            padding: 15px;
            text-align: center;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Electricity Billing System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Calculation</a></li>
                    <li class="nav-item"><a class="nav-link" href="Check_outstanding.php">Check Outstanding</a></li>
                    <li class="nav-item"><a class="nav-link" href="discount.php">Discount and Usage Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="jumbotron text-center">
        <h1 class="display-4">Check Outstanding Bill</h1>
        <p class="lead">View outstanding balance by customer ID</p>
    </div>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="customer_id">Enter Customer ID:</label>
                <input type="text" name="customer_id" class="form-control" placeholder="Enter Customer ID" required>
            </div>

            <button type="submit" name="check_outstanding" class="btn btn-primary">Check Outstanding</button>
        </form>

        <?php if ($results): ?>
            <div class="mt-4">
                <h5>Outstanding Balance for Customer ID: <?= htmlspecialchars($_POST['customer_id']); ?></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Outstanding (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $payment): ?>
                            <tr>
                                <td><?= htmlspecialchars($payment['payment_date']); ?></td>
                                <td><?= htmlspecialchars($payment['payment_amount']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
