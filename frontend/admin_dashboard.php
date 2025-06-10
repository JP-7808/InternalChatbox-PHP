<?php require_once __DIR__ . '/components/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - TeamChat</title>
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body>
    <?php require_once __DIR__ . '/components/sidebar.php'; ?>
    <main>
        <h2>Admin Dashboard</h2>
        <h3>Manage Users</h3>
        <form id="editUserForm" enctype="multipart/form-data">
            <input type="number" name="user_id" placeholder="User ID">
            <input type="text" name="name" placeholder="Name">
            <input type="text" name="designation" placeholder="Designation">
            <input type="text" name="location" placeholder="Location">
            <input type="file" name="profileImage">
            <button type="submit">Edit User</button>
        </form>
        <form id="deleteUserForm">
            <input type="number" name="user_id" placeholder="User ID">
            <button type="submit">Delete User</button>
        </form>
        <form id="changePasswordForm">
            <input type="number" name="user_id" placeholder="User ID">
            <input type="password" name="password" placeholder="New Password">
            <button type="submit">Change Password</button>
        </form>
        <h3>Manage Groups</h3>
        <form id="createGroupForm">
            <input type="text" name="group_name" placeholder="Group Name">
            <button type="submit">Create Group</button>
        </form>
        <form id="toggleGroupAdminForm">
            <input type="number" name="group_id" placeholder="Group ID">
            <input type="number" name="user_id" placeholder="User ID">
            <select name="is_admin">
                <option value="1">Make Admin</option>
                <option value="0">Remove Admin</option>
            </select>
            <button type="submit">Toggle Admin</button>
        </form>
        <h3>View/Download Chats</h3>
        <form id="viewGroupChatForm">
            <input type="number" name="group_id" placeholder="Group ID">
            <button type="submit">View Group Chat</button>
        </form>
        <form id="downloadGroupChatForm">
            <input type="number" name="group_id" placeholder="Group ID">
            <button type="submit">Download Group Chat</button>
        </form>
        <form id="viewPrivateChatForm">
            <input type="number" name="user_id1" placeholder="User ID 1">
            <input type="number" name="user_id2" placeholder="User ID 2">
            <button type="submit">View Private Chat</button>
        </form>
        <form id="downloadPrivateChatForm">
            <input type="number" name="user_id1" placeholder="User ID 1">
            <input type="number" name="user_id2" placeholder="User ID 2">
            <button type="submit">Download Private Chat</button>
        </form>
    </main>
    <?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>