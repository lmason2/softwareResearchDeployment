<!--
Luke Mason
CPSC 314: WebDev Final Project
login.php
Page to start the user at a login screen
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <link rel="stylesheet" href="loginStyle.css">
</head>

<script src = "loginJScript.js"></script>

<?php
session_destroy(); // End previous session
session_start(); // Start new session
?>

<body style = "background-color: whitesmoke;">
    <form action = "admin.php">
        <input class = "login-btn-style" type="submit" value="I'm an admin">
    </form>
    <h1 class = "header">Gonzaga Intramurals</h1>
    <div class = "center login-background">
        <form action = "home.php" method="POST">
            <p>
                <label class = "login-labels" for="username">GUID:</label>
                <input type="text" name="username" id="username">
            </p>
            <input class = "login-btn-style center-buttons" id = "login-btn" type="submit" value="Login">
        </form>  
    </div>
</body>
</html>