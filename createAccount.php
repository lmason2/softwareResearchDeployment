<!--
Luke Mason
CPSC 314: WebDev Final Project
createAccount.php
Page to create an account to put into the database
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>Create Account</title>
    <link rel="stylesheet" href="createAccountStyle.css">
</head>

<body style = "background-color: whitesmoke;">
    <?php
        // Function to check if the GUID entered is a repeat GUID
        function checkIfRepeat($GU_ID, $userName) {
            // Server info
            $server = "cps-database.gonzaga.edu";
            $username = "lmason2";
            $password = "Gozagsxc17";
            $database = "lmason2_DB";
    
            // Connect to database
            $conn = mysqli_connect($server, $username, $password, $database);
    
            // Check connection
            if (!$conn) {
                die('Error: ' . mysqli_connect_error());
                console.log("error"); 
            }

            // Query to check if the GUID already exists
            $authentication = "SELECT u.user_n " .
                              "FROM user u " .
                              "WHERE u.GU_ID = ?";

            // Run query
            $stmt = $conn->stmt_init();
            $stmt->prepare($authentication);
            $stmt->bind_param("i", $GU_ID);
            $stmt->execute();
            $stmt->bind_result($user_n);

            // Check if username is taken
            if($stmt->fetch()) {
                echo '<script>alert("That username is already taken")</script>';
            }
            else {
                // Username not taken
                $conn = mysqli_connect($server, $username, $password, $database);
    
                // Check connection
                if (!$conn) {
                    die('Error: ' . mysqli_connect_error());
                    console.log("error"); 
                }

                // Insertion query
                $insert = "INSERT INTO user (GU_ID, user_n, is_admin) VALUES (" . $GU_ID . ", \"" . $userName . "\", " . "0" . ")";
                if ($conn->query($insert) === TRUE) {
                    // User has been inserted into database
                    header("Location: http://barney.gonzaga.edu/~lmason2/htmlFiles/login.php");
                }
                else {
                    // Problem with insertion query
                    echo '<script>alert("Problem inserting new user.")</script>';
                }
            }
        }

        if(isset($_POST["guid"]) && isset($_POST["username"])) {
            // The GUID and the username have been set

            // Sanitize
            $filteredGUID = filter_var($_POST["guid"], FILTER_SANITIZE_STRING);
            checkIfRepeat($filteredGUID, $_POST["username"]);
        }
    ?>
    <h1 class = "header">Create Account</h1>
    <div class = "center">
        <form class = "create-background" action="createAccount.php" method="POST">
            <p>
                <label class = "ca-labels" for="guid">GUID: </label>
                <input type="text" name="guid" id="guid">
            </p>

            <p>
                <label class = "ca-labels" for="username">Username:</label>
                <input type="text" name="username" id="username">
            </p>
            <input class = "create-btn-style center-buttons" id = "create-btn" type="submit" value="Create Account">
        </form>  
    </div>
</body>
</html>