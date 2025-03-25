<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .hero-section {
            background: linear-gradient(90deg, rgba(0, 123, 255, 0.8), rgb(0, 86, 179)), url('https://via.placeholder.com/1500x500');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero-section p {
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card h5 {
            font-size: 1.5rem;
            font-weight: bold;
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
                    <li class="nav-item"><a class="nav-link" href="check_outstanding.php">Check Outstanding</a></li>
                    <li class="nav-item"><a class="nav-link" href="discount.php">Discount and Usage Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Welcome to the Electricity Billing System</h1>
        <p>Manage your billing, check outstanding payments, and handle discounts seamlessly.</p>
    </div>

    <!-- Dashboard Content -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card p-4">
                    <h5>Calculation</h5>
                    <p>Easily calculate your electricity usage and bills.</p>
                    <a href="index.php" class="btn btn-primary">Go to Calculation</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h5>Check Outstanding</h5>
                    <p>View and track outstanding payments quickly.</p>
                    <a href="Check_outstanding.php" class="btn btn-primary">Check Outstanding</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h5>Discount & Usage</h5>
                    <p>Manage discounts and monitor electricity usage.</p>
                    <a href="discount.php" class="btn btn-primary">Manage Discounts and Usage</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 py-4 bg-light">
        <p>&copy; 2025 Electricity Billing System. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
