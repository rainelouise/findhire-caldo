<?php
require_once('./models/user.php');

$user = new User();
$errors = [];
$successMessage = '';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['usernameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $user->login($usernameOrEmail, $password);

    if ($result['success']) {
        header('Location: index.php'); 
        exit;
    } else {
        $errors = $result['errors'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <form action="" method="POST" class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-3xl font-bold text-pink-600 mb-6 text-center">Login</h2>
        
        <?php if (!empty($errors)): ?>
            <ul class="mb-4 text-red-600">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <div class="mb-4">
            <label for="usernameOrEmail" class="block text-sm font-medium text-purple-700">Username or Email</label>
            <input 
                type="text" 
                name="usernameOrEmail" 
                id="usernameOrEmail" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" 
                value="<?= htmlspecialchars($usernameOrEmail ?? '') ?>">
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-purple-700">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
        </div>
        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-full w-full hover:bg-pink-600 transition">Login</button>
        
        <p class="mt-4 text-sm text-center text-purple-700">
            Don't have an account? 
            <a href="register.php" class="text-pink-500 underline hover:text-pink-700">Register here</a>.
        </p>
    </form>
</body>
</html>