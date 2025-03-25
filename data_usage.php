<?php
session_start(); // Start the session for managing messages and data

if (isset($_POST['validate_discount']) || isset($_POST['submit_reading']) || isset($_POST['send_data_usage'])) {
    $customer_id = $_POST['customer_id'];
    $month = $_POST['month'];

    if (isset($_POST['submit_reading'])) {
        $start_reading = $_POST['start_reading'];
        $end_reading = $_POST['end_reading'];

        // Validate that ending reading is greater than starting reading
        if ($end_reading <= $start_reading) {
            $_SESSION['error_message'] = "Invalid readings. Ensure the ending reading is greater than the starting reading.";
        } else {
            // Calculate electricity usage
            $total_usage = $end_reading - $start_reading;

            // Save the data to a simulated file (or database)
            $file = fopen('data_usage.csv', 'a');
            fputcsv($file, [$customer_id, $month, $start_reading, $end_reading, $total_usage]);
            fclose($file);

            $_SESSION['success_message'] = "Data usage for $month has been successfully saved.";
            $_SESSION['usage_data'] = [
                'customer_id' => $customer_id,
                'month' => $month,
                'start_reading' => $start_reading,
                'end_reading' => $end_reading,
                'total_usage' => $total_usage
            ];
        }
    } elseif (isset($_POST['validate_discount'])) {
        $total_usage = $_POST['total_usage'] ?? ($_SESSION['usage_data']['total_usage'] ?? 0);

        // Define discount eligibility criteria
        $discount_rate = 0;
        if ($total_usage > 1000) {
            $discount_rate = 0.15;
        } elseif ($total_usage > 600) {
            $discount_rate = 0.10;
        } elseif ($total_usage > 400) {
            $discount_rate = 0.05;
        }

        // Determine if discount is valid and set discount value
        if ($discount_rate > 0) {
            $discount_amount = $total_usage * $discount_rate;
            $message = "Discount of " . ($discount_rate * 100) . "% (RM" . round($discount_amount, 2) . ") applied successfully.";

            // Save discount data in session or database
            $_SESSION['discount_data'] = [
                'customer_id' => $customer_id,
                'month' => $month,
                'total_usage' => $total_usage,
                'discount_rate' => $discount_rate * 100, // Convert to percentage
                'discount_amount' => round($discount_amount, 2),
            ];

            $_SESSION['success_message'] = $message;
        } else {
            $_SESSION['error_message'] = "No discount applicable for usage of $total_usage kWh.";
        }
    } elseif (isset($_POST['send_data_usage'])) {
        $_SESSION['success_message'] = "Data usage has been successfully sent to customer ID: $customer_id.";
    }

    header('Location: discount.php');
    exit();
}

if (isset($_POST['save_discount'])) {
    $discount_data = $_SESSION['discount_data'];

    // Save discount data to a CSV file
    $file = fopen('discount_data.csv', 'a'); // Open file in append mode
    fputcsv($file, [
        $discount_data['customer_id'],
        $discount_data['month'],
        $discount_data['total_usage'],
        $discount_data['discount_rate'],
        $discount_data['discount_amount']
    ]);
    fclose($file);

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
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
        </div>
    </nav>

    <div class="jumbotron text-center text-white bg-primary mb-0">
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

        <!-- Combined Form for Reading Input and Discount Validation -->
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
                <label for="month">Select Month:</label>
                <input type="month" name="month" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="start_reading">Starting Reading (kWh):</label>
                <input type="number" name="start_reading" class="form-control" placeholder="Enter Starting Reading" required>
            </div>
            <div class="form-group">
                <label for="end_reading">Ending Reading (kWh):</label>
                <input type="number" name="end_reading" class="form-control" placeholder="Enter Ending Reading" required>
            </div>
            <button type="submit" name="submit_reading" class="btn btn-success">Calculate Usage</button>
        </form>

        <?php if (isset($_SESSION['usage_data'])): ?>
            <div class="mt-4">
                <h5>Electricity Usage Data</h5>
                <p><strong>Customer ID:</strong> <?= htmlspecialchars($_SESSION['usage_data']['customer_id']); ?></p>
                <p><strong>Month:</strong> <?= htmlspecialchars($_SESSION['usage_data']['month']); ?></p>
                <p><strong>Starting Reading:</strong> <?= htmlspecialchars($_SESSION['usage_data']['start_reading']); ?> kWh</p>
                <p><strong>Ending Reading:</strong> <?= htmlspecialchars($_SESSION['usage_data']['end_reading']); ?> kWh</p>
                <p><strong>Total Usage:</strong> <?= htmlspecialchars($_SESSION['usage_data']['total_usage']); ?> kWh</p>

                <!-- Discount Validation Form -->
                <form method="post" action="">
                    <input type="hidden" name="total_usage" value="<?= htmlspecialchars($_SESSION['usage_data']['total_usage']); ?>">
                    <button type="submit" name="validate_discount" class="btn btn-primary">Validate Discount</button>
                </form>

                <!-- Send Data Usage Form -->
                <form method="post" action="" class="mt-2">
                    <button type="submit" name="send_data_usage" class="btn btn-info">Send Data Usage to Customer</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Display Discount Data -->
        <?php if (isset($_SESSION['discount_data'])): ?>
            <div class="alert alert-info mt-4">
                <h5>Discount Details</h5>
                <p><strong>Customer ID:</strong> <?= htmlspecialchars($_SESSION['discount_data']['customer_id']); ?></p>
                <p><strong>Month:</strong> <?= htmlspecialchars($_SESSION['discount_data']['month']); ?></p>
                <p><strong>Total Usage:</strong> <?= htmlspecialchars($_SESSION['discount_data']['total_usage']); ?> kWh</p>
                <p><strong>Discount Rate:</strong> <?= htmlspecialchars($_SESSION['discount_data']['discount_rate']); ?>%</p>
                <p><strong>Discount Amount:</strong> RM<?= htmlspecialchars($_SESSION['discount_data']['discount_amount']); ?></p>
                <form method="post" action="">
                    <button type="submit" name="save_discount" class="btn btn-success">Save Discount</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
