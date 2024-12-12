<?php
require_once('./auth.php');
require_once('./models/application.php');
require_once('./models/jobPost.php');

if ($_SESSION['role'] !== 2) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$jobPostModel = new JobPost();
$jobPostId = $_GET['job_post_id'] ?? 0;
$jobPostDetails = $jobPostModel->getJobPostById($jobPostId);
$jobPostTitle = $jobPostDetails['Title'] ?? "Unknown Job Post";

$applicationModel = new Application();
$applications = $applicationModel->getApplicationsByJobPostId($jobPostId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $applicationId = $_POST['application_id'] ?? 0;

    if ($action === 'accept') {
        $applicationModel->acceptApplication($applicationId);
    } elseif ($action === 'reject') {
        $applicationModel->rejectApplication($applicationId);
    }

    header("Location: view_applications.php?job_post_id=" . $jobPostId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee); 
        }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen py-12">


    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-4xl">


        <div class="flex justify-between items-center mb-8">
            <a href="job_post.php" 
               class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full shadow-md transition duration-200">Back</a>
            <h2 class="text-xl font-semibold text-gray-700">Applications for: <?= htmlspecialchars($jobPostTitle) ?></h2>
            <div></div> 
        </div>

        <div class="bg-white p-6 rounded shadow-md overflow-x-auto">
            <?php if ($applications && count($applications) > 0): ?>

                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-400 bg-white shadow-sm">
                        <thead>
                            <tr>
                                <th class="border border-gray-400 px-4 py-2 bg-gray-100">Applicant</th>
                                <th class="border border-gray-400 px-4 py-2 bg-gray-100">Cover Letter</th>
                                <th class="border border-gray-400 px-4 py-2 bg-gray-100">Resume</th>
                                <th class="border border-gray-400 px-4 py-2 bg-gray-100">Status</th>
                                <th class="border border-gray-400 px-4 py-2 bg-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-400 px-4 py-2"><?= htmlspecialchars($application['Username']) ?></td>
                                    <td class="border border-gray-400 px-4 py-2"><?= nl2br(htmlspecialchars($application['CoverLetter'])) ?></td>
                                    <td class="border border-gray-400 px-4 py-2">
                                        <a href="<?= htmlspecialchars($application['ResumePath']) ?>" target="_blank"
                                           class="text-blue-500 hover:text-blue-700 underline">View Resume</a>
                                    </td>
                                    <td class="border border-gray-400 px-4 py-2"><?= htmlspecialchars($application['Status']) ?></td>
                                    <td class="border border-gray-400 px-4 py-2">
                                        <form method="POST" class="flex space-x-2 justify-start">
                                            <input type="hidden" name="application_id" value="<?= htmlspecialchars($application['ApplicationID']) ?>">
                                            <button type="submit" name="action" value="accept" 
                                                    class="bg-pink-500 hover:bg-pink-600 text-white px-3 py-1 rounded-full shadow-md transition duration-200">Accept</button>
                                            <button type="submit" name="action" value="reject" 
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full shadow-md transition duration-200">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-600">No applications found for this job post.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>