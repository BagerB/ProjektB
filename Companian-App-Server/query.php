<?php

include_once 'rpg.php';

$rpg = new RPG();

//Clean POST Data
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//TEST

$action = $_POST['action'];

if ($action === "createuser") {
    //User erstellen
    $return = $rpg->createUser($_POST["username"], $_POST["password"], $_POST["password2"]);
    echo json_encode($return, JSON_PRETTY_PRINT);
} elseif ($action === "loginuser") {
    $return = $rpg->loginUser($_POST["username"], $_POST["password"]);
    echo json_encode($return, JSON_PRETTY_PRINT);
} elseif ($action === "selectteam") {
    $return = $rpg->selectTeam($_POST["userid"], $_POST["teamid"]);
    echo json_encode($return, JSON_PRETTY_PRINT);
} elseif ($action === "selectclass") {
    $return = $rpg->selectClass($_POST["userid"], $_POST["classid"]);
    echo json_encode($return, JSON_PRETTY_PRINT);
} elseif ($action === "listteams") {
    $return = $rpg->listTeams();
    echo json_encode($return, JSON_PRETTY_PRINT);
} elseif ($action === "listclasses") {
    $return = $rpg->listClasses($_POST["teamid"]);
    echo json_encode($return, JSON_PRETTY_PRINT);
} elseif ($action === "listquestlog") {
    $return = $rpg->listQuestlog($_POST["userid"], $_POST["done"]);
    echo json_encode($return, JSON_PRETTY_PRINT);
}
?>