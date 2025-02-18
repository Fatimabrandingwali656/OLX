<?php
include 'db.php';

// Fetch ads
$stmt = $conn->prepare("SELECT * FROM ads ORDER BY created_at DESC");
$stmt->execute();
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classified Ads</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome to Classified Ads</h1>
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="Search ads...">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Electronics">Electronics</option>
            <option value="Vehicles">Vehicles</option>
        </select>
        <button type="submit">Search</button>
    </form>
    <div class="ads-grid">
        <?php foreach ($ads as $ad): ?>
            <div class="ad">
                <img src="<?= htmlspecialchars($ad['image_path']) ?>" alt="Ad Image">
                <h3><?= htmlspecialchars($ad['title']) ?></h3>
                <p><?= htmlspecialchars($ad['price']) ?> USD</p>
                <a href="contact.php?ad_id=<?= $ad['id'] ?>">Contact Seller</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
