<?php
include 'url_restrictrion.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $service = $_POST['service'];
    $amount = $_POST['amount'];
    $paid_date = $_POST['paid_date'];

    $stmt = $conn->prepare("INSERT INTO walkin_payments (fullname, service, amount, paid_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $fullname, $service, $amount, $paid_date);
    $stmt->execute();
    $stmt->close();

    $success = "Walk-in payment recorded successfully.";
}

// Fetch rates for dropdown
$rates = [];
$result = $conn->query("SELECT name, charge FROM rates");
while ($row = $result->fetch_assoc()) {
    $rates[] = $row;
}

// Fetch all walk-in payments
$walkins = [];
$result = $conn->query("SELECT * FROM walkin_payments ORDER BY paid_date DESC, id DESC");
while ($row = $result->fetch_assoc()) {
    $walkins[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walk-in Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Walk-in Payment</h2>
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <form method="post" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Service</label>
            <select name="service" id="service" class="form-select" required>
                <option value="">Select Service</option>
                <?php foreach ($rates as $rate): ?>
                    <option value="<?= htmlspecialchars($rate['name']) ?>" data-charge="<?= $rate['charge'] ?>">
                        <?= htmlspecialchars($rate['name']) ?> (₱<?= $rate['charge'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Paid Date</label>
            <input type="date" name="paid_date" class="form-control" required value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Record Payment</button>
        </div>
    </form>

    <h3>All Walk-in Payments</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Paid Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($walkins) === 0): ?>
                <tr><td colspan="4" class="text-center">No walk-in payments yet.</td></tr>
            <?php else: ?>
                <?php foreach ($walkins as $w): ?>
                    <tr>
                        <td><?= htmlspecialchars($w['fullname']) ?></td>
                        <td><?= htmlspecialchars($w['service']) ?></td>
                        <td>₱<?= htmlspecialchars($w['amount']) ?></td>
                        <td><?= htmlspecialchars($w['paid_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('service').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var charge = selected.getAttribute('data-charge');
    if (charge) {
        document.getElementById('amount').value = charge;
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>