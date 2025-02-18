<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to contact the seller. <a href='login.php'>Login here</a>");
}

$user_id = $_SESSION['user_id'];
$ad_id = isset($_GET['ad_id']) ? (int)$_GET['ad_id'] : 0;

// Fetch ad details
$stmt = $conn->prepare("SELECT * FROM ads WHERE id = :ad_id");
$stmt->execute(['ad_id' => $ad_id]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    die("Ad not found. <a href='index.php'>Go back</a>");
}

// Fetch messages for the ad
$msg_stmt = $conn->prepare("
    SELECT messages.*, users.username AS sender_name
    FROM messages
    JOIN users ON messages.sender_id = users.id
    WHERE ad_id = :ad_id
    ORDER BY sent_at ASC
");
$msg_stmt->execute(['ad_id' => $ad_id]);
$messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $receiver_id = $ad['user_id']; // The seller's ID

    if (!empty($message)) {
        $insert_stmt = $conn->prepare("
            INSERT INTO messages (sender_id, receiver_id, ad_id, message) 
            VALUES (:sender_id, :receiver_id, :ad_id, :message)
        ");
        $insert_stmt->execute([
            'sender_id' => $user_id,
            'receiver_id' => $receiver_id,
            'ad_id' => $ad_id,
            'message' => $message,
        ]);

        // Reload to show the new message
        header("Location: contact.php?ad_id=$ad_id");
        exit;
    } else {
        echo "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Seller</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Contact Seller</h1>
    <a href="index.php">Back to Homepage</a>

    <!-- Ad Details -->
    <div class="ad-details">
        <h2><?= htmlspecialchars($ad['title']) ?></h2>
        <p><strong>Price:</strong> <?= htmlspecialchars($ad['price']) ?> USD</p>
        <p><strong>Description:</strong> <?= htmlspecialchars($ad['description']) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($ad['category']) ?></p>
    </div>

    <!-- Messages -->
    <h2>Messages</h2>
    <div class="messages">
        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <p><strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?></p>
                    <small>Sent at: <?= htmlspecialchars($msg['sent_at']) ?></small>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages yet. Be the first to contact the seller!</p>
        <?php endif; ?>
    </div>

    <!-- Message Form -->
    <form method="POST">
        <textarea name="message" rows="4" placeholder="Write your message..." required></textarea>
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
