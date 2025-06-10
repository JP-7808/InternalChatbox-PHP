<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /teamchat/frontend/login.php');
    exit;
}
?>
<div class="sidebar">
    <h2>TeamChat</h2>
    <ul>
        <li><a href="/teamchat/frontend/dashboard.php">Dashboard</a></li>
        <li><a href="/teamchat/frontend/chat.php">Chat</a></li>
        <li><a href="/teamchat/frontend/profile.php">Profile</a></li>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="/teamchat/frontend/admin_dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
    </ul>
    <h3>Users</h3>
    <ul id="userList"></ul>
    <h3>Groups</h3>
    <ul id="groupList"></ul>
</div>