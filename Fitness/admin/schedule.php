<?php
include 'url_restrictrion.php';

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM class_schedule WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: class.php?msg=deleted");
    exit;
}

// Handle add/edit
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = $_POST['class_name'];
    $coach_id = $_POST['coach_id'];
    $day_of_week = $_POST['day_of_week']; // e.g. Monday, Tuesday, etc.
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = $_POST['location'];

    if (!$class_name || !$coach_id || !$day_of_week || !$start_time || !$end_time) {
        $error = "All fields except location are required.";
    } else {
        if (isset($_POST['edit_id']) && $_POST['edit_id']) {
            // Edit
            $edit_id = intval($_POST['edit_id']);
            $stmt = $conn->prepare("UPDATE class_schedule SET class_name=?, coach_id=?, day_of_week=?, start_time=?, end_time=?, location=? WHERE id=?");
            $stmt->bind_param("sissssi", $class_name, $coach_id, $day_of_week, $start_time, $end_time, $location, $edit_id);
            $stmt->execute();
            $stmt->close();
            header("Location: class.php?msg=updated");
            exit;
        } else {
            // Add
            $stmt = $conn->prepare("INSERT INTO class_schedule (class_name, coach_id, day_of_week, start_time, end_time, location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissss", $class_name, $coach_id, $day_of_week, $start_time, $end_time, $location);
            $stmt->execute();
            $stmt->close();
            header("Location: class.php?msg=added");
            exit;
        }
    }
}

// Fetch coaches for dropdown
$coaches = [];
$result = $conn->query("SELECT * FROM coach ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $coaches[] = $row;
}

// Fetch all schedules
$schedules = [];
$result = $conn->query("SELECT cs.*, c.name as coach_name FROM class_schedule cs JOIN coach c ON cs.coach_id = c.id ORDER BY cs.day_of_week, cs.start_time");
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

// For editing
$edit = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM class_schedule WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    $stmt->close();
    $edit = true;
}

// Days of the week array
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Class Scheduling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- nav bar start -->
<?php include 'navbar.php'; ?>
<!-- nav bar end -->

<div class="container mt-5">
    <h2>Class Scheduling</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['msg'] == 'added') echo "Class scheduled successfully!";
            if ($_GET['msg'] == 'updated') echo "Class updated successfully!";
            if ($_GET['msg'] == 'deleted') echo "Class deleted successfully!";
            ?>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3 mb-4">
        <?php if ($edit && $edit_data): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        <div class="col-md-3">
            <label class="form-label">Class Name</label>
            <input type="text" name="class_name" class="form-control" required value="<?= $edit && $edit_data ? htmlspecialchars($edit_data['class_name']) : '' ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Coach</label>
            <select name="coach_id" class="form-select" required>
                <option value="">Select Coach</option>
                <?php foreach ($coaches as $coach): ?>
                    <option value="<?= $coach['id'] ?>" <?= ($edit && $edit_data && $edit_data['coach_id'] == $coach['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($coach['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Day</label>
            <select name="day_of_week" class="form-select" required>
                <option value="">Select Day</option>
                <?php foreach ($days as $day): ?>
                    <option value="<?= $day ?>" <?= ($edit && $edit_data && $edit_data['day_of_week'] == $day) ? 'selected' : '' ?>>
                        <?= $day ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Start Time</label>
            <input type="time" name="start_time" class="form-control" required value="<?= $edit && $edit_data ? htmlspecialchars($edit_data['start_time']) : '' ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">End Time</label>
            <input type="time" name="end_time" class="form-control" required value="<?= $edit && $edit_data ? htmlspecialchars($edit_data['end_time']) : '' ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="<?= $edit && $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?= $edit ? 'Update' : 'Add' ?> Class</button>
            <?php if ($edit): ?>
                <a href="class.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <h3>Scheduled Classes</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Coach</th>
                <th>Day</th>
                <th>Start</th>
                <th>End</th>
                <th>Location</th>
                <th>Actions</th>
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
                            <a href="class.php?edit=<?= $sched['id'] ?>" class="btn btn-info btn-sm">View Member</a>
                            <a href="class.php?delete=<?= $sched['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this class?');">Delete</a>
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