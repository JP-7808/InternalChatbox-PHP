<!DOCTYPE html>
<html>
<head>
    <title>Register - TeamChat</title>
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body>
    <header>
        <h1>TeamChat - Register</h1>
    </header>
    <main>
        <h2>Register</h2>
        <form id="registerForm" onsubmit="event.preventDefault(); register();" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="designation" placeholder="Designation" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="employeeId" placeholder="Employee ID" required>
            <input type="file" name="profileImage">
            <select name="role">
                <option value="employee">Employee</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Register</button>
        </form>
    </main>
</body>
</html>