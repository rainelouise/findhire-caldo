<?php
require_once('./auth.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$roleID = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96 text-center">
        <div class="mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16" viewBox="0 0 24 24" fill="currentColor" style="color: #f06292;">
                <path d="M12 0C8.686 0 6 2.686 6 6c0 1.133.388 2.191 1.036 3.055C3.88 10.146 1 13.672 1 18c0 3.314 2.686 6 6 6 3.184 0 5.802-2.519 5.98-5.676A5.002 5.002 0 0 0 15 19a5 5 0 0 0 4.98-5.676A5.98 5.98 0 0 0 24 12c0-3.314-2.686-6-6-6-3.314 0-6 2.686-6 6a5.986 5.986 0 0 0 1.227 3.601C12.31 17.657 12 18.793 12 20c0 3.314-2.686 6-6 6s-6-2.686-6-6c0-4.943 3.771-9.024 8.775-9.893A3.98 3.98 0 0 1 12 6c0-2.21-1.79-4-4-4S4 3.79 4 6a3.98 3.98 0 0 1-.225 1.107C.771 8.976-1 12.943-1 18c0 5.523 4.477 10 10 10s10-4.477 10-10c0-4.943-3.771-9.024-8.775-9.893A3.98 3.98 0 0 1 12 6c0-2.21 1.79-4 4-4s4 1.79 4 4c0 1.274-.605 2.401-1.536 3.065a5.976 5.976 0 0 0 1.537 7.935A6 6 0 0 0 24 12c0-3.314-2.686-6-6-6s-6 2.686-6 6a5.986 5.986 0 0 0 1.227 3.601C12.31 17.657 12 18.793 12 20c0 3.314-2.686 6-6 6S0 23.314 0 20c0-4.943 3.771-9.024 8.775-9.893A3.98 3.98 0 0 1 12 6z"/>
            </svg>
            <h1 class="text-pink-600 text-3xl font-bold mt-2">FindHire</h1>
        </div>

        <h1 class="text-2xl font-bold text-purple-700 mb-4">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>

        <p class="mb-6 text-purple-600">
            You are logged in as 
            <span class="font-semibold">
                <?= $roleID == 1 ? 'Applicant' : 'HR Representative' ?>
            </span>.
        </p>

        <?php if ($roleID == 1): ?>
            <div class="mb-4">
                <a href="job_post.php" class="bg-pink-500 text-white px-4 py-2 rounded-full block mb-2 hover:bg-pink-600">Apply for a Job</a>
                <a href="message_hr.php" class="bg-pink-500 text-white px-4 py-2 rounded-full block hover:bg-pink-600">Message HR</a>
            </div>
        <?php elseif ($roleID == 2): ?>
            <div class="mb-4">
                <a href="job_post.php" class="bg-pink-500 text-white px-4 py-2 rounded-full block mb-2 hover:bg-pink-600">View Job Post</a>
                <a href="view_messages.php" class="bg-pink-500 text-white px-4 py-2 rounded-full block hover:bg-pink-600">View Messages</a>
            </div>
        <?php endif; ?>

        <form action="logout.php" method="POST">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-full w-full hover:bg-red-600">Logout</button>
        </form>
    </div>
</body>
</html>