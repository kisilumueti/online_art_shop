<?php
$success = false;

// Handle form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if ($name && $email && $message) {
        // here you can save to DB or send email
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - Online Art Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white shadow-lg rounded-lg max-w-lg w-full p-8">
    <div class="text-center mb-6">
        <img src="logo.png" alt="Logo" class="mx-auto h-16 mb-4">
        <h1 class="text-3xl font-bold text-gray-800">Contact Us</h1>
        <p class="text-gray-500">We'd love to hear from you!</p>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded text-center">
            ✅ Thank you, <?php echo $name; ?>. Your message has been received!
        </div>
        <div class="text-center mt-4">
            <a href="login.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to Login</a>
        </div>
    <?php else: ?>
    <form method="POST" class="space-y-4">
        <input name="name" required placeholder="Your Name" class="w-full px-4 py-2 border rounded">
        <input name="email" type="email" required placeholder="you@example.com" class="w-full px-4 py-2 border rounded">
        <textarea name="message" required placeholder="Your Message" class="w-full px-4 py-2 border rounded" rows="4"></textarea>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Send Message</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
