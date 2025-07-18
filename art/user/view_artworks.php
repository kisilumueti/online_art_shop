<?php
session_start();
require_once __DIR__ . '/../db.php';

// Verify session and role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['artist', 'customer'])) {
    header("Location: ../login.php");
    exit;
}

// fetch artworks with artist name
$artworks = $pdo->query("
    SELECT a.*, u.name as artist_name
    FROM artworks a
    JOIN users u ON a.artist_id = u.id
    WHERE a.status = 'available'
    ORDER BY a.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Artworks - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- Navbar -->
<nav class="bg-blue-800 text-white p-4 flex justify-between items-center">
    <div class="text-lg font-bold">Available Artworks</div>
    <div class="flex space-x-4 items-center">
        <a href="dashboard.php" class="hover:underline">Dashboard</a>
        <a href="notifications.php" class="hover:underline">Notifications</a>
        <a href="buy_art.php" class="hover:underline">Buy Art</a>
        <a href="my_orders.php" class="hover:underline">My Orders</a>
        <a href="../logout.php" class="hover:underline">Logout</a>
        <img src="../logo.png" alt="Logo" class="h-8 ml-4">
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Browse & Buy Artworks</h1>

    <?php if (!$artworks): ?>
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
            No artworks available at the moment.
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($artworks as $art): ?>
                <div class="bg-white rounded shadow hover:shadow-lg transition">
                    <?php if ($art['image_path']): ?>
                        <img src="../<?php echo htmlspecialchars($art['image_path']); ?>" class="h-48 w-full object-cover rounded-t">
                    <?php else: ?>
                        <div class="h-48 flex items-center justify-center bg-gray-200 rounded-t">No Image</div>
                    <?php endif; ?>
                    <div class="p-4">
                        <h2 class="text-lg font-bold"><?php echo htmlspecialchars($art['title']); ?></h2>
                        <p class="text-sm text-gray-600">by <?php echo htmlspecialchars($art['artist_name']); ?></p>
                        <p class="mt-2 text-blue-700 font-semibold">$<?php echo number_format($art['price'], 2); ?></p>
                        <p class="text-gray-500 text-sm">Stock: <?php echo $art['stock']; ?></p>
                        <a href="buy_art.php?artwork_id=<?php echo $art['id']; ?>" 
                            class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Buy</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
