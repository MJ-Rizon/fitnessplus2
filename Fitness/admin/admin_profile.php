<?php
session_start();
include 'config.php';

$error = "";
$message = "";

// Get current admin username from session
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_username'])) {
    header("Location: Login/admin_login.php");
    exit();
}
$current_username = $_SESSION['admin_username'];

// Fetch current admin info
$stmt = $connection->prepare("SELECT username FROM admins WHERE username = ?");
$stmt->bind_param("s", $current_username);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $current_username = $row['username'];
}
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_username == '') {
        $error = "Username cannot be empty.";
    } elseif ($password !== '' && $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username is taken by another admin
        if ($new_username !== $_SESSION['admin_username']) {
            $stmt = $connection->prepare("SELECT user_id FROM admins WHERE username = ? AND username != ?");
            $stmt->bind_param("ss", $new_username, $_SESSION['admin_username']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error = "Username already taken.";
            }
            $stmt->close();
        }
        if ($error == "") {
            if ($password !== '') {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $connection->prepare("UPDATE admins SET username = ?, password = ? WHERE username = ?");
                $stmt->bind_param("sss", $new_username, $hashed_password, $_SESSION['admin_username']);
            } else {
                $stmt = $connection->prepare("UPDATE admins SET username = ? WHERE username = ?");
                $stmt->bind_param("ss", $new_username, $_SESSION['admin_username']);
            }
            if ($stmt->execute()) {
                $_SESSION['admin_username'] = $new_username;
                $message = "Profile updated successfully.";
            } else {
                $error = "Failed to update profile.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Admin Profile</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required
                    value="<?php echo htmlspecialchars($current_username); ?>">
            </div>
            <div class="mb-3">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>