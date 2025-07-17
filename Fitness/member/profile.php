<?php
session_start();
include '../db/connection.php';

// Get member ID from session
$member_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$member_id) {
    header("Location: ../login.php");
    exit;
}

// Fetch member details
$stmt = $conn->prepare("SELECT fullname, username, gender, dor, expiration_date, services, amount, paid_date, plan, address, contact, status, attendance_count, ini_weight, curr_weight, ini_bodytype, curr_bodytype, progress_date FROM members WHERE user_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$stmt->bind_result($fullname, $username, $gender, $dor, $expiration_date, $services, $amount, $paid_date, $plan, $address, $contact, $status, $attendance_count, $ini_weight, $curr_weight, $ini_bodytype, $curr_bodytype, $progress_date);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fitness+ Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">My Profile</h2>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3"><?php echo htmlspecialchars($fullname); ?></h4>
            <table class="table table-bordered">
                <tr><th>Username</th><td><?php echo htmlspecialchars($username); ?></td></tr>
                <tr><th>Gender</th><td><?php echo htmlspecialchars($gender); ?></td></tr>
                <tr><th>Date of Registration</th><td><?php echo htmlspecialchars($dor); ?></td></tr>
                <tr><th>Expiration Date</th><td><?php echo htmlspecialchars($expiration_date); ?></td></tr>
                <tr><th>Services</th><td><?php echo htmlspecialchars($services); ?></td></tr>
                <tr><th>Amount</th><td><?php echo htmlspecialchars($amount); ?></td></tr>
                <tr><th>Paid Date</th><td><?php echo htmlspecialchars($paid_date); ?></td></tr>
                <tr><th>Plan</th><td><?php echo htmlspecialchars($plan); ?></td></tr>
                <tr><th>Address</th><td><?php echo htmlspecialchars($address); ?></td></tr>
                <tr><th>Contact</th><td><?php echo htmlspecialchars($contact); ?></td></tr>
                <tr><th>Status</th><td><?php echo htmlspecialchars($status); ?></td></tr>
                <tr><th>Attendance Count</th><td><?php echo htmlspecialchars($attendance_count); ?></td></tr>
                <tr><th>Initial Weight</th><td><?php echo htmlspecialchars($ini_weight); ?> kg</td></tr>
                <tr><th>Current Weight</th><td><?php echo htmlspecialchars($curr_weight); ?> kg</td></tr>
                <tr><th>Initial Body Type</th><td><?php echo htmlspecialchars($ini_bodytype); ?></td></tr>
                <tr><th>Current Body Type</th><td><?php echo htmlspecialchars($curr_bodytype); ?></td></tr>
                <tr><th>Progress Date</th><td><?php echo htmlspecialchars($progress_date); ?></td></tr>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>