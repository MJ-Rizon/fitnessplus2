<?php
session_start();
include '../../db/connection.php';
// Ensure $connection is set (for compatibility with db/connection.php)
if (!isset($connection) && isset($conn)) {
    $connection = $conn;
}

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_username'])) {
    header("Location: ../Login/admin_login.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $contact = $_POST['contact'];
    $dor = $_POST['dor'];
    $plan = intval($_POST['plan']);
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password confirmation check
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check for duplicate username
        $check = $connection->prepare("SELECT user_id FROM members WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // Compute expiration date
            $expDateObj = new DateTime($dor);
            $expDateObj->modify("+$plan months");
            $expDate = $expDateObj->format('Y-m-d');

            $sql = "INSERT INTO members (fullname, contact, dor, plan, expiration_date, gender, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssissss", $fullname, $contact, $dor, $plan, $expDate, $gender, $username, $password_hashed);

            if ($stmt->execute()) {
                header("Location: ../members.php?msg=added");
                exit;
            } else {
                $error = "Error adding member.";
            }
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add Member</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post">
    <div class="mb-3">
        <label>Fullname</label>
        <input type="text" name="fullname" class="form-control" required value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>">
    </div>
    <div class="mb-3">
        <label>Contact Number</label>
        <input type="text" name="contact" class="form-control" required value="<?php echo isset($contact) ? htmlspecialchars($contact) : ''; ?>">
    </div>
    <div class="mb-3">
        <label>Registration Date</label>
        <input type="date" name="dor" class="form-control" required value="<?php echo isset($dor) ? htmlspecialchars($dor) : ''; ?>">
    </div>
    <div class="mb-3">
        <label>Plan (months)</label>
        <input type="number" name="plan" class="form-control" required value="<?php echo isset($plan) ? htmlspecialchars($plan) : ''; ?>">
    </div>
    <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-control" required>
            <option value="">Select gender</option>
            <option value="Male" <?php if(isset($gender) && $gender == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if(isset($gender) && $gender == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if(isset($gender) && $gender == 'Other') echo 'selected'; ?>>Other</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Add Member</button>
    <a href="../members.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>
</html>