<?php
session_start();
require_once __DIR__ . '/../db.php';

// Validate session & role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['artist', 'customer'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Track login time (or fallback to now if missing)
if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
}
$since = $_SESSION['login_time'];

// Fetch artworks added after login time
$stmt = $pdo->prepare("
    SELECT a.*, u.name AS artist_name
    FROM artworks a
    JOIN users u ON a.artist_id = u.id
    WHERE a.created_at >= ? 
    ORDER BY a.created_at DESC
");
$stmt->execute([$since]);
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-800 text-white p-4 flex justify-between items-center">
    <div class="text-lg font-bold">Notifications</div>
    <div class="flex space-x-4 items-center">
        <a href="dashboard.php" class="hover:underline">Dashboard</a>
        <a href="view_artworks.php" class="hover:underline">View Artworks</a>
        <a href="buy_art.php" class="hover:underline">Buy Art</a>
        <a href="my_orders.php" class="hover:underline">My Orders</a>
        <a href="../logout.php" class="hover:underline">Logout</a>
        <img src="../logo.png" alt="Logo" class="h-8 ml-4">
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">New Artworks Since Login</h1>

    <?php if (empty($notifications)): ?>
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
            No new artworks added since your last login.
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($notifications as $art): ?>
                <div class="bg-white shadow rounded p-4 flex items-center">
                    <?php if ($art['image_path']): ?>
                        <img src="../<?php echo htmlspecialchars($art['image_path']); ?>" class="h-16 w-16 object-cover rounded mr-4">
                    <?php else: ?>
                        <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center mr-4">N/A</div>
                    <?php endif; ?>
                    <div>
                        <h2 class="text-lg font-bold"><?php echo htmlspecialchars($art['title']); ?></h2>
                        <p class="text-sm text-gray-600">by <?php echo htmlspecialchars($art['artist_name']); ?></p>
                        <p class="text-sm text-gray-600">Added: <?php echo $art['created_at']; ?></p>
                        <a href="view_artworks.php" class="text-blue-600 hover:underline text-sm">View Artwork</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
