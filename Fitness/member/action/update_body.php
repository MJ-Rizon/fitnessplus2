<?php
session_start();
include '../../db/connection.php';

// Check if user is logged in
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: ../Login/member_login.php");
    exit();
}

$username = $_SESSION['member_username'];
$errors = [];

// Fetch the current weight and height of the logged-in member
$stmt3 = $conn->prepare("SELECT weight, height FROM members WHERE username = ?");
$stmt3->bind_param("s", $username);
$stmt3->execute();
$stmt3->bind_result($current_weight, $current_height);
$stmt3->fetch();
$stmt3->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = isset($_POST['weight']) ? trim($_POST['weight']) : null;
    $height = isset($_POST['height']) ? trim($_POST['height']) : null;

    // Input validation
    if (!is_numeric($weight) || $weight <= 0) {
        $errors[] = "Weight must be a positive number.";
    }
    if (!is_numeric($height) || $height <= 0) {
        $errors[] = "Height must be a positive number.";
    }

    if (empty($errors)) {
        // Update member's weight and height
        $stmt = $conn->prepare("UPDATE members SET weight = ?, height = ? WHERE username = ?");
        $stmt->bind_param("dds", $weight, $height, $username);
        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['update_success'] = "Your data has been updated successfully!";
            header("Location: ../dashboard.php");
            exit();
        } else {
            $errors[] = "Failed to update data.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Body Data - Fitness+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="mb-4 text-center">Update Your Body Data</h3>

                    <!-- Display current data -->
                    <div class="alert alert-info">
                        <strong>Your current data:</strong><br>
                        <ul class="mb-0">
                            <li>Weight: <?php echo (!is_null($current_weight) && $current_weight > 0) ? htmlspecialchars($current_weight) . ' kg' : '<span class="text-muted">Not set</span>'; ?></li>
                            <li>Height: <?php echo (!is_null($current_height) && $current_height > 0) ? htmlspecialchars($current_height) . ' cm' : '<span class="text-muted">Not set</span>'; ?></li>
                        </ul>
                    </div>

                    <!-- Display errors -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Update form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="weight" name="weight"
                                   value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ( (!is_null($current_weight) && $current_weight > 0) ? htmlspecialchars($current_weight) : '' ); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="height" class="form-label">Height (cm)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="height" name="height"
                                   value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ( (!is_null($current_height) && $current_height > 0) ? htmlspecialchars($current_height) : '' ); ?>" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Data</button>
                            <a href="../dashboard.php" class="btn btn-secondary mt-2">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
