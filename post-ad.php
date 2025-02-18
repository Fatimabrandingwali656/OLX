<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to post an ad. <a href='login.php'>Login here</a>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            // Insert ad data into the database
            $stmt = $conn->prepare("INSERT INTO ads (user_id, title, description, price, category, image_path) 
                                    VALUES (:user_id, :title, :description, :price, :category, :image_path)");
            $stmt->execute([
                'user_id' => $user_id,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'image_path' => $filePath
            ]);

            echo "Ad posted successfully! <a href='index.php'>View Ads</a>";
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "Please upload a valid image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Ad</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Post an Ad</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="number" name="price" placeholder="Price" required>
        <select name="category" required>
            <option value="Electronics">Electronics</option>
            <option value="Vehicles">Vehicles</option>
            <option value="Furniture">Furniture</option>
            <option value="Real Estate">Real Estate</option>
            <option value="Others">Others</option>
        </select>
        <input type="file" name="image" required>
        <button type="submit">Post Ad</button>
    </form>
</body>
</html>
