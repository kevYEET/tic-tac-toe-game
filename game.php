<?php
session_start();

if (!isset($_SESSION["game_id"])) {
    header("Location: index.php");
    exit();
}
require_once "dbconnector.php";

?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tic-Tac-Toe játék</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        table, th, td {
            border: 1px solid black;
        }
        td{
            width: 100px;
            height: 100px;
            font-size: 72px;
            text-align: center;
        }
        a {
            text-decoration: none;
            color: black;
        }
        .card{
            margin-left: 20%;
            margin-right: 20%;
            margin-top: 20px;
        }
        body{
            background-image: url("https://www.xmple.com/wallpaper/blue-graph-paper-grid-purple-1920x1080-c2-e6e6fa-5f9ea0-l2-3-93-a-0-f-20.svg");
        }
    </style>
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Tic-Tac-Toe játék</span>
    </div>
</nav>
<div class="card">
    <div class="card-body">
<?php

$stmt = $conn->prepare("SELECT * FROM `games` WHERE `game_id`=?");
$stmt->bind_param("s", $_SESSION["game_id"]);
$stmt->execute();
$query_result = $stmt->get_result()->fetch_row();
$playfield_json = json_decode($query_result[2], true);

$htmlout = "";

$htmlout .= <<<HTML
<h2>{$query_result[4]} VS. a Gép</h2>
HTML;

if(isset($_GET["message"])){
    switch ($_GET["message"]){
        case 1:
            $htmlout .= <<<HTML
<h3 style="color:red;">Hiba! Nem létező mező!</h3>
HTML;
            break;
        case 2:
            $htmlout .= <<<HTML
<h3 style="color:red;">Hiba! Foglalt mező! Válassz másikat!</h3>
HTML;
            break;
        case 3:
            $htmlout .= <<<HTML
<h3 style="color:red;">Hiba! A meccs már véget ért!</h3>
HTML;
            break;
    }
}

if($query_result[3] == "YTD"){
    $htmlout .= <<<HTML
<div class="d-flex justify-content-center">
    <table>
        <tr>
            <td><a href="move.php?field=0">{$playfield_json[0]}</a></td>
            <td><a href="move.php?field=1">{$playfield_json[1]}</a></td>
            <td><a href="move.php?field=2">{$playfield_json[2]}</a></td>
        </tr>
        <tr>
            <td><a href="move.php?field=3">{$playfield_json[3]}</a></td>
            <td><a href="move.php?field=4">{$playfield_json[4]}</a></td>
            <td><a href="move.php?field=5">{$playfield_json[5]}</a></td>
        </tr>
        <tr>
            <td><a href="move.php?field=6">{$playfield_json[6]}</a></td>
            <td><a href="move.php?field=7">{$playfield_json[7]}</a></td>
            <td><a href="move.php?field=8">{$playfield_json[8]}</a></td>
        </tr>
    </table>
    </div>
<br>
<div class="d-flex justify-content-end">
    <a href="endgame.php" class="btn btn-danger">Játék feladása</a>
    </div>

HTML;
} else {
    $angol_magyar = array(
        "YTD" => "Folyamatban",
        "Computer" => "Gép",
        "User" => "Játékos",
        "Tie" => "Döntetlen"
    );
    $kieg = $query_result[3] == "Tie" ? "" : " győzött";
    $htmlout .= <<<HTML
<div class="d-flex justify-content-center">
    <h1>Eredmény: {$angol_magyar[$query_result[3]]}{$kieg}!</h1>
    </div>
    <div class="d-flex justify-content-center">
    <table>
        <tr>
            <td>{$playfield_json[0]}</td>
            <td>{$playfield_json[1]}</td>
            <td>{$playfield_json[2]}</td>
        </tr>
        <tr>
            <td>{$playfield_json[3]}</td>
            <td>{$playfield_json[4]}</td>
            <td>{$playfield_json[5]}</td>
        </tr>
        <tr>
            <td>{$playfield_json[6]}</td>
            <td>{$playfield_json[7]}</td>
            <td>{$playfield_json[8]}</td>
        </tr>
    </table>
    </div>
    <br>
    <div class="d-flex justify-content-end">
    <a href="endgame.php" class="btn btn-danger">Visszatérés a főoldalra!</a>
    </div>
</div>
HTML;

}

echo $htmlout;
?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
