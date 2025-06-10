<?php require_once __DIR__ . '/components/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat - TeamChat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body onload="loadUsersAndGroups()">
    <input type="hidden" id="currentUserId" value="<?php echo $_SESSION['user_id']; ?>">
    <?php require_once __DIR__ . '/components/sidebar.php'; ?>
    <main>
        <h2>Chat</h2>
        <div id="chatHeader" class="mb-3"></div>
        <div class="chat-box" id="chatBox"></div>
        <div class="chat-input">
            <input type="text" id="messageText" placeholder="Type a message" class="form-control d-inline-block">
            <button onclick="sendMessage()" class="btn btn-primary">Send</button>
        </div>
    </main>
    <?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>