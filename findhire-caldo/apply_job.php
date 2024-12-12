<?php
require_once('./auth.php');
require_once('./models/jobPost.php');

if ($_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$jobPostId = $_GET['job_post_id'] ?? null;
$jobPost = new JobPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {

    $coverLetter = $_POST['cover_letter'];
    $resume = $_FILES['resume'];
    $resumePath = null;
    
    if ($resume && $resume['error'] === UPLOAD_ERR_OK) {
        $fileTmpName = $resume['tmp_name'];
        $fileName = basename($resume['name']);
        $filePath = 'uploads/resumes/' . $fileName;
        
        if (move_uploaded_file($fileTmpName, $filePath)) {
            $resumePath = $filePath;
        } else {
            $_SESSION['message'] = ['type' => 'error', 'content' => 'Failed to upload resume.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Please upload a valid PDF resume.'];
    }

    if ($resumePath && $jobPostId) {
        $applicantId = $_SESSION['user_id'];
        if ($jobPost->applyJob($jobPostId, $applicantId, $coverLetter, $resumePath)) {
            $_SESSION['message'] = ['type' => 'success', 'content' => 'Application submitted successfully.'];
            header("Location: job_post.php");
            exit;
        } else {
            $_SESSION['message'] = ['type' => 'error', 'content' => 'Failed to apply for the job.'];
        }
    }
}

if ($jobPostId) {
    $jobDetails = $jobPost->getJobPostDetails($jobPostId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-6 p-4 rounded <?= $_SESSION['message']['type'] === 'success' ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' ?>">
                <?= htmlspecialchars($_SESSION['message']['content']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h2 class="text-3xl font-bold mb-6 text-center text-pink-600">Apply for Job</h2>

        <?php if ($jobDetails): ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <label for="cover_letter" class="block text-gray-800 font-medium mb-2">Cover Letter</label>
                    <textarea name="cover_letter" id="cover_letter" rows="5" 
                              class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-pink-300" 
                              required></textarea>
                </div>

                <div class="mb-6">
                    <label for="resume" class="block text-gray-800 font-medium mb-2">Resume (PDF)</label>
                    <input type="file" name="resume" id="resume" accept="application/pdf" 
                           class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-pink-300" 
                           required>
                </div>

                <div class="flex justify-between items-center">

                    <a href="job_post.php" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full shadow transition duration-200">
                        Back
                    </a>

                    <button type="submit" name="apply" 
                            class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-full transition duration-200">
                        Submit Application
                    </button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-center text-gray-600">Job post not found.</p>
        <?php endif; ?>
    </div>

</body>
</html>