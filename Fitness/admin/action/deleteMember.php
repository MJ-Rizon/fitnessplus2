<?php
include '../config.php'; // adjust path as needed

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Delete member from database
    $sql = "DELETE FROM members WHERE user_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect back to members page with success message
        header("Location: ../members.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting member.";
    }
} else {
    echo "Invalid request.";
}
?>