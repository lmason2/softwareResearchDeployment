<!--
Luke Mason
CPSC 314: WebDev Final Project
tournament.php
Page to let the user see available tournaments
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>My Team</title>
    <link rel="stylesheet" href="tournamentStyle.css">
</head>

<script src = "tournamentJScript.js"></script>

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
    <h1 class = "header">Tournaments</h1>
    <?php
        session_start(); // Start session for session variable
        $GU_ID = $_SESSION["guid"]; // Set local variable to session variable

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

        // Check if user wants to join tourney
        if(isset($_POST['tourney_name'])) {
            // User wants to join tournament
            // Reset connection
            $conn = mysqli_connect($server, $username, $password, $database);

            // Write the query to get the team the user is on
            $teamQuery = "SELECT u.team_n FROM userOnTeam u WHERE u.GU_ID = ?";
           
            // Run the query
            $stmt = $conn->stmt_init();
            $stmt->prepare($teamQuery);
            $stmt->bind_param("i", $GU_ID);
            $stmt->execute();
            $stmt->bind_result($team_n);

            if($stmt->fetch()) {
                // User is on a team

                // Reset connection
                $conn = mysqli_connect($server, $username, $password, $database);
                
                // Write insertion
                $insert = "INSERT INTO teamInTournament (team_n, tourney_n) VALUES (\"" . $team_n . "\", \"" . $_POST['tourney_name'] . "\");";
                if ($conn->query($insert) === TRUE) {
                    // Successfully inserted team into tournament
                    echo '<script>alert("Success")</script>';
                }
                else {
                    // Error inserting team
                    echo '<script>alert("Problem entering tournament.")</script>';
                }
            }
            else {
                // User is not on a team
                echo '<script>alert("You are not on a team.")</script>';
            }  
        }

        // Query to get user's current tournaments
        $curTournamentsQuery = "SELECT t.tourney_n " . 
                               "FROM userOnTeam u JOIN teamInTournament t USING(team_n) " .
                               "WHERE u.GU_ID = ?;";
           
        // Run the query
        $stmt = $conn->stmt_init();
        $stmt->prepare($curTournamentsQuery);
        $stmt->bind_param("i", $GU_ID);
        $stmt->execute();
        $stmt->bind_result($tourney);

        echo "<h2 class = \"subheading\">Tournaments You Are Registered For: </h2>\n";
        if($stmt->fetch()) {
            // User is registered for tournaments
            echo "<h3 class = \"currentTournaments\">" . $tourney . "</h3>\n";
            while ($stmt->fetch()) {
                echo "<h3 class = \"currentTournaments\">" . $tourney . "</h3>\n";
            }
            echo "<h3 class = \"currentTournaments\">Contact lmason2@zagmail.gonzaga.edu to be removed.</h3>\n";
        }
        else {
            // User is not registered
            echo "<h3 class = \"currentTournaments\">No current registrations</h3>\n";
        }

        // Write query to view tournaments
        $query = "SELECT  t.tourney_n, t.tourney_date " . 
                 "FROM tournament t ";

        // Run the query
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Tournaments exists
            echo "<table class = \"tournament-table\">\n";
            echo "<tr>\n";
            echo "<th>Tournament Name</th>\n";
            echo "<th>Tournament Date</th>\n";
            echo "<th>Join</th>\n";
            echo "</tr>\n";
            while($row = mysqli_fetch_assoc($result)) {
                // Populate table with form data
                echo "<form action = \"tournament.php\" method = \"POST\">";
                echo "<tr>\n";
                echo "<td>" . $row["tourney_n"] . "</td>" . "\n";
                echo "<input type = \"hidden\" name = \"tourney_name\" value = \"" . $row["tourney_n"] . "\"" . ">";
                echo "<td>" . $row["tourney_date"] . "</td>" . "\n";
                echo "<td><input class = \"join-btn\" type = \"submit\" value = \"Join\"></td>";
                echo "</tr>\n";
                echo "</form>";
            }
            echo "</table>";
        }
        else {
            // There are no tournaments
            echo "<p class = \"center-class\">No Results<p>\n";
        }
        mysqli_close($conn);
    ?>
</body>
</html>