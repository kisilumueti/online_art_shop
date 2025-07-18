<?php
session_start();
require_once __DIR__ . '/../db.php';

// Verify user session & role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['artist', 'customer'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders + order items
$orders = $pdo->prepare("
    SELECT o.*, oi.artwork_id, oi.quantity, oi.price as item_price, a.title, a.image_path 
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN artworks a ON oi.artwork_id = a.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$orders->execute([$user_id]);
$rows = $orders->fetchAll();

// Group by order_id
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['id']]['order'] = [
        'id' => $row['id'],
        'total' => $row['total'],
        'status' => $row['status'],
        'created_at' => $row['created_at'],
        'shipping_address' => $row['shipping_address'] ?? '',
        'payment_method' => $row['payment_method'] ?? '',
        'transaction_id' => $row['transaction_id'] ?? ''
    ];
    $grouped[$row['id']]['items'][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- Navbar -->
<nav class="bg-blue-800 text-white p-4 flex justify-between items-center">
    <div class="text-lg font-bold">My Orders</div>
    <div class="flex space-x-4 items-center">
        <a href="dashboard.php" class="hover:underline">Dashboard</a>
        <a href="view_artworks.php" class="hover:underline">View Artworks</a>
        <a href="notifications.php" class="hover:underline">Notifications</a>
        <a href="buy_art.php" class="hover:underline">Buy Art</a>
        <a href="../logout.php" class="hover:underline">Logout</a>
        <img src="../logo.png" alt="Logo" class="h-8 ml-4">
    </div>
</nav>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Your Order History</h1>

    <?php if (empty($grouped)): ?>
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
            You have not placed any orders yet.
        </div>
    <?php else: ?>
        <?php foreach ($grouped as $order_id => $data): ?>
            <div class="bg-white shadow rounded p-4 mb-6">
                <div class="flex justify-between mb-2">
                    <div>
                        <h2 class="text-lg font-bold">Order #<?php echo $order_id; ?></h2>
                        <p class="text-sm text-gray-600">Placed: <?php echo $data['order']['created_at']; ?></p>
                        <p class="text-sm text-gray-600">Status: <span class="capitalize"><?php echo $data['order']['status']; ?></span></p>
                        <p class="text-sm text-gray-600">Total: <strong>$<?php echo number_format($data['order']['total'],2); ?></strong></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Payment: <?php echo htmlspecialchars($data['order']['payment_method']); ?></p>
                        <p class="text-sm text-gray-600">Transaction ID: <?php echo htmlspecialchars($data['order']['transaction_id']); ?></p>
                    </div>
                </div>
                <table class="min-w-full text-sm mt-4">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600">
                            <th class="p-2">Artwork</th>
                            <th class="p-2">Image</th>
                            <th class="p-2">Quantity</th>
                            <th class="p-2">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['items'] as $item): ?>
                            <tr class="border-b">
                                <td class="p-2"><?php echo htmlspecialchars($item['title']); ?></td>
                                <td class="p-2">
                                    <?php if ($item['image_path']): ?>
                                        <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" class="h-12">
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td class="p-2"><?php echo $item['quantity']; ?></td>
                                <td class="p-2">$<?php echo number_format($item['item_price'],2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
