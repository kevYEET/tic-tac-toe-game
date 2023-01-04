<?php
session_start();

if (!isset($_SESSION["game_id"])) {
    header("Location: index.php");
    exit();
}
require_once "dbconnector.php";

if($_GET["field"] > 8 || $_GET["field"] < 0 || !is_numeric($_GET["field"])){
    header("location: game.php?message=1");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM `games` WHERE `game_id`=?");
$stmt->bind_param("s", $_SESSION["game_id"]);
$stmt->execute();
$playfield = $stmt->get_result()->fetch_row();
$playfield_json = json_decode($playfield[2], true);

if($playfield[3] != "YTD"){
    header("location: game.php?message=3");
    exit();
}

// Check user move
if($playfield_json[$_GET["field"]] != "-"){
    header("location: game.php?message=2");
    exit();
} else {
    $playfield_json[$_GET["field"]] = "X";
}

// Computer move
if(checkIfEmptySpaceLeft($playfield_json)){
    $computer_choice = rand(0,8);
    while($playfield_json[$computer_choice] != "-"){
        $computer_choice = rand(0,8);
    }
}

$playfield_json[$computer_choice] = "O";

$playfield_encoded = json_encode($playfield_json);

$_SESSION["playfield"] = $playfield_encoded;
$stmt = $conn->prepare("UPDATE `games` SET `playfield` = ? WHERE `game_id`=?");
$stmt->bind_param("ss", $playfield_encoded, $_SESSION["game_id"]);
$stmt->execute();

if(checkIfWin("X", $playfield_json)){
    $stmt = $conn->prepare("UPDATE `games` SET `winner` = ? WHERE `game_id`=?");
    $winner = "User";
    $stmt->bind_param("ss", $winner, $_SESSION["game_id"]);
    $stmt->execute();
    $_SESSION["winner"] = $winner;
} else if(checkIfWin("O", $playfield_json)){
    $stmt = $conn->prepare("UPDATE `games` SET `winner` = ? WHERE `game_id`=?");
    $winner = "Computer";
    $stmt->bind_param("ss", $winner, $_SESSION["game_id"]);
    $stmt->execute();
    $_SESSION["winner"] = $winner;
} else if(!checkIfEmptySpaceLeft($playfield_json)){
    $stmt = $conn->prepare("UPDATE `games` SET `winner` = ? WHERE `game_id`=?");
    $winner = "Tie";
    $stmt->bind_param("ss", $winner, $_SESSION["game_id"]);
    $stmt->execute();
    $_SESSION["winner"] = $winner;
}

header("location: game.php");

function checkIfWin($c, $p){
    // Row 1
    if($p[0] == $c && $p[1] == $c && $p[2] == $c){
        return true;
    }

    // Row 2
    if($p[3] == $c && $p[4] == $c && $p[5] == $c){
        return true;
    }

    // Row 3
    if($p[6] == $c && $p[7] == $c && $p[8] == $c){
        return true;
    }

    // Col 1
    if($p[0] == $c && $p[3] == $c && $p[6] == $c){
        return true;
    }

    // Col 2
    if($p[1] == $c && $p[4] == $c && $p[7] == $c){
        return true;
    }

    // Col 3
    if($p[2] == $c && $p[5] == $c && $p[8] == $c){
        return true;
    }

    // Diag 1
    if($p[0] == $c && $p[4] == $c && $p[8] == $c){
        return true;
    }

    // Diag 2
    if($p[2] == $c && $p[4] == $c && $p[6] == $c){
        return true;
    }

    return false;
}

function checkIfEmptySpaceLeft($p){
    if(in_array("-", $p)){
        return true;
    }
    return false;
}