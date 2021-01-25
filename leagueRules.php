<!--
Luke Mason
CPSC 314: WebDev Final Project
leagueRules.php
Page to take the user when they click on league rules
-->
<html>

<head>
    <meta charset="utf-8" />
    <title>League Rules</title>
    <link rel="stylesheet" href="leagueRulesStyle.css">
</head>

<script src = "leagueRulesJScript.js"></script>

<body style = "background-color: whitesmoke;">
    <ul class = "navbar">
        <li><a href = "#" id = "home-link">Home</a></li>
        <li><a href = "#" id = "my-team-link">My team</a></li>
        <li><a href = "#" id = "my-league-link">My League</a></li>
        <li><a href = "#" id = "schedule-link">Schedule</a></li>
        <li><a href = "#" id = "tournament-link">Tournaments</a></li>
        <li><a href = "#" id = "results-link">Results</a></li>
        <li style="float:right"><a class="active" href = "#" id = "logout-link">Log Out</a></li>
    </ul>
    <h1 class = "header">League Rules</h1>
    <div id = "rules">
        <ol class = "center-class">
            <li>Must have at least 3 of each gender on team.</li><br>
            <li>Must report score within 24 hours of game.</li><br>
            <li>Must show up at least 5 minutes prior to start of game.</li><br>
            <li>Must bring zagcard to each game</li><br>
            <li>Failure to abide by the above rules will lead to forfeit or league termination</li><br>
        </ol>
    </div>
</body>

</html>