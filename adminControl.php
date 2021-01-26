<!--
Luke Mason
CPSC 314: WebDev Final Project
adminControl.php
Page to let the admin add appropriate values to database
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="loginStyle.css">
</head>

<body style = "background-color: whitesmoke;">
    <?php
        // Check password
        if(isset($_POST["password"])) {
            if ($_POST["password"] != "asdfghjkl;'") {
                $_POST['wrongPassword'] = "set";
                header("Location: https://gonzaga-intramural.herokuapp.com/admin.php");
            }
        }

        // Adding League
        if(isset($_POST['league_id'])) {
            if (isset($_POST['max_players']) && isset($_POST['league_level']) 
             && isset($_POST['gender']) && isset($_POST['sport'])) {
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

                $insert = "INSERT INTO league (league_ID, rule, max_players, league_level, gender, sport) VALUES (" . $_POST['league_id'] . ", \"See Rules\", " . 
                          $_POST['max_players'] . ", \"" . $_POST['league_level'] . "\", \"" . $_POST['gender'] . "\", \"" . $_POST['sport'] . "\");";
                if ($conn->query($insert) === TRUE) {
                    // League successfully inserted and notify
                    echo '<script>alert("Success creating new league")</script>';
                }
                else {
                    // Error adding the league
                    echo '<script>alert("Error creating league")</script>';
                }

            }
            else {
                echo '<script>alert("Please fill all league fields")</script>';
            }
        }

        // Adding scheduled game
        if(isset($_POST["s_team_one"])){
            if(isset($_POST["s_team_two"]) && isset($_POST["s_date_of_game"]) && isset($_POST["s_location"])){
                $server = "cps-database.gonzaga.edu";
                $username = "lmason2";
                $password = "Gozagsxc17";
                $database = "lmason2_DB";
                $conn = mysqli_connect($server, $username, $password, $database);
                // check connection
                if (!$conn) {
                    die('Error: ' . mysqli_connect_error());
                    console.log("error"); 
                }

                $insertGame = "INSERT INTO schedule (team_one, team_two, date_of_game, game_location) VALUES (\"" . $_POST["s_team_one"] . "\", \"" . $_POST["s_team_two"] . "\", " . $_POST["s_date_of_game"] . ", \"" .$_POST["s_location"]."\");";
                if ($conn->query($insertGame) === TRUE) {
                    echo '<script>alert("Success adding schedule")</script>';
                }
                else {
                    echo '<script>alert("Error adding game")</script>';
                }
            }
            else{
                echo '<script>alert("Please fill in all schedule fields.")</script>';
            }
        }


         // Adding Result
        if(isset($_POST['r_team_one'])) {
            if (isset($_POST['r_team_two']) && isset($_POST['r_date_of_game']) 
             && isset($_POST['r_team_one_score']) && isset($_POST['r_team_two_score'])) {
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

                $insert = "INSERT INTO results (team_one, team_two, date_of_game, team_one_score, team_two_score) VALUES (\"" . $_POST['r_team_one'] . "\", \"" . $_POST['r_team_two'] . 
                        "\", " . $_POST['r_date_of_game'] . ", " . $_POST['r_team_one_score'] . ", " . $_POST['r_team_two_score'] . ");";
                if ($conn->query($insert) === TRUE) {
                    // Result successfully inserted and notify
                    echo '<script>alert("Success creating new result")</script>';
                    
                    $conn = mysqli_connect($server, $username, $password, $database);

                    $teamOneQuery = "SELECT t.wins, t.losses, t.ties " . 
                                    "FROM team t " . 
                                    "WHERE t.team_n = ?";
                    $stmt = $conn->stmt_init();
                    $stmt->prepare($teamOneQuery);
                    $stmt->bind_param("s", $_POST['r_team_one']);
                    $stmt->execute();
                    $stmt->bind_result($one_wins, $one_losses, $one_ties);


                    $conn = mysqli_connect($server, $username, $password, $database);

                    $teamTwoQuery = "SELECT t.wins, t.losses, t.ties " . 
                                    "FROM team t " . 
                                    "WHERE t.team_n = ?";
                    $stmt = $conn->stmt_init();
                    $stmt->prepare($teamTwoQuery);
                    $stmt->bind_param("s", $_POST['r_team_two']);
                    $stmt->execute();
                    $stmt->bind_result($two_wins, $two_losses, $two_ties);

                    if ($_POST['r_team_one_score'] > $_POST['r_team_two_score']) {
                        // Increment team one wins, increment team two losses
                        $one_wins = $one_wins + 1;
                        $two_losses = $two_losses + 1;
                    }
                    else if ($_POST['r_team_one_score'] < $_POST['r_team_two_score']) {
                        $one_losses = $one_losses + 1;
                        $two_wins = $two_wins + 1;
                    }
                    else {
                        $one_ties = $one_ties + 1;
                        $one_ties = $one_ties + 1;
                    }

                    // Make the updates
                    $conn = mysqli_connect($server, $username, $password, $database);
                    $updateOne = "UPDATE team SET wins = " . $one_wins . ", losses = " . $one_losses . ", ties = " . $one_ties . " WHERE team_n = \"" . $_POST['r_team_one'] . "\";";
                    if ($conn->query($updateOne) === TRUE) {
                        echo '<script>alert("Updated team one")</script>';
                    } else {
                        echo '<script>alert("Error updating team one")</script>';
                    }

                    $conn = mysqli_connect($server, $username, $password, $database);
                    $updateTwo = "UPDATE team SET wins = " . $two_wins . ", losses = " . $two_losses . ", ties = " . $two_ties . " WHERE team_n = \"" . $_POST['r_team_two'] . "\";";
                    if ($conn->query($updateTwo) === TRUE) {
                        echo '<script>alert("Updated team two")</script>';
                    } else {
                        echo '<script>alert("Error updating team two")</script>';
                    }

                }
                else {
                    // Error adding result
                    echo '<script>alert("Error adding result")</script>';
                }

            }
            else {
                echo '<script>alert("Please fill all results fields")</script>';
            }
        }

        // Adding scheduled game
        if(isset($_POST["tourney_name"])){
            if(isset($_POST["tourney_date"])){
                $server = "cps-database.gonzaga.edu";
                $username = "lmason2";
                $password = "Gozagsxc17";
                $database = "lmason2_DB";
                
                $conn = mysqli_connect($server, $username, $password, $database);
                // check connection
                if (!$conn) {
                    die('Error: ' . mysqli_connect_error());
                    console.log("error"); 
                }

                $insertTourney = "INSERT INTO tournament (tourney_n, tourney_date) VALUES (\"" . $_POST["tourney_name"] . "\", " . $_POST["tourney_date"]. ");";
                if ($conn->query($insertTourney) === TRUE) {
                    echo '<script>alert("Success adding tournament")</script>';
                }
                else {
                    echo '<script>alert("Error adding tournament")</script>';
                }
            }
            else{
                echo '<script>alert("Please fill in all schedule fields.")</script>';
            }
        }
    ?>
    <h1 class = "header">Database Controls</h1>
    <h2 class = "subheading">League Controls</h2>
    <div>
        <form action = "adminControl.php" method="POST">
            <label class = "login-labels" for="league_id">League ID: </label>
                <input type="text" name="league_id">
            <label class = "login-labels" for="max_players">Max Players: </label>
                <input type="text" name="max_players">
            <label class = "login-labels" for="league_level">League Level: </label>
                <input type="text" name="league_level">
            <label class = "login-labels" for="gender">Gender: </label>
                <input type="text" name="gender">
            <label class = "login-labels" for="sport">Sport: </label>
                <input type="text" name="sport">
            <input class = "login-btn-style center-buttons" id = "league-btn" type="submit" value="Submit">
        </form>  
    </div>
    
    <h2 class = "subheading">Schedule Controls</h2>
    <div>
        <form action = "adminControl.php" method="POST">
            <label class = "login-labels" for="s_team_one">Team One: </label>
                <input type="text" name="s_team_one">
            <label class = "login-labels" for="s_team_two">Team Two: </label>
                <input type="text" name="s_team_two">
            <label class = "login-labels" for="s_date_of_game">Date of game: </label>
                <input type="text" name="s_date_of_game">
            <label class = "login-labels" for="s_location">Game Location: </label>
                <input type="text" name="s_location">
            <input class = "login-btn-style center-buttons" id = "schedule-btn" type="submit" value="Submit">
        </form>  
    </div>
    
    <h2 class = "subheading">Results Controls</h2>
    <div>
        <form action = "adminControl.php" method="POST">
            <label class = "login-labels" for="r_team_one">Team One: </label>
                <input type="text" name="r_team_one">
            <label class = "login-labels" for="r_team_two">Team Two: </label>
                <input type="text" name="r_team_two">
            <label class = "login-labels" for="r_date_of_game">Date of game: </label>
                <input type="text" name="r_date_of_game">
            <label class = "login-labels" for="r_team_one_score">Team One Score: </label>
                <input type="text" name="r_team_one_score">
            <label class = "login-labels" for="r_team_two_score">Team Two Score: </label>
                <input type="text" name="r_team_two_score">
            <input class = "login-btn-style center-buttons" id = "results-btn" type="submit" value="Submit">
        </form>  
    </div>

    <h2 class = "subheading">Tournament Controls</h2>
    <div>
        <form action = "adminControl.php" method="POST">
            <label class = "login-labels" for="tourney_name">Tournament Name: </label>
                <input type="text" name="tourney_name">
            <label class = "login-labels" for="tourney_date">Tournament Date: </label>
                <input type="text" name="tourney_date">
            <input class = "login-btn-style center-buttons" id = "results-btn" type="submit" value="Submit">
        </form>  
    </div>
</body>
</html>