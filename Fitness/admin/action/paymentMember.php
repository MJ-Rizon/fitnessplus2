<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Login/admin_login.php");
    exit;
}
include '../../db/connection.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Fetch member info
$member = null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM members WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $member) {
    $plan = $_POST['plan'];
    $amount = $_POST['amount'];
    $paid_date = $_POST['paid_date'];

    // Insert payment record
    $stmt = $conn->prepare("INSERT INTO monthlyPayments (user_id, plan, amount, paid_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $plan, $amount, $paid_date);
    $stmt->execute();
    $stmt->close();

    // Calculate new expiration date
    $expiration_date = '';
    if (!empty($paid_date) && !empty($plan) && $plan > 0) {
        $expDateObj = new DateTime($paid_date);
        $expDateObj->modify("+{$plan} months");
        $expiration_date = $expDateObj->format('Y-m-d');
    }
    // Update dor and expiration_date in members table
    $stmt2 = $conn->prepare("UPDATE members SET dor=?, plan=?, expiration_date=? WHERE user_id=?");
    $stmt2->bind_param("sisi", $paid_date, $plan, $expiration_date, $user_id);
    $stmt2->execute();
    $stmt2->close();

    $success = "Payment recorded successfully and registration/expiration date updated.";
}

// Fetch payment history
$payments = [];
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM monthlyPayments WHERE user_id=? ORDER BY paid_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Member Payment</h2>
    <?php if ($member): ?>
        <div class="mb-3">
            <strong>Name:</strong> <?= htmlspecialchars($member['fullname']) ?><br>
            <strong>Contact:</strong> <?= htmlspecialchars($member['contact']) ?><br>
            <strong>Current Plan:</strong> <?= htmlspecialchars($member['plan']) ?> Month(s)
        </div>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Plan (Months)</label>
                <select name="plan" class="form-select" required>
                    <option value="">Select Months</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> Month<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Paid Date</label>
                <input type="date" name="paid_date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Record Payment</button>
                <a href="../members.php" class="btn btn-secondary">Back to Members</a>
            </div>
        </form>

        <h4>Payment History</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Plan (Months)</th>
                    <th>Amount</th>
                    <th>Paid Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($payments) === 0): ?>
                    <tr><td colspan="3" class="text-center">No payment records.</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $pay): ?>
                        <tr>
                            <td><?= htmlspecialchars($pay['plan']) ?></td>
                            <td><?= htmlspecialchars($pay['amount']) ?></td>
                            <td><?= htmlspecialchars($pay['paid_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger">Member not found.</div>
    <?php endif; ?>
</div>
</body>
</html>