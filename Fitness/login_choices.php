<?php
session_start();


$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Example: check against a users table (replace with your real table/fields)
    $sql = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Choices - Fitness+</title>
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
        .choice-container {
            background: rgb(0, 0, 0);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-top: 80px;
        }
        .choice-title {
            font-weight: 700;
            color:rgb(255, 255, 255);
        }
        .btn-choice {
            background: rgb(17, 183, 20);
            border: none;
            color: #fff;
            margin-bottom: 1rem;
        }23
        .btn-choice:hover {
            background: #176682;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="choice-container col-12 col-md-6 col-lg-4 text-center">
        <h2 class="mb-4 choice-title">Choose Login Type</h2>
        <a href="admin/login/admin_login.php" class="btn btn-choice w-100">Admin Login</a>
        <a href="member/login/member_login.php" class="btn btn-choice w-100">Member Login</a>
        <!-- Add more login types as needed -->
    </div>
</div>
</body>
</html>