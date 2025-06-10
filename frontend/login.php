<!DOCTYPE html>
<html>
<head>
    <title>Login - TeamChat</title>
    <link rel="stylesheet" href="/teamchat/frontend/assets/css/style.css">
    <script src="/teamchat/frontend/assets/js/script.js"></script>
</head>
<body>
    <header>
        <h1>TeamChat - Login</h1>
    </header>
    <main>
        <h2>Login</h2>
        <form onsubmit="event.preventDefault(); login();">
            <input type="email" id="email" placeholder="Email" required>
            <input type="password" id="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="/teamchat/frontend/register.php">Register</a></p>
    </main>
</body>
</html>