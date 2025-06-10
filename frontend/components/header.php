<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /teamchat/frontend/login.php');
    exit;
}
?>
<header>
    <h1>TeamChat</h1>
    <a href="/teamchat/backend/auth/logout">Logout</a>
</header>