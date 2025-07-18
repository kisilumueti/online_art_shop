<?php
session_start();
require_once __DIR__ . '/../db.php';

// Require admin role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all artists for selection
$artists = $pdo->query("SELECT id, name FROM users WHERE role='artist'")->fetchAll();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id   = $_POST['artist_id'] ?? null;
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $category    = trim($_POST['category']);
    $status      = in_array($_POST['status'], ['available', 'sold', 'reserved', 'hidden']) ? $_POST['status'] : 'available';
    $slug        = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title)) . '-' . time();

    if (!$artist_id || !$title || !$price || !$stock) {
        $errors[] = "Artist, Title, Price and Stock are required.";
    }

    if (!is_numeric($price) || $price <= 0) $errors[] = "Price must be a positive number.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "Stock must be 0 or higher.";

    // Handle file upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $filename = uniqid('art_', true) . '.' . $ext;
            $target = $upload_dir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = "Failed to upload image.";
            } else {
                $image_path = 'uploads/' . $filename;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO artworks (artist_id, title, slug, description, price, stock, category, image_path, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $artist_id, $title, $slug, $description, $price, $stock, $category, $image_path, $status
        ]);
        $success = "Artwork uploaded successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Artwork - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-800 text-white p-4 flex justify-between">
    <div class="text-lg font-bold">Admin Dashboard</div>
    <div>
        <a href="dashboard.php" class="px-3 hover:underline">Dashboard</a>
        <a href="manage_users.php" class="px-3 hover:underline">Manage Users</a>
        <a href="manage_art.php" class="px-3 hover:underline">Manage Art</a>
        <a href="../logout.php" class="px-3 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Upload New Artwork</h1>

    <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
        <?php foreach ($errors as $err) echo htmlspecialchars($err) . "<br>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 shadow rounded">
        <div>
            <label class="block text-gray-700">Artist</label>
            <select name="artist_id" required class="w-full border rounded p-2">
                <option value="">Select Artist</option>
                <?php foreach ($artists as $artist): ?>
                    <option value="<?php echo $artist['id']; ?>" <?php if (!empty($_POST['artist_id']) && $_POST['artist_id'] == $artist['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($artist['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-gray-700">Title</label>
            <input name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-gray-700">Description</label>
            <textarea name="description" class="w-full border rounded p-2"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700">Price ($)</label>
                <input name="price" type="number" step="0.01" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-gray-700">Stock</label>
                <input name="stock" type="number" required value="<?php echo htmlspecialchars($_POST['stock'] ?? '1'); ?>" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-gray-700">Category</label>
                <input name="category" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>" class="w-full border rounded p-2">
            </div>
        </div>

        <div>
            <label class="block text-gray-700">Status</label>
            <select name="status" class="w-full border rounded p-2">
                <option value="available">Available</option>
                <option value="sold">Sold</option>
                <option value="reserved">Reserved</option>
                <option value="hidden">Hidden</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700">Upload Image</label>
            <input type="file" name="image" class="w-full">
        </div>

        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Upload Artwork</button>
    </form>
</div>
</body>
</html>
