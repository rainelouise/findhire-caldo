<?php
require_once('./auth.php');
require_once('./models/jobPost.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$roleID = $_SESSION['role'];
$jobPost = new JobPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job_post_id'])) {
    $jobPostId = $_POST['delete_job_post_id'];
    if ($jobPost->deleteJobPost($jobPostId)) {
        $_SESSION['message'] = ['type' => 'success', 'content' => 'Job post deleted successfully.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Failed to delete the job post.'];
    }
    header("Location: job_post.php");
    exit;
}

$jobPosts = $jobPost->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Job Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-3xl">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-6 p-4 rounded <?= $_SESSION['message']['type'] === 'success' ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' ?>">
                <?= htmlspecialchars($_SESSION['message']['content']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-pink-600">All Job Posts</h2>
            <div>
                <?php if ($roleID == 2): ?>
                    <a href="create_job_post.php" 
                       class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full transition duration-200">Create Job Post</a>
                <?php endif; ?>
                <a href="index.php" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full transition duration-200 ml-2">Back</a>
            </div>
        </div>

        <?php if ($jobPosts): ?>
            <ul class="space-y-6">
                <?php foreach ($jobPosts as $post): ?>
                    <li class="p-4 rounded-xl shadow-md bg-gray-50">
                        <h3 class="text-xl font-semibold text-purple-700 mb-2"><?= htmlspecialchars($post['Title']) ?></h3>
                        <p class="text-sm text-gray-700 mb-2"><?= htmlspecialchars($post['Description']) ?></p>
                        <p class="text-xs text-gray-500 mb-4">Created by: <?= htmlspecialchars($post['CreatedBy']) ?> on <?= htmlspecialchars($post['CreatedAt']) ?></p>
                        
                        <div class="flex flex-wrap items-center gap-4">
                            <?php if ($roleID == 2): ?>
                                <a href="view_applications.php?job_post_id=<?= htmlspecialchars($post['JobPostID']) ?>" 
                                   class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-full transition">View Applications</a>
                                <form action="job_post.php" method="POST">
                                    <input type="hidden" name="delete_job_post_id" value="<?= htmlspecialchars($post['JobPostID']) ?>">
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-full transition">Delete</button>
                                </form>
                            <?php elseif ($roleID == 1): ?>
                                <a href="apply_job.php?job_post_id=<?= htmlspecialchars($post['JobPostID']) ?>" 
                                   class="bg-pink-500 hover:bg-pink-600 text-white px-3 py-2 rounded-full transition">Apply</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-gray-600">No job posts available.</p>
        <?php endif; ?>
    </div>
</body>
</html>