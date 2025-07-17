<?php
session_start();
include '../config.php';

$error = "";

function admin_login($connection, $username, $password) {
    // Fetch the hashed password for the given username
    $stmt = $connection->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Allow both hashed and plain text passwords for compatibility
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: ../dashboard.php");
            exit();
        }
    }
    $stmt->close();
    return "Invalid username or password!";
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    if ($username == '' || $password == '') {
        $error = 'Username and password are required.';
    } else {
        $error = admin_login($connection, $username, $password);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Fitness+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
        background: url(login_bg.jpg);
        background-repeat: no-repeat;
        background-position: center;
        background-attachement: fixed;
        background-size: cover;
        background-color: #000;
        min-height: 100vh;
    }
    .login-container {
        background: #000;
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        padding: 2.5rem 2rem 2rem 2rem;
        margin-top: 80px;
        color: #fff;
    }
    .login-title {
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
    <div class="login-container col-12 col-md-6 col-lg-4">
        <h2 class="mb-4 text-center login-title">Admin Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <a href="../../member/Login/member_login.php" class="btn btn-dark w-100 mt-2">Member Login</a>
    </div>
</div>
</body>
</html>