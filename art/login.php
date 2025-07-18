<?php
session_start();
require_once __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $valid = false;

        // First check if stored password is hashed
        if (password_verify($password, $user['password'])) {
            $valid = true;
        }
        // If not hashed, check as plain text
        elseif ($password === $user['password']) {
            $valid = true;
        }

        if ($valid) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit;
        }
    }

    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
#loader {
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:white;
    z-index:9999;
    display:flex;
    justify-content:center;
    align-items:center;
}
.spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% {transform: rotate(0deg);}
    100% {transform: rotate(360deg);}
}
</style>
</head>
<body class="bg-gray-100">
<div id="loader">
    <div class="spinner"></div>
</div>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg max-w-md w-full p-8">
        <div class="text-center mb-6">
            <img src="logo.png" alt="Logo" class="mx-auto h-16 mb-2">
            <h1 class="text-2xl font-bold text-gray-800">Sign in to Your Account</h1>
            <p class="text-gray-500">Welcome back, please login to continue</p>
        </div>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required placeholder="you@example.com" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            </div>
            <div class="relative">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" required placeholder="********" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                <button type="button" onclick="togglePassword()" class="absolute right-3 top-9 text-gray-600">
                    👁️
                </button>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Sign In</button>
        </form>
        <div class="mt-4 text-center text-sm text-gray-600">
            <a href="register.php" class="hover:underline">Register</a> |
            <a href="reset.php" class="hover:underline">Reset Password</a> |
            <a href="about.php" class="hover:underline">About</a> |
            <a href="contact.php" class="hover:underline">Contact</a>
        </div>
    </div>
</div>

<script>
window.addEventListener("load", () => {
    document.getElementById('loader').style.display = 'none';
});

function togglePassword() {
    const pwd = document.getElementById("password");
    pwd.type = (pwd.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
