<?php
session_start();
require_once 'includes/db_connection.php'; // Ensure this connects to your database

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'signup') {
        // Sign-Up Logic
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
        $role = $_POST['role'];
        $status = $_POST['status'];

        $query = "INSERT INTO users (id, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $id, $name, $email, $password, $role, $status);

        if ($stmt->execute()) {
            $success = "User registered successfully! Please log in.";
        } else {
            $error = "Error: Could not register user. " . $conn->error;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
        // Login Logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Sign Up - Electricity Billing System</title>
    <link rel="stylesheet" href="assets/styles.css"> <!-- Ensure correct path -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('assets/diagonal-striped-brick-1920x1080.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        .container {
            width: 400px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        h1 {
            color: #004080;
            font-size: 32px;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #004080;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 255, 0.2);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #004080;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color:#003366;
        }

        .message {
            text-align: center;
            margin-top: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .toggle {
            text-align: center;
            margin-top: 20px;
        }

        .toggle a {
            color: #004080; /* Set the link text color to blue */
            text-decoration: none;
            font-weight: bold;
        }

        .toggle a:hover {
            text-decoration: underline;
        }

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            color: white;
            text-decoration: none;
        }

        .back-arrow:hover {
            color: #ffc107;
        }

        .card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 30px;
            background-color:#ffffff;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-arrow">&larr;</a>
        <h1>Electricity Billing System</h1>
        <h2><?php echo isset($_GET['action']) && $_GET['action'] === 'signup' ? 'Sign Up' : 'Login'; ?></h2>
        <?php if (isset($success)): ?>
            <div class="message success"> <?php echo $success; ?> </div>
        <?php elseif (isset($error)): ?>
            <div class="message error"> <?php echo $error; ?> </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php if (isset($_GET['action']) && $_GET['action'] === 'signup'): ?>
                <div class="form-group">
                    <label for="id">User ID</label>
                    <input type="text" id="id" name="id" required>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php if (isset($_GET['action']) && $_GET['action'] === 'signup'): ?>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="Admin">Admin</option>
                        <option value="Billing Processor">Billing Processor</option>
                        <option value="Manager">Manager</option>
                        <option value="Customer">Customer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="action" value="signup" class="btn">Sign Up</button>
            <?php else: ?>
                <button type="submit" name="action" value="login" class="btn">Login</button>
            <?php endif; ?>
        </form>
        <div class="toggle">
            <?php if (isset($_GET['action']) && $_GET['action'] === 'signup'): ?>
                Already have an account? <a href="?action=login">Login</a>
            <?php else: ?>
                Don't have an account yet? <a href="?action=signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
