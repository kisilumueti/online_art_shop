<?php
session_start();
require_once __DIR__ . '/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) $errors[] = "No user found with this email.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);
        $success = "Password reset successfully. You can now <a href='login.php' class='underline text-blue-600'>login</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
<div class="bg-white shadow-lg rounded-lg max-w-md w-full p-8">
<div class="text-center mb-6">
<img src="logo.png" alt="Logo" class="mx-auto h-16 mb-2">
<h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
<p class="text-gray-500">Enter your email and new password</p>
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
<input name="email" type="email" required placeholder="Email" class="w-full px-4 py-2 border rounded" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">
<input name="password" type="password" required placeholder="New Password" class="w-full px-4 py-2 border rounded">
<input name="confirm" type="password" required placeholder="Confirm Password" class="w-full px-4 py-2 border rounded">
<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Reset Password</button>
</form>

<div class="mt-4 text-center text-sm text-gray-600">
<a href="login.php" class="hover:underline text-blue-600">Back to Login</a>
</div>

</div>
</div>
</body>
</html>
