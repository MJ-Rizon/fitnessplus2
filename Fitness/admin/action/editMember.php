<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Login/admin_login.php");
    exit;
}
include '../config.php'; // adjust path as needed

// Get member ID from URL
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Fetch member data
    $sql = "SELECT * FROM members WHERE user_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    if (!$member) { 
        echo "Member not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $contact = $_POST['contact'];
    $dor = $_POST['dor'];
    $plan = intval($_POST['plan']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Compute new expiration date
    $expDateObj = new DateTime($dor);
    $expDateObj->modify("+$plan months");
    $expDate = $expDateObj->format('Y-m-d');

    // Password confirmation check
    if (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE members SET fullname=?, contact=?, dor=?, plan=?, expiration_date=?, password=? WHERE user_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssissi", $fullname, $contact, $dor, $plan, $expDate, $hashedPassword, $user_id);
        } else {
            $sql = "UPDATE members SET fullname=?, contact=?, dor=?, plan=?, expiration_date=? WHERE user_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssisi", $fullname, $contact, $dor, $plan, $expDate, $user_id);
        }

        if (empty($error) && $stmt->execute()) {
            header("Location: ../members.php?msg=updated");
            exit;
        } elseif (empty($error)) {
            $error = "Error updating member.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Member</h2>
    <div class="mb-3">
        <strong>Username:</strong> <?php echo htmlspecialchars($member['username']); ?>
    </div>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Fullname</label>
            <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($member['fullname']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($member['contact']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Registration Date</label>
            <input type="date" name="dor" class="form-control" value="<?php echo htmlspecialchars($member['dor']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Plan (months)</label>
            <input type="number" name="plan" class="form-control" value="<?php echo htmlspecialchars($member['plan']); ?>" required>
        </div>
        <div class="mb-3">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" placeholder="New Password">
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="../members.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>