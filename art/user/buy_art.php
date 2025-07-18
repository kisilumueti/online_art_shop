<?php
session_start();
require_once __DIR__ . '/../db.php';

// Verify user session & role
if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['artist', 'customer'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['artwork_id'])) {
    header("Location: view_artworks.php");
    exit;
}

$artwork_id = (int) $_GET['artwork_id'];

// Fetch artwork to validate & get details
$stmt = $pdo->prepare("SELECT * FROM artworks WHERE id = ? AND status = 'available'");
$stmt->execute([$artwork_id]);
$art = $stmt->fetch();

if (!$art) {
    die("Artwork not available.");
}

// Create Order
$pdo->beginTransaction();
try {
    // create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status, shipping_address, payment_method, transaction_id) 
                           VALUES (?, ?, 'paid', '', 'online', ?)");
    $transaction_id = uniqid('txn_', true);
    $stmt->execute([$user_id, $art['price'], $transaction_id]);
    $order_id = $pdo->lastInsertId();

    // create order item
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, artwork_id, quantity, price) VALUES (?, ?, 1, ?)");
    $stmt->execute([$order_id, $artwork_id, $art['price']]);

    // optional: update artwork stock & status
    $stmt = $pdo->prepare("UPDATE artworks SET stock = stock - 1, status = CASE WHEN stock-1<=0 THEN 'sold' ELSE 'available' END WHERE id = ?");
    $stmt->execute([$artwork_id]);

    $pdo->commit();
    $success = true;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Purchase failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Purchase - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
<meta http-equiv="refresh" content="3;url=dashboard.php">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded shadow text-center">
    <?php if ($success): ?>
        <h1 class="text-2xl font-bold text-green-600 mb-4">✅ Purchase Successful!</h1>
        <p class="text-gray-700">You have successfully purchased <strong><?php echo htmlspecialchars($art['title']); ?></strong>.</p>
        <p class="text-sm text-gray-500 mt-2">You will be redirected to your dashboard shortly...</p>
        <a href="dashboard.php" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Go to Dashboard Now</a>
    <?php endif; ?>
</div>
</body>
</html>
