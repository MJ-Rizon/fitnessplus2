<?php
session_start();
include '../db/connection.php';

// Check if user is logged in
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: Login/member_login.php");
    exit();
}

// Get username from session
$username = $_SESSION['member_username'];
$member_name = 'Member';
$plan = '';
$dor = '';
$expiration_date = '';
$status = '';

// Fetch member details using username
$stmt = $conn->prepare("SELECT user_id, fullname, dor, plan FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($member_id, $fullname, $dor, $plan);
if ($stmt->fetch()) {
    $member_name = $fullname;
    // Calculate expiration date from dor and plan
    $today = date('Y-m-d');
    $expiration_date = '';
    if (!empty($dor) && !empty($plan) && $plan > 0) {
        $expDateObj = new DateTime($dor);
        $expDateObj->modify("+{$plan} months");
        $expiration_date = $expDateObj->format('Y-m-d');
    }
    $status = (!empty($expiration_date) && $expiration_date >= $today) ? 'Active' : 'Expired';
}
$stmt->close();

// Fetch member's weight and height (assuming columns exist in members table)
$weight = null;
$height = null;
$bmi = null;
$stmt2 = $conn->prepare("SELECT weight, height FROM members WHERE user_id = ?");
$stmt2->bind_param("i", $member_id);
$stmt2->execute();
$stmt2->bind_result($weight, $height);
if ($stmt2->fetch()) {
    // Calculate BMI if both weight and height are available and valid
    if (!is_null($weight) && !is_null($height) && $weight > 0 && $height > 0) {
        $bmi = round($weight / pow($height / 100, 2), 2); // height in cm to m
    }
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness+ Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>

<!-- nav bar start -->
<?php include 'navbar.php'; ?>
<!-- nav bar end -->

<div class="container d-flex justify-content-center align-items-center" style="min-height:80vh;">
    <div class="dashboard-card w-100 text-center">
        <h2 class="welcome-title mb-3">Hi, <?php echo htmlspecialchars($member_name); ?>!</h2>
        <p class="lead">This is your Fitness+ member dashboard.</p>
        <div class="row justify-content-center mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Membership Period</h5>
                        <p class="card-text">
                            <?php
                            // Format dates as "Month day, Year" (e.g., July 4, 2025)
                            $dor_word = !empty($dor) ? date("F j, Y", strtotime($dor)) : '';
                            $expiration_word = !empty($expiration_date) ? date("F j, Y", strtotime($expiration_date)) : '';
                            echo htmlspecialchars($dor_word) . ' to ' . htmlspecialchars($expiration_word);
                            ?>
                        </p>
                        <h6>Status:</h6>
                        <?php if ($status === 'Active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Expired</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Your Body Data</h5>
                        <?php
                        // Show "No record yet" if both are null or both are 0/empty
                        if ((is_null($weight) && is_null($height)) || ($weight === 0 && $height === 0) || ($weight === '' && $height === '')): ?>
                            <div class="text-muted">No record yet</div>
                        <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Weight:</strong>
                                <?php echo (!is_null($weight) && $weight !== '' && $weight > 0) ? htmlspecialchars($weight) . ' kg' : '<span class="text-muted">Not set</span>'; ?>
                            </li>
                            <li class="list-group-item"><strong>Height:</strong>
                                <?php echo (!is_null($height) && $height !== '' && $height > 0) ? htmlspecialchars($height) . ' cm' : '<span class="text-muted">Not set</span>'; ?>
                            </li>
                            <li class="list-group-item"><strong>BMI:</strong>
                                <?php
                                if ($bmi) {
                                    echo htmlspecialchars($bmi);
                                    if ($bmi < 18.5) {
                                        echo ' <span class="badge bg-info text-dark">Underweight</span>';
                                    } elseif ($bmi < 25) {
                                        echo ' <span class="badge bg-success">Normal</span>';
                                    } elseif ($bmi < 30) {
                                        echo ' <span class="badge bg-warning text-dark">Overweight</span>';
                                    } else {
                                        echo ' <span class="badge bg-danger">Obese</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted">Not available</span>';
                                }
                                ?>
                            </li>
                        </ul>

                        <!-- BMI Progress Bar -->
                        <?php if ($bmi): ?>
                        <div class="mt-4">
                            <h6>BMI Insight</h6>
                            <div class="progress" style="height: 30px;">
                                <?php
                                // Calculate percent for progress bar (min 10, max 40)
                                $bmi_percent = min(max(($bmi - 10) * 100 / 30, 0), 100);
                                $bar_class = 'bg-success';
                                if ($bmi < 18.5) {
                                    $bar_class = 'bg-info';
                                } elseif ($bmi < 25) {
                                    $bar_class = 'bg-success';
                                } elseif ($bmi < 30) {
                                    $bar_class = 'bg-warning';
                                } else {
                                    $bar_class = 'bg-danger';
                                }
                                ?>
                                <div class="progress-bar <?php echo $bar_class; ?>" role="progressbar"
                                    style="width: <?php echo $bmi_percent; ?>%;"
                                    aria-valuenow="<?php echo $bmi; ?>" aria-valuemin="10" aria-valuemax="40">
                                    <?php echo htmlspecialchars($bmi); ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between small mt-1">
                                <span>10</span>
                                <span>18.5</span>
                                <span>25</span>
                                <span>30</span>
                                <span>40</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-info">Underweight</span>
                                <span class="text-success">Normal</span>
                                <span class="text-warning">Overweight</span>
                                <span class="text-danger">Obese</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Update Data Button -->
                        <div class="d-flex flex-column align-items-center mt-3 gap-2">
                            <a href="action/update_body.php" class="btn btn-primary mb-2">Update Data</a>
                            <a href="#" class="btn btn-outline-success" id="iot-btn">
                                <i class="bi bi-wifi"></i> IoT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>