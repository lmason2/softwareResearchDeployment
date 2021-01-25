/*
Luke Mason
CPSC 314: WebDev Final Project
myLeagueJScript.js
Javascript file for myLeague.php
*/

window.onload = function() {
    var homeLink = document.getElementById("home-link");
    var myTeamLink = document.getElementById("my-team-link");
    var myLeagueLink = document.getElementById("my-league-link");
    var scheduleLink = document.getElementById("schedule-link");
    var createLink = document.getElementById("tournament-link");
    var logoutLink = document.getElementById("logout-link");
    var leagueRulesButton = document.getElementById("view_league_rules_button");
    var resultsLink = document.getElementById("results-link");

    homeLink.href = "home.php";
    myTeamLink.href = "myTeam.php";
    myLeagueLink.href = "myLeague.php";
    scheduleLink.href = "schedule.php";
    createLink.href = "tournament.php";
    logoutLink.href = "login.php";
    resultsLink.href = "results.php";
    leagueRulesButton.addEventListener("click", function() {
        document.location.href = "leagueRules.php";
    })
}