<?php
session_start();
require_once __DIR__ . '/../db.php';

// Verify session and role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['artist', 'customer'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- Navbar -->
<nav class="bg-blue-800 text-white p-4 flex justify-between items-center">
    <div class="text-lg font-bold">Welcome, <?php echo ucfirst(htmlspecialchars($role)); ?></div>
    <div class="flex space-x-4 items-center">
        <a href="dashboard.php" class="hover:underline">Dashboard</a>
        <a href="view_artworks.php" class="hover:underline">View Artworks</a>
        <a href="notifications.php" class="hover:underline">Notifications</a>
        <a href="buy_art.php" class="hover:underline">Buy Art</a>
        <a href="my_orders.php" class="hover:underline">My Orders</a>
        <a href="../logout.php" class="hover:underline">Logout</a>
        <img src="../logo.png" alt="Logo" class="h-8 ml-4">
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">User Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="view_artworks.php" class="bg-white p-6 shadow rounded text-center hover:bg-blue-50">
            <h2 class="text-xl text-gray-700 mb-2">Browse Artworks</h2>
            <p>Explore available art and make purchases.</p>
        </a>
        <a href="notifications.php" class="bg-white p-6 shadow rounded text-center hover:bg-blue-50">
            <h2 class="text-xl text-gray-700 mb-2">Notifications</h2>
            <p>See updates and new art alerts.</p>
        </a>
        <a href="buy_art.php" class="bg-white p-6 shadow rounded text-center hover:bg-blue-50">
            <h2 class="text-xl text-gray-700 mb-2">Buy Art</h2>
            <p>Start purchasing your favorite pieces.</p>
        </a>
        <a href="my_orders.php" class="bg-white p-6 shadow rounded text-center hover:bg-blue-50">
            <h2 class="text-xl text-gray-700 mb-2">My Orders</h2>
            <p>Track your order history and status.</p>
        </a>
    </div>

</div>
</body>
</html>
