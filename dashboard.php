<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access your dashboard. <a href='login.php'>Login here</a>");
}

$user_id = $_SESSION['user_id'];

// Fetch user-specific ads
$stmt = $conn->prepare("SELECT * FROM ads WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$user_ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the ad from the database
    $stmt = $conn->prepare("DELETE FROM ads WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $delete_id, 'user_id' => $user_id]);

    echo "Ad deleted successfully! <a href='dashboard.php'>Refresh</a>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome to Your Dashboard</h1>
    <a href="index.php">Back to Homepage</a>
    <a href="post-ad.php">Post a New Ad</a>
    <a href="logout.php">Logout</a>

    <h2>Your Ads</h2>
    <div class="ads-grid">
        <?php if (count($user_ads) > 0): ?>
            <?php foreach ($user_ads as $ad): ?>
                <div class="ad">
                    <img src="<?= htmlspecialchars($ad['image_path']) ?>" alt="Ad Image">
                    <h3><?= htmlspecialchars($ad['title']) ?></h3>
                    <p><?= htmlspecialchars($ad['price']) ?> USD</p>
                    <p><?= htmlspecialchars($ad['category']) ?></p>
                    <p><?= htmlspecialchars($ad['description']) ?></p>
                    <a href="edit-ad.php?ad_id=<?= $ad['id'] ?>">Edit</a>
                    <a href="dashboard.php?delete_id=<?= $ad['id'] ?>" onclick="return confirm('Are you sure you want to delete this ad?');">Delete</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have not posted any ads yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
