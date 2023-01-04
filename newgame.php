<?php
session_start();

if(isset($_SESSION["game_id"])){
    header("location: game.php");
    exit();
} else {
    require_once "dbconnector.php";
    $query = $conn -> prepare("INSERT INTO games (`game_id`, `playfield`, `winner`, `player_name`) VALUES (?,?,?,?)");
    $game_id = uniqid();
    $default_playfield = "{\"0\": \"-\",\"1\": \"-\",\"2\": \"-\",\"3\": \"-\",\"4\": \"-\",\"5\": \"-\",\"6\": \"-\",\"7\": \"-\",\"8\": \"-\"}";
    $winner = "YTD";
    $player_name = $_GET["playername"] == "" ? "Polgár Jenő" : $_GET["playername"];
    $query->bind_param("ssss", $game_id, $default_playfield, $winner, $player_name);
    $query->execute();
    $_SESSION["game_id"] = $game_id;
    $_SESSION["playfield"] = $default_playfield;
    $_SESSION["player_name"] = $player_name;
    $_SESSION["winner"] = $winner;
    header("location: game.php");
}