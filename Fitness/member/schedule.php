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

// Fetch member details using username
$stmt = $conn->prepare("SELECT user_id, fullname FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($member_id, $fullname);
if ($stmt->fetch()) {
    $member_name = $fullname;
}
$stmt->close();



// Fetch member dor and plan for expiration calculation
$status = '';
$dor = '';
$plan = '';
$exp_stmt = $conn->prepare("SELECT dor, plan FROM members WHERE user_id = ?");
$exp_stmt->bind_param("i", $member_id);
$exp_stmt->execute();
$exp_stmt->bind_result($dor, $plan);
if ($exp_stmt->fetch()) {
    $today = date('Y-m-d');
    $expiration_date = '';
    if (!empty($dor) && !empty($plan) && $plan > 0) {
        $expDateObj = new DateTime($dor);
        $expDateObj->modify("+{$plan} months");
        $expiration_date = $expDateObj->format('Y-m-d');
    }
    $status = (!empty($expiration_date) && $expiration_date >= $today) ? 'Active' : 'Expired';
}
$exp_stmt->close();

// Fetch all class schedules
$schedules = [];
$result = $conn->query("SELECT cs.*, c.name as coach_name FROM class_schedule cs JOIN coach c ON cs.coach_id = c.id ORDER BY cs.day_of_week, cs.start_time");
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

// Fetch classes joined by this member
$joined = [];
$join_result = $conn->query("SELECT class_id FROM class_members WHERE member_id = $member_id");
while ($row = $join_result->fetch_assoc()) {
    $joined[] = $row['class_id'];
}

// Handle join/unjoin actions
function join_class($conn, $class_id, $member_id) {
    $stmt = $conn->prepare("INSERT IGNORE INTO class_members (class_id, member_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $class_id, $member_id);
    $stmt->execute();
    $stmt->close();
}
function unjoin_class($conn, $class_id, $member_id) {
    $stmt = $conn->prepare("DELETE FROM class_members WHERE class_id=? AND member_id=?");
    $stmt->bind_param("ii", $class_id, $member_id);
    $stmt->execute();
    $stmt->close();
}
if ($member_id && isset($_GET['join'])) {
    $class_id = intval($_GET['join']);
    join_class($conn, $class_id, $member_id);
    header("Location: schedule.php");
    exit;
}
if ($member_id && isset($_GET['unjoin'])) {
    $class_id = intval($_GET['unjoin']);
    unjoin_class($conn, $class_id, $member_id);
    header("Location: schedule.php");
    exit;
}

// --- Auto-unjoin if member's membership is expired or if inside the class (current time is within class time) ---
date_default_timezone_set('Asia/Manila'); // Set to your timezone
$now = date('H:i:s');
$today_day = date('l'); // e.g., Monday
foreach ($schedules as $sched) {
    if (in_array($sched['id'], $joined)) {
        // Auto-unjoin if membership expired
        if ($status === 'Expired') {
            unjoin_class($conn, $sched['id'], $member_id);
            $joined = array_diff($joined, [$sched['id']]);
            continue;
        }
        // Or auto-unjoin if today and now is within class time
        if (strtolower($sched['day_of_week']) === strtolower($today_day)
            && $now >= $sched['start_time'] && $now <= $sched['end_time']) {
            unjoin_class($conn, $sched['id'], $member_id);
            $joined = array_diff($joined, [$sched['id']]);
        }
    }
}
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

<div class="container mt-5">
    <h2 class="mb-4">Class Schedule</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Coach</th>
                <th>Day</th>
                <th>Start</th>
                <th>End</th>
                <th>Location</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($schedules) == 0): ?>
                <tr><td colspan="7" class="text-center">No classes scheduled.</td></tr>
            <?php else: ?>
                <?php foreach ($schedules as $sched): ?>
                    <tr>
                        <td><?= htmlspecialchars($sched['class_name']) ?></td>
                        <td><?= htmlspecialchars($sched['coach_name']) ?></td>
                        <td><?= htmlspecialchars($sched['day_of_week']) ?></td>
                        <td><?= htmlspecialchars(date("g:i A", strtotime($sched['start_time']))) ?></td>
                        <td><?= htmlspecialchars(date("g:i A", strtotime($sched['end_time']))) ?></td>
                        <td><?= htmlspecialchars($sched['location']) ?></td>
                        <td>
                            <?php if (in_array($sched['id'], $joined)): ?>
                                <a href="schedule.php?unjoin=<?= $sched['id'] ?>" class="btn btn-danger btn-sm">Unjoin</a>
                            <?php else: ?>
                                <?php if ($status === 'Expired'): ?>
                                    <button class="btn btn-secondary btn-sm" disabled title="Membership expired. Renew to join classes.">Join</button>
                                <?php else: ?>
                                    <a href="schedule.php?join=<?= $sched['id'] ?>" class="btn btn-success btn-sm">Join</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- Button to show members joined -->
                            <button class="btn btn-info btn-sm mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#members-<?= $sched['id'] ?>">
                                Show Members
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="members-<?= $sched['id'] ?>">
                        <td colspan="7" class="bg-light">
                            <strong>Members Joined:</strong>
                            <ul class="mb-0">
                            <?php
                            $mid = $sched['id'];
                            $memres = $conn->query("SELECT m.fullname FROM class_members cm JOIN members m ON cm.member_id = m.user_id WHERE cm.class_id = $mid");
                            $hasMembers = false;
                            while ($m = $memres->fetch_assoc()) {
                                $hasMembers = true;
                                echo '<li>' . htmlspecialchars($m['fullname']) . '</li>';
                            }
                            if (!$hasMembers) {
                                echo '<li class="text-muted">None</li>';
                            }
                            ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>