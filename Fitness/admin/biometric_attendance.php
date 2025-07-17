<?php
// Endpoint for biometric attendance integration
include '../db/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action']; // 'time_in' or 'time_out'
    $now = date('Y-m-d H:i:s');

    // Check if member exists
    $check = $conn->prepare("SELECT user_id FROM members WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows == 0) {
        echo json_encode(["success" => false, "message" => "Member ID does not exist."]);
        exit;
    }
    $check->close();

    if ($action === 'time_in') {
        // Prevent duplicate time in (if already timed in and not yet timed out)
        $open = $conn->prepare("SELECT id FROM attendance WHERE member_id = ? AND time_out IS NULL");
        $open->bind_param("i", $user_id);
        $open->execute();
        $open->store_result();
        if ($open->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "This member is already timed in and has not timed out yet."]);
            $open->close();
            exit;
        }
        $open->close();
        // Insert a new attendance record with time_in
        $stmt = $conn->prepare("INSERT INTO attendance (member_id, time_in) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $now);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Time In recorded for Member ID $user_id at $now"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error recording Time In."]);
        }
        $stmt->close();
    } elseif ($action === 'time_out') {
        // Update the latest attendance record with time_out
        $stmt = $conn->prepare("UPDATE attendance SET time_out=? WHERE member_id=? AND time_out IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("si", $now, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Time Out recorded for Member ID $user_id at $now"]);
        } else {
            echo json_encode(["success" => false, "message" => "No open Time In found for this member."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
