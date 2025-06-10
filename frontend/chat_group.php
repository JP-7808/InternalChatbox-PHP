<?php require_once __DIR__ . '/components/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Group Chat - TeamChat</title>
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body>
    <?php require_once __DIR__ . '/components/sidebar.php'; ?>
    <main>
        <h2>Group Chat</h2>
        <div class="chat-box" id="chatBox"></div>
        <input type="text" id="messageText" placeholder="Type a message">
        <input type="number" id="groupId" placeholder="Group ID">
        <button onclick="sendMessage(null, document.getElementById('groupId').value)">Send</button>
        <button onclick="loadMessages(null, document.getElementById('groupId').value)">Refresh</button>
    </main>
    <?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>