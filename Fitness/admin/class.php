<?php
include 'url_restrictrion.php';

// Delete a member from a class (admin action)
if (isset($_GET['remove_member']) && isset($_GET['class_id'])) {
    $remove_member_id = intval($_GET['remove_member']);
    $class_id = intval($_GET['class_id']);
    $stmt = $conn->prepare("DELETE FROM class_members WHERE class_id=? AND member_id=?");
    $stmt->bind_param("ii", $class_id, $remove_member_id);
    $stmt->execute();
    $stmt->close();
    header("Location: class.php");
    exit;
}

// Fetch all classes
$schedules = [];
$result = $conn->query("SELECT cs.*, c.name as coach_name FROM class_schedule cs JOIN coach c ON cs.coach_id = c.id ORDER BY cs.day_of_week, cs.start_time");
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Class List & Manage Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- nav bar start -->
<?php include 'navbar.php'; ?>
<!-- nav bar end -->

<div class="container mt-5">
    <h2>Class Schedule</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Coach</th>
                <th>Day</th>
                <th>Start</th>
                <th>End</th>
                <th>Location</th>
                <th>Members Joined</th>
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
                            <ul class="mb-0 list-unstyled">
                            <?php
                            $mid = $sched['id'];
                            $memres = $conn->query("SELECT m.user_id, m.fullname FROM class_members cm JOIN members m ON cm.member_id = m.user_id WHERE cm.class_id = $mid");
                            $hasMembers = false;
                            while ($m = $memres->fetch_assoc()) {
                                $hasMembers = true;
                                echo '<li class="d-flex justify-content-between align-items-center mb-1">'
                                    . '<span>' . htmlspecialchars($m['fullname']) . '</span>'
                                    . '<a href="class.php?remove_member=' . $m['user_id'] . '&class_id=' . $mid . '" class="btn btn-danger btn-sm ms-2" onclick="return confirm(\'Remove this member from class?\')">Delete</a>'
                                    . '</li>';
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
</body>
</html>