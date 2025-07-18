<?php
session_start();
require_once __DIR__ . '/../db.php';

// Require admin role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Delete artwork
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: manage_art.php");
    exit;
}

// Fetch all artworks with artist name
$artworks = $pdo->query("
    SELECT a.*, u.name as artist_name 
    FROM artworks a 
    JOIN users u ON a.artist_id = u.id 
    ORDER BY a.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Artworks - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-800 text-white p-4 flex justify-between">
    <div class="text-lg font-bold">Admin Dashboard</div>
    <div>
        <a href="dashboard.php" class="px-3 hover:underline">Dashboard</a>
        <a href="upload_art.php" class="px-3 hover:underline">Upload Art</a>
        <a href="manage_users.php" class="px-3 hover:underline">Manage Users</a>
        <a href="../logout.php" class="px-3 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Artworks</h1>

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-200 text-gray-600">
                    <th class="p-2">ID</th>
                    <th class="p-2">Image</th>
                    <th class="p-2">Title</th>
                    <th class="p-2">Artist</th>
                    <th class="p-2">Price</th>
                    <th class="p-2">Stock</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Created</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($artworks as $art): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2"><?php echo $art['id']; ?></td>
                    <td class="p-2">
                        <?php if ($art['image_path']): ?>
                            <img src="../<?php echo htmlspecialchars($art['image_path']); ?>" class="h-10">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td class="p-2"><?php echo htmlspecialchars($art['title']); ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($art['artist_name']); ?></td>
                    <td class="p-2">$<?php echo number_format($art['price'],2); ?></td>
                    <td class="p-2"><?php echo $art['stock']; ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($art['category']); ?></td>
                    <td class="p-2 capitalize"><?php echo $art['status']; ?></td>
                    <td class="p-2"><?php echo $art['created_at']; ?></td>
                    <td class="p-2">
                        <a href="edit_art.php?id=<?php echo $art['id']; ?>" class="text-blue-600 hover:underline">Edit</a> |
                        <a href="manage_art.php?delete=<?php echo $art['id']; ?>" class="text-red-600 hover:underline"
                           onclick="return confirm('Delete this artwork?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
