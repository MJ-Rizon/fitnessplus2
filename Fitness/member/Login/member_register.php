<?php
session_start();
include '../config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($username == '' || $password == '' || $confirm_password == '') {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username already exists
        $stmt = $connection->prepare("SELECT * FROM members WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            // Hash the password and insert new member
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $connection->prepare("INSERT INTO members (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                $success = "Registration successful! <a href='../member_login.php'>Login here</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Register - Fitness+</title>
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
        <h2 class="mb-4 text-center register-title">Member Register</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <a href="member_login.php" class="btn btn-secondary w-100 mt-2">Back to Login</a>
    </div>
</div>
</body>
</html>