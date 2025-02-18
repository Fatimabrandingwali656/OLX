<?php
include 'db.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build the SQL query dynamically based on user input
$sql = "SELECT * FROM ads WHERE 1=1";

$params = [];

// Add conditions for search query
if (!empty($query)) {
    $sql .= " AND title LIKE :query";
    $params['query'] = '%' . $query . '%';
}

// Add conditions for category filter
if (!empty($category)) {
    $sql .= " AND category = :category";
    $params['category'] = $category;
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Search Results</h1>
    <a href="index.php">Back to Homepage</a>
    <div class="ads-grid">
        <?php if (count($results) > 0): ?>
            <?php foreach ($results as $ad): ?>
                <div class="ad">
                    <img src="<?= htmlspecialchars($ad['image_path']) ?>" alt="Ad Image">
                    <h3><?= htmlspecialchars($ad['title']) ?></h3>
                    <p><?= htmlspecialchars($ad['price']) ?> USD</p>
                    <p>Category: <?= htmlspecialchars($ad['category']) ?></p>
                    <a href="contact.php?ad_id=<?= $ad['id'] ?>">Contact Seller</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No results found for your search.</p>
        <?php endif; ?>
    </div>
</body>
</html>
