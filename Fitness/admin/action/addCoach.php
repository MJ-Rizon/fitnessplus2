<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Login/admin_login.php");
    exit;
}
include  '../../db/connection.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'];
    $specialization = $_POST['specialization'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    if (!$name || !$birth_date || !$specialization || !$contact_number || !$email) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO coach (name, birth_date, specialization, contact_number, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $birth_date, $specialization, $contact_number, $email);
        $stmt->execute();
        $stmt->close();
        header("Location: ../coach.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Coach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add Coach</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Birth Date</label>
            <input type="date" name="birth_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Specialization</label>
            <input type="text" name="specialization" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="contact_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Coach</button>
        <a href="../coach.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>