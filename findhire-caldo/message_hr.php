<?php
require_once('./auth.php');
require_once('./models/message.php');
require_once('./models/user.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit;
}

$messageModel = new Message();
$userModel = new User();

$hrs = $userModel->getHRs();
$sendSuccess = false;
$deleteSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_message'])) {
        $receiverId = $_POST['receiver_id'] ?? 0;
        $content = $_POST['content'] ?? '';
        if ($receiverId && $content) {
            $sendSuccess = $messageModel->sendMessage($_SESSION['user_id'], $receiverId, $content);
            header('Location: message_hr.php');
            exit;
        }
    }

    if (isset($_POST['delete_messages'])) {
        $hrId = $_POST['hr_id'] ?? 0;
        if ($hrId) {
            $deleteSuccess = $messageModel->deleteAllMessages($_SESSION['user_id'], $hrId);
            header('Location: message_hr.php');
            exit;
        }
    }
}

$conversations = [];
foreach ($hrs as $hr) {
    $conversation = $messageModel->getConversationBetweenUserAndApplicant($_SESSION['user_id'], $hr['UserID']);

    if (count($conversation) > 0) {
        $conversations[$hr['UserID']] = $conversation;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversations with HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex flex-col items-center justify-start min-h-screen">

    <div class="bg-white w-full max-w-2xl p-4 mt-6 shadow-md rounded-md flex items-center justify-between">
        <h2 class="text-2xl font-bold text-pink-600">Conversations with HR</h2>
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
            All messages with this HR have been deleted!
        </div>
    <?php endif; ?>

    <div class="bg-white w-full max-w-2xl mt-6 p-6 rounded-xl shadow-lg space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Previous Messages</h3>

        <?php if (count($conversations) > 0): ?>
            <?php foreach ($conversations as $hrId => $conversation): ?>
                <div class="bg-white rounded-lg shadow-md p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-2xl font-semibold text-gray-700">
                            <?= htmlspecialchars($hrs[array_search($hrId, array_column($hrs, 'UserID'))]['Username']) ?>
                        </h4>

                        <form method="POST">
                            <input type="hidden" name="hr_id" value="<?= htmlspecialchars($hrId) ?>">
                            <button type="submit" name="delete_messages" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full shadow-md mt-4">
                                Delete Conversation
                            </button>
                        </form>
                    </div>

                    <div class="space-y-2 max-h-96 overflow-y-auto">
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

                    <form method="POST" class="mt-4">
                        <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($hrId) ?>">
                        <div>
                            <label for="content" class="block text-gray-800 font-semibold mb-2">Message Content:</label>
                            <textarea name="content" id="content" rows="4" class="border border-gray-400 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring focus:ring-pink-300" required></textarea>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="send_message" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-full shadow-md transition duration-200">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No conversations yet. Start a message with an HR!</p>
        <?php endif; ?>
    </div>

    <div class="bg-white w-full max-w-2xl mt-6 p-6 rounded-xl shadow-lg space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Start a Conversation</h3>
        <form method="POST">
            <div>
                <label for="receiver_id" class="block text-gray-800 font-semibold mb-2">Select HR:</label>
                <select name="receiver_id" id="receiver_id" class="border border-gray-400 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring focus:ring-pink-300" required>
                    <option value="" disabled selected>Select an HR</option>
                    <?php foreach ($hrs as $hr): ?>
                        <option value="<?= htmlspecialchars($hr['UserID']) ?>"><?= htmlspecialchars($hr['Username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4">
                <label for="content" class="block text-gray-800 font-semibold mb-2">Message Content:</label>
                <textarea name="content" id="content" rows="4" class="border border-gray-400 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring focus:ring-pink-300" required></textarea>
            </div>

            <div class="text-center mt-4">
                <button type="submit" name="send_message" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-full shadow-md transition duration-200">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</body>
</html>