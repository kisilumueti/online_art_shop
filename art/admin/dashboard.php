<?php
session_start();
require_once __DIR__ . '/../db.php';

// Require admin role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch counts
$counts = [
    'admins' => 0,
    'artists' => 0,
    'customers' => 0,
    'total_users' => 0,
    'total_orders' => 0,
    'total_payments' => 0,
    'total_sales' => 0.00,
];

$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
while ($row = $stmt->fetch()) {
    $counts[$row['role'] . 's'] = $row['count'];
    $counts['total_users'] += $row['count'];
}

$counts['total_orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$counts['total_payments'] = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
$counts['total_sales'] = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM sales")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-800 text-white p-4 flex justify-between">
    <div class="text-lg font-bold">Admin Dashboard</div>
    <div>
        <a href="upload_art.php" class="px-3 hover:underline">Upload Art</a>
        <a href="manage_users.php" class="px-3 hover:underline">Manage Users</a>
        <a href="manage_art.php" class="px-3 hover:underline">Manage Art</a>
        <a href="../logout.php" class="px-3 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Overview</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-xl text-gray-700">Admins</h2>
            <p class="text-3xl font-bold text-blue-600"><?php echo $counts['admins']; ?></p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-xl text-gray-700">Artists</h2>
            <p class="text-3xl font-bold text-blue-600"><?php echo $counts['artists']; ?></p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-xl text-gray-700">Customers</h2>
            <p class="text-3xl font-bold text-blue-600"><?php echo $counts['customers']; ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-xl text-gray-700">Total Users</h2>
            <p class="text-3xl font-bold text-green-600"><?php echo $counts['total_users']; ?></p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-xl text-gray-700">Total Orders</h2>
            <p class="text-3xl font-bold text-green-600"><?php echo $counts['total_orders']; ?></p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-xl text-gray-700">Total Payments</h2>
            <p class="text-3xl font-bold text-green-600"><?php echo $counts['total_payments']; ?></p>
        </div>
    </div>

    <div class="bg-white p-4 shadow rounded text-center mt-6">
        <h2 class="text-xl text-gray-700">Total Sales Revenue</h2>
        <p class="text-3xl font-bold text-purple-700">$<?php echo number_format($counts['total_sales'], 2); ?></p>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Recent Orders</h2>
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-200 text-gray-600">
                        <th class="p-2">Order ID</th>
                        <th class="p-2">User ID</th>
                        <th class="p-2">Total</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Created At</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, user_id, total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 10");
                while ($order = $stmt->fetch()) {
                    echo "<tr class='border-b'>
                        <td class='p-2'>{$order['id']}</td>
                        <td class='p-2'>{$order['user_id']}</td>
                        <td class='p-2'>\$" . number_format($order['total'], 2) . "</td>
                        <td class='p-2'>{$order['status']}</td>
                        <td class='p-2'>{$order['created_at']}</td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Recent Sales</h2>
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-200 text-gray-600">
                        <th class="p-2">Sale ID</th>
                        <th class="p-2">Artwork ID</th>
                        <th class="p-2">Artist ID</th>
                        <th class="p-2">Order ID</th>
                        <th class="p-2">Amount</th>
                        <th class="p-2">Created At</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, artwork_id, artist_id, order_id, amount, created_at FROM sales ORDER BY created_at DESC LIMIT 10");
                while ($sale = $stmt->fetch()) {
                    echo "<tr class='border-b'>
                        <td class='p-2'>{$sale['id']}</td>
                        <td class='p-2'>{$sale['artwork_id']}</td>
                        <td class='p-2'>{$sale['artist_id']}</td>
                        <td class='p-2'>{$sale['order_id']}</td>
                        <td class='p-2'>\$" . number_format($sale['amount'], 2) . "</td>
                        <td class='p-2'>{$sale['created_at']}</td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
