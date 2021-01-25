<!--
Luke Mason
CPSC 314: WebDev Final Project
admin.php
Page to let the admin log in
-->

<html>

<head>
    <meta charset="utf-8" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="loginStyle.css">
</head>

<body style = "background-color: whitesmoke;">
    <?php
        if(isset($_POST["wrongPassword"])) {
            echo '<script>alert("Wrong password.")</script>';
            // Redirected and password is incorrect
        }
    ?>
    <h1 class = "header">Admin Login</h1>
    <div class = "center login-background">
        <form action = "adminControl.php" method="POST">
            <p>
                <label class = "login-labels" for="password">Password:</label>
                <input type="password" name="password" id="a_password">
            </p>
            <input class = "login-btn-style center-buttons" id = "login-btn" type="submit" value="Login">
        </form>  
    </div>
</body>
</html>