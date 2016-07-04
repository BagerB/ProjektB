<?php

include_once 'rpg.php';

$rpg = new RPG();

//Clean POST Data
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


$action = $_POST['action'];

if ($action === "createuser") {
    //User erstellen
    $return = $rpg->createUser($_POST["username"], $_POST["password"], $_POST["password2"]);
} elseif ($action === "loginuser") {
    $return = $rpg->loginUser($_POST["username"], $_POST["password"]);
} elseif ($action === "selectteam") {
    $return = $rpg->selectTeam($_POST["userid"], $_POST["teamid"]);
} elseif ($action === "selectclass") {
    $return = $rpg->selectClass($_POST["userid"], $_POST["classid"]);
} elseif ($action === "listteams") {
    $return = $rpg->listTeams();
} elseif ($action === "listclasses") {
    $return = $rpg->listClasses($_POST["teamid"]);
} elseif ($action === "listquestlog") {
    $return = $rpg->listQuestlog($_POST["userid"], $_POST["done"]);
} elseif ($action === "listquests") {
    $return = $rpg->listQuests($_POST["ownerid"]);
} elseif ($action === "setquest") {
    $return = $rpg->setQuestActive($_POST["userid"], $_POST["questid"]);
} elseif ($action === "setquestdone") {
    $return = $rpg->setQuestDone($_POST["userid"], $_POST["questid"]);
} elseif ($action === "listinventar") {
    $return = $rpg->listInventar($_POST["userid"]);
} elseif ($action === "transferitem") {
    $return = $rpg->transferToInventar($_POST["fromuserid"], $_POST["itemid"], $_POST["touserid"], $_POST["amount"]);
} elseif ($action === "getuserlist") {
    $return = $rpg->getUserList();
} elseif ($action === "getuserdetails") {
    $return = $rpg->getUserDetails($_POST["userid"]);
}elseif($action === "checkserver"){
    $return = array("status" => array("status"=>"ok"));
}elseif($action === "loadcode"){
    $return = $rpg->loadCode($_POST["code"],$_POST["userid"]);
}elseif($action === "addcode"){
    $return = $rpg->addCode($_POST["code"],$_POST["code_type"],$_POST["code_reward_id"],$_POST["code_reward_amount"]);
}
    echo json_encode($return, JSON_PRETTY_PRINT);
?>