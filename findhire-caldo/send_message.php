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
$sendSuccess = false;

if (isset($_GET['receiver_id'])) {
    $receiverId = $_GET['receiver_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $content = $_POST['content'] ?? '';
        if ($receiverId && $content) {
            $sendSuccess = $messageModel->sendMessage($_SESSION['user_id'], $receiverId, $content);
            header('Location: view_messages.php');
            exit;
        }
    }
} else {
    header('Location: view_messages.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #fbc2eb, #a6c1ee);
        }
    </style>
</head>
<body class="flex flex-col items-center justify-start min-h-screen">

    <div class="bg-white w-full max-w-2xl p-4 mt-6 shadow-md rounded-md flex items-center justify-between">
        <h2 class="text-2xl font-bold text-pink-600">Send Message</h2>
        <a href="view_messages.php" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full shadow-md transition duration-200">
           Back to Messages
        </a>
    </div>

    <?php if ($sendSuccess): ?>
        <div class="bg-green-200 text-green-800 p-4 w-full max-w-2xl mt-4 rounded shadow-md text-center">
            Message sent successfully!
        </div>
    <?php endif; ?>

    <div class="bg-white w-full max-w-2xl mt-6 p-6 rounded-xl shadow-lg">
        <form method="POST">
            <div>
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