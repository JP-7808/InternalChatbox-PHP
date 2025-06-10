<?php require_once __DIR__ . '/components/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - TeamChat</title>
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body onload="loadProfile()">
    <?php require_once __DIR__ . '/components/sidebar.php'; ?>
    <main>
        <h2>Profile</h2>
        <img id="profileImage" src="" alt="Profile Image" width="100">
        <p>Name: <span id="profileName"></span></p>
        <p>Designation: <span id="profileDesignation"></span></p>
        <p>Location: <span id="profileLocation"></span></p>
        <form id="updateProfileForm" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Name">
            <input type="text" name="designation" placeholder="Designation">
            <input type="text" name="location" placeholder="Location">
            <input type="file" name="profileImage">
            <button type="submit">Update Profile</button>
        </form>
        <form id="updateEmployeeIdForm">
            <input type="text" name="employeeId" placeholder="Employee ID">
            <button type="submit">Update Employee ID</button>
        </form>
        <form id="updateStatusForm">
            <select name="status">
                <option value="online">Online</option>
                <option value="working">Working</option>
                <option value="away">Away</option>
                <option value="offline">Offline</option>
            </select>
            <button type="submit">Update Status</button>
        </form>
    </main>
    <?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>