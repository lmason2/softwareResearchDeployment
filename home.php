<!--
Luke Mason
CPSC 314: WebDev Final Project
home.php
Page to take the user to after login
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>Gonzaga Intramurals</title>
    <link rel="stylesheet" href="homeStyle.css">
</head>

<script src = "homeJScript.js"></script>

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
    <?php
        session_start(); // Used to hold session varibale
         
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
        
        // Check if the username variable has been set
        if(isset($_POST['username'])) {
            // Sanitize
            $sanitizedGUID = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $_SESSION["guid"] = $sanitizedGUID; // Set session variable
            $GU_ID = $sanitizedGUID;

            // Run query to see if user exists
            $authentication = "SELECT u.user_n " .
                              "FROM user u " .
                              "WHERE u.GU_ID = ?";

            // Run query
            $stmt = $conn->stmt_init();
            $stmt->prepare($authentication);
            $stmt->bind_param("i", $GU_ID);
            $stmt->execute();
            $stmt->bind_result($user_n);

            // Check if user exists
            if(!$stmt->fetch()) {
                // Redirect user to account creation
                header("Location: https://gonzaga-intramural.herokuapp.com/home.php"); 
                exit();
            }
        }
        $GU_ID = $_SESSION['guid']; // Set variable from session

        // Create headings
        echo "<h1 class = \"header\">Zag Intramurals</h1>";
        echo "<h2 id = \"results\" class = \"subheading\">Results</h2>";
        echo "<div id = \"results-query\">";

        // Connect to database
        $conn = mysqli_connect($server, $username, $password, $database);

        // Check connection
        if (!$conn) {
            die('Error: ' . mysqli_connect_error());
            console.log("error"); 
        }

        // Run query for results table
        $query = "SELECT * " .
                "FROM results;"; 
                //"ORDER BY r.date_of_game;";
        $result = mysqli_query($conn, $query); // Bind results

        if (mysqli_num_rows($result) > 0) {
            // Table for results
            echo "<table class = \"home-table\">\n";
            echo "<tr>\n";
            echo "<th>Team One</th>\n";
            echo "<th>Team Two</th>\n";
            echo "<th>Team One Score</th>\n";
            echo "<th>Team Two Score</th>\n";
            echo "<th>Date-Time</th>\n";
            echo "</tr>\n";
            while($row = mysqli_fetch_assoc($result)) {
                // Data to populate table
                echo "<tr>\n";
                echo "<td>" . $row["team_one"] . "</td>" . "\n";
                echo "<td>" . $row["team_two"] . "</td>" . "\n";
                echo "<td>" . $row["team_one_score"] . "</td>" . "\n";
                echo "<td>" . $row["team_two_score"] . "</td>" . "\n";
                echo "<td>" . $row["date_of_game"] . "</td>" . "\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            // No results
            echo "<p class = \"center-class\">No Results<p>\n";
        }
        echo "</div>";
        echo "<h2 id = \"upcoming\" class = \"subheading\">Upcoming</h2>"; 
        echo "<div id = \"upcoming-query\">";

        // Run query for upcoming games table
        $query = "SELECT * " .
                "FROM schedule s " . 
                "ORDER BY s.date_of_game;";
        $result = mysqli_query($conn, $query); // Bind results

        if (mysqli_num_rows($result) > 0) {
            // Table for upcoming games
            echo "<table class = \"home-table\">\n";
            echo "<tr>\n";
            echo "<th>Team One</th>\n";
            echo "<th>Team Two</th>\n";
            echo "<th>Date</th>\n";
            echo "<th>Location</th>\n";
            echo "</tr>\n";
            $today = date("Y-m-d H:i:s");
            while($row = mysqli_fetch_assoc($result)) {
                if ($today < $row["date_of_game"]) {
                    // Data to populate table
                    echo "<tr>\n";
                    echo "<td>" . $row["team_one"] . "</td>" . "\n";
                    echo "<td>" . $row["team_two"] . "</td>" . "\n";
                    echo "<td>" . $row["date_of_game"] . "</td>" . "\n";
                    echo "<td>" . $row["game_location"] . "</td>" . "\n";
                    echo "</tr>\n";
                }
            }
            echo "</table>";
        }
        else {
            echo "<p class = \"center-class\">No Results<p>\n";
        }
        echo "</div>";
        mysqli_close($conn);
    ?>
</body>
</html>