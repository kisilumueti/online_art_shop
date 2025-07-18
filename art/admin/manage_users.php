<?php
session_start();
require_once __DIR__ . '/../db.php';

// Require admin role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    if ($delete_id !== $_SESSION['user_id']) { // cannot delete self
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: manage_users.php");
        exit;
    }
}

// Fetch users
$users = $pdo->query("SELECT * FROM users ORDER BY role, created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-800 text-white p-4 flex justify-between">
    <div class="text-lg font-bold">Admin Dashboard</div>
    <div>
        <a href="dashboard.php" class="px-3 hover:underline">Dashboard</a>
        <a href="upload_art.php" class="px-3 hover:underline">Upload Art</a>
        <a href="manage_art.php" class="px-3 hover:underline">Manage Art</a>
        <a href="../logout.php" class="px-3 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Users</h1>

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-200 text-gray-600">
                    <th class="p-2">ID</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Role</th>
                    <th class="p-2">Phone</th>
                    <th class="p-2">Address</th>
                    <th class="p-2">Created</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2"><?php echo $user['id']; ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="p-2 capitalize"><?php echo $user['role']; ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($user['address']); ?></td>
                    <td class="p-2"><?php echo $user['created_at']; ?></td>
                    <td class="p-2">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            |
                            <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="text-red-600 hover:underline"
                            onclick="return confirm('Delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
