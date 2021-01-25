<!--
Luke Mason
CPSC 314: WebDev Final Project
results.php
Page to let the user see their team's results
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>Results</title>
    <link rel="stylesheet" href="resultsStyle.css">
</head>

<script src = "resultsJScript.js"></script>

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
    <h1 class = "header">Results</h1>
    <?php
        session_start();

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
        
        // Write query to get the team the user is on
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
            echo "<h2 class = \"subheading\">" . $team_n . " Results:</h2>\n";
            echo "<div>\n";
            echo "<table class = \"results-table\">\n";
            echo "<tr>\n";
            echo "<th>Team One</th>\n";
            echo "<th>Team Two</th>\n";
            echo "<th>Date of Game</th>\n";
            echo "<th>Team One Score</th>\n";
            echo "<th>Team Two Score</th>\n";
            echo "</tr>\n";

            $stmt->close();

            // Write the query to get the results of the team
            $query = "SELECT r.team_one, r.team_two, r.date_of_game, r.team_one_score, r.team_two_score " .
                     "FROM results r " .
                     "WHERE r.team_one = ? OR r.team_two = ?;";
            
            // Run the query
            $stmt = $conn->stmt_init();
            $stmt->prepare($query);
            $stmt->bind_param("ss", $team_n, $team_n);
            $stmt->execute();
            $stmt->bind_result($team_one, $team_two, $date_of_game, $team_one_score, $team_two_score);

            while($stmt->fetch()) {
                // Populate table
                echo "<tr>\n";
                echo "<td>" . $team_one . "</td>" . "\n";
                echo "<td>" . $team_two . "</td>" . "\n";
                echo "<td>" . $date_of_game . "</td>" . "\n";
                echo "<td>" . $team_one_score . "</td>" . "\n";
                echo "<td>" . $team_two_score . "</td>" . "\n";
                echo "</tr>\n";
            }
            echo "</table>";
            echo "</div>";

            $stmt->close();
        }
        else {
            // User does not have a team
            echo "<p class = \"center-class\">No Team<p>\n";
        }
        mysqli_close($conn);
    ?>
</body>
</html>