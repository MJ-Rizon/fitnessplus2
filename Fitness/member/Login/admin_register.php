<?php
session_start();
include '../config.php';

//put this code to sign up admin
//<a href="admin_register.php" class="btn btn-success w-100 mt-2">Sign Up</a>

$error = "";
$success = "";

function admin_register($connection, $username, $password, $confirm_password, $fullname = null) {
    if ($username == '' || $password == '' || $confirm_password == '') {
        return 'All fields are required.';
    }
    if ($password !== $confirm_password) {
        return 'Passwords do not match.';
    }
    // Check if username already exists
    $stmt = $connection->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        return "Username already exists.";
    }
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("INSERT INTO admins (fullname, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $username, $hashed_password);
    if ($stmt->execute()) {
        return "Registration successful! <a href='admin_login.php'>Login here</a>.";
    } else {
        return "Registration failed. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : null;
    $result = admin_register($connection, $username, $password, $confirm_password, $fullname);
    if (strpos($result, 'successful') !== false) {
        $success = $result;
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register - Fitness+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url(../../login_bg.jpg);
            background-repeat: no-repeat;
            background-position: center;
            background-attachement: fixed;
            background-size: cover;
            background-color: #000;
            min-height: 100vh;
        }
        .register-container {
            background: #000;
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-top: 80px;
            color: #fff;
        }
        .register-title {
            font-weight: 700;
            color: #fff;
        }
        .btn-primary {
            background:rgb(17, 183, 20);
            border: none;
        }
        .btn-primary:hover {
            background:rgb(3, 122, 25);
        }
        .form-label {
            color: #fff;
        }
        .form-control {
            color: #fff !important;
            background-color: #222 !important;
            border-color: #444;
        }
        .form-control::placeholder {
            color: #bbb !important;
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="register-container col-12 col-md-6 col-lg-4">
        <h2 class="mb-4 text-center register-title">Admin Register</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3"><label class="form-label">Full Name (optional)</label><input type="text" name="fullname" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <a href="admin_login.php" class="btn btn-secondary w-100 mt-2">Back to Login</a>
    </div>
</div>
</body>
</html>