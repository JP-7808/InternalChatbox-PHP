<?php require_once __DIR__ . '/components/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>One-on-One Chat - TeamChat</title>
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body>
    <?php require_once __DIR__ . '/components/sidebar.php'; ?>
    <main>
        <h2>One-on-One Chat</h2>
        <div class="chat-box" id="chatBox"></div>
        <input type="text" id="messageText" placeholder="Type a message">
        <input type="number" id="receiverId" placeholder="Receiver ID">
        <button onclick="sendMessage(document.getElementById('receiverId').value, null)">Send</button>
        <button onclick="loadMessages(document.getElementById('receiverId').value, null)">Refresh</button>
    </main>
    <?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>