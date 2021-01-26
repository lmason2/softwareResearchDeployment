<!--
Luke Mason
CPSC 314: WebDev Final Project
myTeam.php
Page to let the user see their team
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>My Team</title>
    <link rel="stylesheet" href="myTeamStyle.css">
</head>

<script src = "myTeamJScript.js"></script>

<body style = "background-color: whitesmoke;">
    <ul>
        <li><a href = "#" id = "home-link">Home</a></li>
        <li><a href = "#" id = "my-team-link">My team</a></li>
        <li><a href = "#" id = "my-league-link">My League</a></li>
        <li><a href = "#" id = "schedule-link">Schedule</a></li>
        <li><a href = "#" id = "tournament-link">Tournaments</a></li>
        <li><a href = "#" id = "results-link">Results</a></li>
        <li style="float:right"><a class="active" href = "#" id = "logout-link">Log Out</a></li>
    </ul>
    <h1 class = "header">My Teams</h1>
    <?php
        session_start(); // Start session for session variable

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

        $GU_ID = $_SESSION["guid"]; // Set local variable to session variable

        // Check if player is added
        if (isset($_POST['guidOfAdded'])) {
            // sanitize GUID
            $sanitizedAddedGUID = filter_var($_POST['guidOfAdded'], FILTER_SANITIZE_STRING);
            
            // validate integer
            if (!filter_var($sanitizedAddedGUID, FILTER_VALIDATE_INT) === false) {
                echo '<script>alert("Added user must be an integer")</script>';
                exit();
            } 
            $userToAdd = $sanitizedAddedGUID;
            unset($_POST['guidOfAdded']);

            //Connect to database
            $conn = mysqli_connect($server, $username, $password, $database);

            // Check connection
            if (!$conn) {
                die('Error: ' . mysqli_connect_error());
                console.log("error"); 
            }

            // Write query to see if user is on a team
            $playerQuery = "SELECT u.GU_ID " .
                           "FROM userOnTeam u " . 
                           "WHERE u.GU_ID = ?;";

            // Run query
            $stmt = $conn->stmt_init();
            $stmt->prepare($playerQuery);
            $stmt->bind_param("i", $userToAdd);
            $stmt->execute();
            $stmt->bind_result($playerToAdd);

            if($stmt->fetch()) {
                // User is on another team
                echo '<script>alert("That user is already on a team")</script>';
            }
            else {
                $stmt->close();

                // Reset connection
                $conn = mysqli_connect($server, $username, $password, $database);

                // Write query to get team name of logged in user 
                $teamQuery = "SELECT u.team_n " . 
                             "FROM userOnTeam u " .
                             "WHERE u.GU_ID = ?;";

                // Run query
                $stmt = $conn->stmt_init();
                $stmt->prepare($teamQuery);
                $stmt->bind_param("i", $GU_ID);
                $stmt->execute();
                $stmt->bind_result($team_n);

                if ($stmt->fetch()) {
                    // There is a team to add the user to

                    // Reset connection
                    $conn = mysqli_connect($server, $username, $password, $database);
    
                    // Write query to insert player on team
                    $insert = "INSERT INTO userOnTeam (GU_ID, team_n, is_captain) VALUES (" . $userToAdd . ", \"" . $team_n . "\", 0);";
                    if ($conn->query($insert) === TRUE) {
                        // Player successfully inserted and reload teams
                        header("Location: https://gonzaga-intramural.herokuapp.com/myTeam.php");
                    }
                    else {
                        // Error adding the user
                        echo '<script>alert("Error adding user")</script>';
                    }
                    
                }
                else {
                    echo '<script>alert("You are not on a team...")</script>';
                }
            }
        }

        // Join team
        if (isset($_POST['team_n'])) {
            // sanitize team name
            $sanitizedTeamName = filter_var($_POST['team_n'], FILTER_SANITIZE_STRING);
            $joinTeam = $sanitizedTeamName;
            unset($_POST['c_team_n']);

            // Connect to database
            $conn = mysqli_connect($server, $username, $password, $database);

            // Check connection
            if (!$conn) {
                die('Error: ' . mysqli_connect_error());
                console.log("error"); 
            }

            // Write query to check if player is on team
            $playerQuery = "SELECT u.GU_ID " .
                           "FROM userOnTeam u " . 
                           "WHERE u.GU_ID = ?;";
            
            // Run the query
            $stmt = $conn->stmt_init();
            $stmt->prepare($playerQuery);
            $stmt->bind_param("i", $GU_ID);
            $stmt->execute();
            $stmt->bind_result($playerToAdd);
               
            if($stmt->fetch()) {
                echo '<script>alert("You are already on a team")</script>';
            }
            else{
                $stmt->close();
                $conn = mysqli_connect($server, $username, $password, $database);

                if (!$conn) {
                    die('Error: ' . mysqli_connect_error());
                    console.log("error"); 
                }

                // Write query to get team to join
                $teamQuery = "SELECT u.team_n " . 
                            "FROM team u " .
                            "WHERE u.team_n = ?;";

                // Run the query
                $stmt = $conn->stmt_init();
                $stmt->prepare($teamQuery);
                $stmt->bind_param("s", $joinTeam);
                $stmt->execute();
                $stmt->bind_result($team_n);

                if ($stmt->fetch()) {
                    // Insert user into that team
                    $conn = mysqli_connect($server, $username, $password, $database);
                    $insertPlayer =  "INSERT INTO userOnTeam (GU_ID, team_n, is_captain) VALUES (" . $GU_ID . ", \"" . $joinTeam . "\", 0);";
                    if ($conn->query($insertPlayer) === TRUE){
                        header("Location: https://gonzaga-intramural.herokuapp.com/myTeam.php");
                    }
                    else{
                        echo '<script>alert("Error adding player to team")</script>';
                    }
                }
                else {
                    // Team name does not exist
                    echo '<script>alert("Team does not exist")</script>';
                }
            }

        }

        // Create new team
        if (isset($_POST['c_team_n'])) {
            // User wants to create a new team

            // Sanitize team name
            $teamToAdd = filter_var($_POST['c_team_n'], FILTER_SANITIZE_STRING);
            unset($_POST['c_team_n']);

            // Connect to database
            $conn = mysqli_connect($server, $username, $password, $database);

            // Check connection
            if (!$conn) {
                die('Error: ' . mysqli_connect_error());
                console.log("error"); 
            }

            // Write query to check if the user is on a team already
            $playerQuery = "SELECT u.GU_ID " .
                           "FROM userOnTeam u " . 
                           "WHERE u.GU_ID = ?;";

            // Run query
            $stmt = $conn->stmt_init();
            $stmt->prepare($playerQuery);
            $stmt->bind_param("i", $GU_ID);
            $stmt->execute();
            $stmt->bind_result($playerToAdd);

            if($stmt->fetch()) {
                // User is already on a team
                echo '<script>alert("You are already on a team")</script>';
            }
            else {
                // User not on a team
                $stmt->close();

                // Reset connection
                $conn = mysqli_connect($server, $username, $password, $database);

                // Check connection
                if (!$conn) {
                    die('Error: ' . mysqli_connect_error());
                    console.log("error"); 
                }

                // Write query to check if the team name exists
                $teamQuery = "SELECT u.team_n " . 
                "FROM team u " .
                "WHERE u.team_n = ?;";

                // Run query
                $stmt = $conn->stmt_init();
                $stmt->prepare($teamQuery);
                $stmt->bind_param("s", $teamToAdd);
                $stmt->execute();
                $stmt->bind_result($team_n);

                if ($stmt->fetch()) {
                    // Team name exists
                    echo '<script>alert("That team name already exists")</script>';
                }
                else {
                    // Reset connection
                    $conn = mysqli_connect($server, $username, $password, $database);
    
                    // Write team insertion and player insertion
                    $insertTeam = "INSERT INTO team (team_n, wins, losses, ties, sportsmanship_rating) VALUES (" . "\"" . $teamToAdd . "\", " . "0, " . "0, " . "0, " . "0" . ");";
                    $insertPlayer = "INSERT INTO userOnTeam (GU_ID, team_n, is_captain) VALUES (" . $GU_ID . ", \"" . $teamToAdd . "\", 1);";
                    if ($conn->query($insertTeam) === TRUE) {
                        // Team inserted
                        if ($conn->query($insertPlayer) === TRUE) {
                            // Player inserted and reload page
                            header("Location: https://gonzaga-intramural.herokuapp.com/myTeam.php");
                        }
                        else {
                            // Error inserting team
                            echo '<script>alert("Error adding user to new team")</script>';
                        }
                    }
                    else {
                        // Error adding team
                        echo '<script>alert("Error adding team")</script>';
                    }
                }
            }
        }

        // leaving team
        if (isset($_POST['leave_team_guid'])) {
            // Sanitize GUID
            $sanitizedLeaveGUID = filter_var($_POST['leave_team_guid'], FILTER_SANITIZE_STRING);
            
            // validate integer
            if (!filter_var($sanitizedLeaveGUID, FILTER_VALIDATE_INT) === false) {
                echo '<script>alert("Your GUID must be an integer")</script>';
                exit();
            } 
            $userToDrop = $sanitizedLeaveGUID;
            unset($_POST['leave_team_guid']);
            $conn = mysqli_connect($server, $username, $password, $database);

            // check connection
            if (!$conn) {
                die('Error: ' . mysqli_connect_error());
                console.log("error"); 
            }

            // check if they are on team
            $playerQuery = "SELECT u.GU_ID " .
                           "FROM userOnTeam u " . 
                           "WHERE u.GU_ID = ?;";
            $stmt = $conn->stmt_init();
            $stmt->prepare($playerQuery);
            $stmt->bind_param("i", $userToDrop);
            $stmt->execute();
            $stmt->bind_result($dropUser);

            // delete
            if($stmt->fetch()) {
                $conn = mysqli_connect($server, $username, $password, $database);
                $deleteQuery = "DELETE FROM userOnTeam WHERE GU_ID = " . $dropUser ."";
                if($conn->query($deleteQuery) === TRUE){
                    header("Location: https://gonzaga-intramural.herokuapp.com/myTeam.php"); 
                }
                else{
                    echo '<script>alert("Error removing user")</script>';
                }
            }
            else {
                echo '<script>alert("You are not on a team")</script>';
            }

        }
        
        // Write query to get the team name of the signed in user
        $teamQuery = "SELECT u.team_n " . 
                     "FROM userOnTeam u " .
                     "WHERE u.GU_ID = ?;";

        // Run query
        $stmt = $conn->stmt_init();
        $stmt->prepare($teamQuery);
        $stmt->bind_param("i", $GU_ID);
        $stmt->execute();
        $stmt->bind_result($team_n);

        if($stmt->fetch()){
            // There is a team the user is on
            echo "<h2 class = \"subheading\">" . $team_n . "</h2>";
            echo "<div>";
            echo "<table class = \"team-table\">\n";
            echo "<tr>\n";
            echo "<th>Username</th>\n";
            echo "<th>Captain?</th>\n";
            echo "</tr>\n";

            // Write query to get players on the team
            $query = "SELECT ut.GU_ID, u.user_n, ut.is_captain " . 
                     "FROM userOnTeam ut JOIN user u USING(GU_ID) " . 
                     "WHERE ut.team_n = ?;";
            
            // Run the query
            $stmt = $conn->stmt_init();
            $stmt->prepare($query);
            $stmt->bind_param("s", $team_n);
            $stmt->execute();
            $stmt->bind_result($GU_ID, $user_n, $is_captain);

            while($stmt->fetch()) {
                // Populate the table
                echo "<tr>\n";
                echo "<td>" . $user_n . "</td>" . "\n";
                echo "<td>" . $is_captain . "</td>" . "\n";
                echo "</tr>\n";
            }
            echo "</table>";
            echo "</div>";

            $stmt->close();
        }
        else {
            // The user is not on a team
            echo "<h2 class = \"subheading\">You are not on a team</h2>\n";
        }
        mysqli_close($conn);
    ?>
    
    <div>
    <form id = "add-player" action="myTeam.php" method="POST">
        <input class = "my-team-buttons" id = "add-player-btn" type="submit" value="Add Player">
        <input type="text" name="guidOfAdded" id="guid" placeholder = "GUID">  
    </form>  
    <form id = "join-team" action="myTeam.php" method="POST">
        <input class = "my-team-buttons" id = "join-team-btn" type="submit" value="Join Team">
        <input type="text" name="team_n" id="team-n" placeholder = "Team Name"> 
    </form>  
    <form id = "join-team" action="myTeam.php" method="POST">
        <input class = "my-team-buttons" id = "create-team-btn" type="submit" value="Create Team">
        <input type="text" name="c_team_n" id="c_team-n" placeholder = "Team Name"> 
    </form>  
    <form id = "leave-team" action="myTeam.php" method="POST">
        <input class = "my-team-buttons" id = "leave-team-btn" type="submit" value="Leave Team">
        <input type="text" name="leave_team_guid" id="leave_team-n" placeholder = "Your GUID"> 
    </form>  
</body>
</html>