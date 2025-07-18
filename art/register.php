<?php
session_start();
require_once __DIR__ . '/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    $role     = in_array($_POST['role'], ['artist', 'customer']) ? $_POST['role'] : 'customer';
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    if (strlen($name) < 3) $errors[] = "Name must be at least 3 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email already registered.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $role, $phone, $address]);
        $success = "Registration successful. You can now <a href='login.php' class='underline text-blue-600'>login</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
<div class="bg-white shadow-lg rounded-lg max-w-md w-full p-8">
<div class="text-center mb-6">
<img src="logo.png" alt="Logo" class="mx-auto h-16 mb-2">
<h1 class="text-2xl font-bold text-gray-800">Create an Account</h1>
<p class="text-gray-500">Join the art community today</p>
</div>

<?php if ($errors): ?>
<div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
<?php foreach ($errors as $err) echo htmlspecialchars($err) . "<br>"; ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST" class="space-y-3">
<input name="name" required placeholder="Full Name" class="w-full px-4 py-2 border rounded" value="<?php echo htmlspecialchars($_POST['name'] ?? '') ?>">
<input name="email" type="email" required placeholder="Email" class="w-full px-4 py-2 border rounded" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">
<input name="phone" placeholder="Phone" class="w-full px-4 py-2 border rounded" value="<?php echo htmlspecialchars($_POST['phone'] ?? '') ?>">
<textarea name="address" placeholder="Address" class="w-full px-4 py-2 border rounded"><?php echo htmlspecialchars($_POST['address'] ?? '') ?></textarea>

<select name="role" class="w-full px-4 py-2 border rounded">
<option value="customer">Customer</option>
<option value="artist">Artist</option>
</select>

<input name="password" type="password" required placeholder="Password" class="w-full px-4 py-2 border rounded">
<input name="confirm" type="password" required placeholder="Confirm Password" class="w-full px-4 py-2 border rounded">

<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Register</button>
</form>

<div class="mt-4 text-center text-sm text-gray-600">
Already have an account? <a href="login.php" class="hover:underline text-blue-600">Login</a>
</div>

</div>
</div>
</body>
</html>
