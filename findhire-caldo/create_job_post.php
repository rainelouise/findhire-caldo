<?php
require_once('./auth.php');
require_once('./models/jobPost.php');

if ($_SESSION['role'] !== 2) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$jobPost = new JobPost();
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $createdBy = $_SESSION['user_id'];

    $result = $jobPost->store($title, $description, $createdBy);

    if ($result['success']) {
        header('Location: job_post.php');
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
    <title>Create Job Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee); 
        }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen py-10">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold mb-6 text-purple-700">Create Job Post</h2>

            <div class="mb-6">
                <a href="job_post.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full transition duration-200 ml-2">Back</a>
            </div>
        </div>
        
        <?php if (!empty($errors)): ?>
            <ul class="mb-6 text-red-600">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Job Title</label>
                <input type="text" name="title" id="title" class="w-full border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" rows="4" required></textarea>
            </div>

            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full w-full shadow-md transition duration-200">Create Job Post</button>
        </form>
    </div>

</body>
</html>