<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$art = $pdo->prepare("SELECT * FROM artworks WHERE id=?");
$art->execute([$id]);
$art = $art->fetch();

if (!$art) die("Artwork not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = trim($_POST['category']);
    $status = in_array($_POST['status'], ['available','sold','reserved','hidden']) ? $_POST['status'] : $art['status'];

    $stmt = $pdo->prepare("UPDATE artworks SET title=?, description=?, price=?, stock=?, category=?, status=? WHERE id=?");
    $stmt->execute([$title, $description, $price, $stock, $category, $status, $id]);

    header("Location: manage_art.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Artwork</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Artwork</h1>
    <form method="POST" class="bg-white p-6 rounded shadow space-y-3 max-w-md">
        <input name="title" value="<?php echo htmlspecialchars($art['title']); ?>" class="w-full p-2 border rounded">
        <textarea name="description" class="w-full p-2 border rounded"><?php echo htmlspecialchars($art['description']); ?></textarea>
        <input name="price" value="<?php echo htmlspecialchars($art['price']); ?>" class="w-full p-2 border rounded">
        <input name="stock" value="<?php echo htmlspecialchars($art['stock']); ?>" class="w-full p-2 border rounded">
        <input name="category" value="<?php echo htmlspecialchars($art['category']); ?>" class="w-full p-2 border rounded">
        <select name="status" class="w-full p-2 border rounded">
            <?php foreach(['available','sold','reserved','hidden'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php if($art['status']==$s) echo 'selected'; ?>><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</div>
</body>
</html>
