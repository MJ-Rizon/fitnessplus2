<?php
session_start();
include '../db/connection.php';

// Require member login
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: Login/member_login.php");
    exit();
}

$username = $_SESSION['member_username'];


// Fetch member's data for context, now including membership period (calculate expiration from dor and plan)
$stmt = $conn->prepare("SELECT fullname, weight, height, ini_bodytype, curr_bodytype, dor, plan FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($fullname, $weight, $height, $ini_bodytype, $curr_bodytype, $dor, $plan);
$stmt->fetch();
$stmt->close();

// Calculate expiration date from dor and plan
$expiration_date = '';
if (!empty($dor) && !empty($plan) && $plan > 0) {
    $expDateObj = new DateTime($dor);
    $expDateObj->modify("+{$plan} months");
    $expiration_date = $expDateObj->format('Y-m-d');
}

$context = "The user is a gym member named $fullname. Their current weight is $weight kg, height is $height cm.";
if ($ini_bodytype || $curr_bodytype) {
    $context .= " They started with a body type of '$ini_bodytype' and are now '$curr_bodytype'.";
}
$now = date('Y-m-d');
if ($dor && $expiration_date) {
    $context .= " Their membership period is from $dor to $expiration_date. If the user asks about their membership deadline, refer to $expiration_date as the deadline for their current monthly membership.";
    if ($expiration_date < $now) {
        $context .= " The user's membership is already expired. If the user asks about their membership, deadline, or tries to access any services, politely inform them that their membership has expired and they need to renew their membership at Villa Fitness Gym to access the services.";
        $context .= " Do not provide any workout plans, diet advice, or other services until the membership is renewed.";
    } else {
        $context .= " You are a helpful fitness coach chatbot. You can provide workout plans (easy, medium, hard), diet advice, help the user reach their fitness goal, and motivate them. Always personalize your answer and do not give medical advice.";
    }
} else {
    $context .= " You are a helpful fitness coach chatbot. You can provide workout plans (easy, medium, hard), diet advice, help the user reach their fitness goal, and motivate them. Always personalize your answer and do not give medical advice.";
}

// Initialize chat history in session if not yet present
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// If a message was sent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $user_message = trim($_POST['message']);
    if (!empty($user_message)) {
        // Add user message to chat history
        $_SESSION['chat_history'][] = ["role" => "user", "content" => $user_message];

        // Build the messages array to send: system + previous chat + new user input
        $messages = [
            ["role" => "system", "content" => $context],
        ];
        foreach ($_SESSION['chat_history'] as $entry) {
            $messages[] = $entry;
        }

        // Call Ollama API (local)
        $data = [
            "model" => "llama3", // or another model you have pulled with Ollama
            "messages" => $messages,
        ];

        $ch = curl_init("http://localhost:11434/api/chat");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);

        if ($response === false) {
            error_log("cURL error: " . curl_error($ch));
            $bot_reply = "Sorry, there was an error connecting to the chatbot service.";
        } else {
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_status !== 200) {
                error_log("HTTP status $http_status: $response");
                $bot_reply = "Sorry, the chatbot service returned an error.";
            } else {
                error_log("Ollama API raw response: " . $response);
                // Handle streaming response: concatenate all message contents
                $bot_reply = '';
                $lines = explode("\n", $response);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $result = json_decode($line, true);
                    if (isset($result['message']['content'])) {
                        $bot_reply .= $result['message']['content'];
                    }
                }
                if (empty($bot_reply)) {
                    $bot_reply = "[DEBUG] Full Ollama response: " . htmlspecialchars($response);
                }
            }
        }
        curl_close($ch);

        // Add bot reply to chat history
        $_SESSION['chat_history'][] = ["role" => "assistant", "content" => $bot_reply];
    }
}

// Add this logic before displaying chat history
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_history'])) {
    $_SESSION['chat_history'] = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Prepare chat history for display
$chat_history = $_SESSION['chat_history'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AI Chatbot - Fitness+</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/chatbot.css">
</head>

<body>

    <!-- nav bar start -->
    <?php include 'navbar.php'; ?>
    <!-- nav bar end -->

    <div class="container mt-5" style="max-width:600px;">
        <h2 class="mb-4 text-center">AI Chatbot</h2>
        <div class="d-flex justify-content-end mb-2">
            <form method="post" style="display:inline;">
                <input type="hidden" name="clear_history" value="1">
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Clear</button>
            </form>
        </div>
        <div class="card mb-3" id="chat-card">
            <div class="card-body p-3" id="chat-messages">
                <?php if (!empty($chat_history)): ?>
                    <?php foreach ($chat_history as $chat): ?>
                        <?php if ($chat['role'] === 'user'): ?>
                            <div class="chat-bubble user ms-auto text-end d-flex flex-row-reverse">
                                <?php echo htmlspecialchars($chat['content']); ?>
                            </div>
                        <?php elseif ($chat['role'] === 'assistant'): ?>
                            <div class="chat-bubble bot me-auto text-start">
                                <?php echo nl2br(htmlspecialchars($chat['content'])); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted text-center">Start the conversation!<br>
                        <small>Try asking about workouts, diet, goals, or motivation.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <form method="post" class="mb-3 sticky-bottom" id="chat-form" autocomplete="off">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Type your message..." required autofocus>
                <button class="btn btn-success" type="submit"><i class="bi bi-send"></i></button>
            </div>
        </form>
    </div>
    <script>
    // Scroll chat to bottom on load and after sending
    function scrollChatToBottom() {
        var chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
    window.onload = scrollChatToBottom;
    document.getElementById('chat-form').onsubmit = function() {
        setTimeout(scrollChatToBottom, 100);
    };
    </script>
</body>
</html>
