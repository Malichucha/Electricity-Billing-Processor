<?php session_start(); // Start session to retrieve error or success messages ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Billing System</title>
    <!-- Bootstrap CSS -->
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
        .btn-lg {
            padding: 15px 30px;
            font-size: 1.2rem;
        }
        footer {
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
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

    <!-- Header Section -->
    <div class="jumbotron text-center">
        <h1 class="display-4">Electricity Billing System</h1>
        <p class="lead">Efficient and Accurate Electricity Billing</p>
    </div>

    <!-- Main Content Section -->
    <div class="container mt-4">
        <!-- Display Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); // Clear the message ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); // Clear the message ?>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="text-center">
            <a href="current_monthbill.php" class="btn btn-primary btn-lg mb-3">Calculate Current Month Bill</a>
            <a href="final_bill.php" class="btn btn-secondary btn-lg mb-3">Calculate Final Bill</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 py-4">
        <p>&copy; <?= date('Y'); ?> Electricity Billing System. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
