<!--
Luke Mason
CPSC 314: WebDev Final Project
myLeague.php
Page to let the user see their league
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>My Team</title>
    <link rel="stylesheet" href="myLeagueStyle.css">
</head>

<script src = "myLeagueJScript.js"></script>

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
    <h2 class = "header"> My Leagues </h2>
    
    <?php
        session_start(); // Start the session

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

        // Check if user wants to enter new league
        if (isset($_POST['leagueChoice'])) {
            // don't need to sanitize because league is passed from select
            $league = $_POST['leagueChoice'];
            unset($_POST['leagueChoice']);

            // Reset connection
            $conn = mysqli_connect($server, $username, $password, $database);

            // Check connection
            if (!$conn) {
                die('Error: ' . mysqli_connect_error());
                console.log("error"); 
            }

            // Get league they are in right now
            $playerQuery = "SELECT u.team_n " .
                           "FROM userOnTeam u " . 
                           "WHERE u.GU_ID = ?;";
            
            $stmt = $conn->stmt_init();
            $stmt->prepare($playerQuery);
            $stmt->bind_param("i", $GU_ID);
            $stmt->execute();
            $stmt->bind_result($team);
               
            if($stmt->fetch()) {
                // Remove them from league

                // Reset Connection and write deletion
                $conn = mysqli_connect($server, $username, $password, $database);
                $deleteQuery = "DELETE FROM teamInLeague WHERE team_n = \"" . $team . "\"";

                if($conn->query($deleteQuery) === TRUE) {
                    // User deleted from league
                    echo '<script>alert("Success removing team from league")</script>';
                }
                else {
                    // Error deleting user
                    echo '<script>alert("Error removing team from league")</script>';
                }
            }

            // Add user to league

            // Check connection and write insertion
            $conn = mysqli_connect($server, $username, $password, $database);
            $insertTeam =  "INSERT INTO teamInLeague (team_n, league_ID) VALUES (\"" . $team . "\", " . $league . ");";
            if ($conn->query($insertTeam) === TRUE){
                // Successful insertion, redirect user
                header("Location: http://barney.gonzaga.edu/~lmason2/htmlFiles/myLeague.php");
            }
            else{
                // Error inserting user
                echo '<script>alert("Error adding team to league")</script>';
            }
        }

        // Get the team the user is on
        $teamQuery = "SELECT ut.team_n " . 
                     "FROM userOnTeam ut JOIN user u USING(GU_ID) " . 
                     "WHERE GU_ID = ?;";
        
        // Run the query
        $stmt = $conn->stmt_init();
        $stmt->prepare($teamQuery);
        $stmt->bind_param("i", $GU_ID);
        $stmt->execute();
        $stmt->bind_result($team_n);

        if($stmt->fetch()){
            // User is on a team
            $stmt->close();

            // Write query to get the user's league
            $leagueQuery = "SELECT l.league_ID, l.gender, l.sport, l.league_level " . 
                            "FROM team t JOIN teamInLeague tl USING (team_n) JOIN league l USING (league_ID) " . 
                            "WHERE t.team_n = ?;";

            // Run the query
            $leagueStmt = $conn->stmt_init();
            $leagueStmt->prepare($leagueQuery);
            $leagueStmt->bind_param("s", $team_n);
            $leagueStmt->execute();
            $leagueStmt->bind_result($league_ID, $gender, $sport, $league);

            if ($leagueStmt->fetch()) {
                // User has a league
                echo "<h2 class = \"subheading\">" . $league . " " . $gender . " " . $sport . "</h2>\n";
                echo "<div>\n";
                echo "<table class = \"league-table\">\n";
                echo "<tr>\n";
                echo "<th> Team Name </th>\n";
                echo "<th> Wins </th>\n";
                echo "<th> Losses </th>\n"; 
                echo "</tr>\n";

                $leagueStmt->close();

                // Write query to get the team in the league
                $teamQuery = "SELECT t.wins, t.losses, tl.team_n " . 
                            "FROM team t JOIN teamInLeague tl USING (team_n) JOIN league l USING (league_ID) " . 
                            "WHERE l.league_ID = ?;";

                // Run the query
                $teamStmt = $conn->stmt_init();
                $teamStmt->prepare($teamQuery);
                $teamStmt->bind_param("i", $league_ID);
                $teamStmt->execute();
                $teamStmt->bind_result($win, $loss, $team);

                while($teamStmt->fetch()){
                    // Populate table with data
                    echo "<tr>\n";
                    echo "<td>" . $team . "</td>" . "\n";
                    echo "<td>" . $win . "</td>" . "\n";
                    echo "<td>" . $loss . "</td>" . "\n";
                    echo "</tr>\n";
                }
                echo "</table>";
                echo "</div>";
                $teamStmt->close();
            }
        }
        else{
            // User is not in a league
            echo "<p class = \" center-class\"> No League<p>\n";
        }
        mysqli_close($conn);
    ?>

    <div>
        <?php
            // Set up league join information 
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

            // Write query to get league information for select tag
            $query = "SELECT league_ID, gender, sport, league_level FROM league;";
            $result = mysqli_query($conn, $query);

            // Set up form with select menu
            echo "<form action = \"myLeague.php\" method = \"POST\">";
            echo "<select name = \"leagueChoice\">\n";
            echo "<option value = \"0\">Select League: </option>";
            if(mysqli_num_rows($result) > 0){
                // Populate select with options
                while($row = mysqli_fetch_assoc($result)){
                    echo "<option value = " . "\"" . $row['league_ID'] . "\">" .  $row['league_level'] . " ". $row['gender'] . " " . $row['sport'] . "</option\n>";
                }
            }
            echo "</select>\n";
            echo "<input type = \"submit\" class = \"league-button-style\" value = \"Join League\"/>";
            echo "</form>";
            mysqli_close($conn);
        ?>
        <button class = "league-button-style" id = "view_league_rules_button">View League Rules</button>
    </div>
</body>

</html>