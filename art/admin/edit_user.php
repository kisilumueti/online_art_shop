<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$user = $pdo->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$id]);
$user = $user->fetch();

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = in_array($_POST['role'], ['admin', 'artist', 'customer']) ? $_POST['role'] : $user['role'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, phone=?, address=? WHERE id=?");
    $stmt->execute([$name, $email, $role, $phone, $address, $id]);

    header("Location: manage_users.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>
    <form method="POST" class="bg-white p-6 rounded shadow space-y-3 max-w-md">
        <input name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full p-2 border rounded">
        <input name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full p-2 border rounded">
        <select name="role" class="w-full p-2 border rounded">
            <?php foreach(['admin','artist','customer'] as $r): ?>
                <option value="<?php echo $r; ?>" <?php if($r==$user['role']) echo 'selected'; ?>><?php echo ucfirst($r); ?></option>
            <?php endforeach; ?>
        </select>
        <input name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="w-full p-2 border rounded">
        <textarea name="address" class="w-full p-2 border rounded"><?php echo htmlspecialchars($user['address']); ?></textarea>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</div>
</body>
</html>
