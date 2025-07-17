<?php
include 'url_restrictrion.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM coach WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: coach.php?msg=deleted");
    exit;
}

// Fetch all coaches
$coaches = [];
$result = $conn->query("SELECT * FROM coach ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $coaches[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness+ Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- nav bar start -->
<?php include 'navbar.php'; ?>
<!-- nav bar end -->

<div class="container mt-5">
    <h2>Coach List</h2>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">Coach deleted successfully.</div>
    <?php endif; ?>
    <a href="action/addCoach.php" class="btn btn-success mb-3">+ Add Coach</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Birth Date</th>
                <th>Specialization</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($coaches) == 0): ?>
                <tr><td colspan="7" class="text-center">No coaches found.</td></tr>
            <?php else: ?>
                <?php foreach ($coaches as $coach): ?>
                    <tr>
                        <td><?= htmlspecialchars($coach['id']) ?></td>
                        <td><?= htmlspecialchars($coach['name']) ?></td>
                        <td><?= htmlspecialchars($coach['birth_date']) ?></td>
                        <td><?= htmlspecialchars($coach['specialization']) ?></td>
                        <td><?= htmlspecialchars($coach['contact_number']) ?></td>
                        <td><?= htmlspecialchars($coach['email']) ?></td>
                        <td>
                            <a href="action/editCoach.php?id=<?= $coach['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="coach.php?delete=<?= $coach['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this coach?');">Delete</a>
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