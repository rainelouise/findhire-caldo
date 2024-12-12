<?php
require_once('./auth.php');
require_once('./models/message.php');
require_once('./models/user.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 2) {
    header('Location: index.php');
    exit;
}

$messageModel = new Message();
$userModel = new User();

$applicants = $userModel->getApplicants();
$sendSuccess = false;
$deleteSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_message'])) {
        $receiverId = $_POST['receiver_id'] ?? 0;
        $content = $_POST['content'] ?? '';
        if ($receiverId && $content) {
            $sendSuccess = $messageModel->sendMessage($_SESSION['user_id'], $receiverId, $content);
            header('Location: view_messages.php');
            exit;
        }
    }

    if (isset($_POST['delete_messages'])) {
        $applicantId = $_POST['applicant_id'] ?? 0;
        if ($applicantId) {
            $deleteSuccess = $messageModel->deleteAllMessages($_SESSION['user_id'], $applicantId);
            header('Location: view_messages.php');
            exit;
        }
    }
}

$conversations = [];
foreach ($applicants as $applicant) {
    $conversation = $messageModel->getConversationBetweenUserAndApplicant($_SESSION['user_id'], $applicant['UserID']);
    
    if (count($conversation) > 0) {
        $conversations[$applicant['UserID']] = $conversation;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversations with Applicants</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex flex-col items-center justify-start min-h-screen">

    <div class="bg-white w-full max-w-2xl p-4 mt-6 shadow-md rounded-md flex items-center justify-between">
        <h2 class="text-2xl font-bold text-pink-600">Conversations with Applicants</h2>
        <a href="index.php" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full shadow-md transition duration-200">
           Back
        </a>
    </div>

    <?php if ($sendSuccess): ?>
        <div class="bg-green-200 text-green-800 p-4 w-full max-w-2xl mt-4 rounded shadow-md text-center">
            Message sent successfully!
        </div>
    <?php endif; ?>
    <?php if ($deleteSuccess): ?>
        <div class="bg-red-200 text-red-800 p-4 w-full max-w-2xl mt-4 rounded shadow-md text-center">
            All messages with this applicant have been deleted!
        </div>
    <?php endif; ?>

    <div class="bg-white w-full max-w-2xl mt-6 p-6 rounded-xl shadow-lg space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Previous Messages</h3>

        <?php if (count($conversations) > 0): ?>
            <?php foreach ($conversations as $applicantId => $conversation): ?>
                <div class="space-y-4 p-4 bg-white rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <h4 class="text-2xl font-semibold text-gray-700"><?= htmlspecialchars($applicants[array_search($applicantId, array_column($applicants, 'UserID'))]['Username']) ?></h4>
                        <form method="POST" class="ml-4">
                            <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($applicantId) ?>">
                            <button type="submit" name="delete_messages" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full shadow-md">
                                Delete All Messages
                            </button>
                        </form>
                    </div>

                    <div class="space-y-2 max-h-96 overflow-y-auto mt-4">
                        <?php foreach ($conversation as $message): ?>
                            <div class="space-y-2">
                                <div class="<?= $message['SenderID'] == $_SESSION['user_id'] ? 'text-right' : 'text-left' ?>">
                                    <div class="inline-block max-w-xs p-4 rounded-lg shadow-md
                                                <?= $message['SenderID'] == $_SESSION['user_id'] ? 'bg-pink-200' : 'bg-gray-200' ?>">
                                        <p class="text-sm"><?= nl2br(htmlspecialchars($message['Content'])) ?></p>
                                        <span class="text-xs text-gray-600 block mt-1">
                                            <?= htmlspecialchars($message['SentAt']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4">
                        <form method="POST">

                            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($applicantId) ?>">

                            <div class="mt-4">
                                <label for="content" class="block text-gray-800 font-semibold mb-2">Message Content:</label>
                                <textarea name="content" id="content" rows="4"
                                        class="border border-gray-400 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring focus:ring-pink-300" 
                                        required></textarea>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="send_message"
                                        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-full shadow-md transition duration-200">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No conversations yet. Start a message with an applicant!</p>
        <?php endif; ?>
    </div>

    <div class="bg-white w-full max-w-2xl mt-6 p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800">Send New Message</h3>
        <form method="POST">
            <div>
                <label for="receiver_id" class="block text-gray-800 font-semibold mb-2">Select Applicant:</label>
                <select name="receiver_id" id="receiver_id" class="border border-gray-400 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring focus:ring-pink-300" required>
                    <option value="" disabled selected>Select an applicant</option>
                    <?php foreach ($applicants as $applicant): ?>
                        <option value="<?= htmlspecialchars($applicant['UserID']) ?>">
                            <?= htmlspecialchars($applicant['Username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="content" class="block text-gray-800 font-semibold mb-2">Message Content:</label>
                <textarea name="content" id="content" rows="4"
                          class="border border-gray-400 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring focus:ring-pink-300" 
                          required></textarea>
            </div>

            <div class="text-center mt-4">
                <button type="submit" name="send_message"
                        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-full shadow-md transition duration-200">
                    Send Message
                </button>
            </div>
        </form>
    </div>

</body>
</html>