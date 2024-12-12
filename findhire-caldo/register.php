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
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $repeatPassword = $_POST['repeatPassword'] ?? '';
    $roleID = $_POST['roleID'] ?? '';

    $result = $user->register($username, $email, $password, $repeatPassword, $roleID);

    if ($result['success']) {
        $successMessage = $result['message'];
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
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <form action="" method="POST" class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-3xl font-bold text-pink-600 mb-6 text-center">Register</h2>
        
        <?php if (!empty($successMessage)): ?>
            <p class="text-green-600 mb-4"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <ul class="mb-4 text-red-600">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-purple-700">Username</label>
            <input 
                type="text" 
                name="username" 
                id="username" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" 
                value="<?= htmlspecialchars($username ?? '') ?>">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-purple-700">Email</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" 
                value="<?= htmlspecialchars($email ?? '') ?>">
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-purple-700">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
        </div>
        <div class="mb-4">
            <label for="repeatPassword" class="block text-sm font-medium text-purple-700">Repeat Password</label>
            <input 
                type="password" 
                name="repeatPassword" 
                id="repeatPassword" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
        </div>
        <div class="mb-4">
            <label for="roleID" class="block text-sm font-medium text-purple-700">Register As</label>
            <select 
                name="roleID" 
                id="roleID" 
                class="w-full border border-pink-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                <option value="">-- Select Role --</option>
                <option value="1" <?= (isset($roleID) && $roleID == 1) ? 'selected' : '' ?>>Applicant</option>
                <option value="2" <?= (isset($roleID) && $roleID == 2) ? 'selected' : '' ?>>HR</option>
            </select>
        </div>
        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-full w-full hover:bg-pink-600 transition">Register</button>
        
        <p class="mt-4 text-sm text-center text-purple-700">
            Already have an account? 
            <a href="login.php" class="text-pink-500 underline hover:text-pink-700">Login here</a>.
        </p>
    </form>
</body>
</html>