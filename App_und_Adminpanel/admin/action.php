<?php

include_once '../rpg.php';
$r = new RPG();

if ($_POST["action"] === "listplayers") {
    $return = $r->listPlayers($_POST["fieldid"]);
    echo json_encode($return);
    die();
}

switch ($action) {
    case "transferitem":
        $return = $r->transferToInventar($_POST["fromUserId"], $_POST["itemId"], $_POST["toUserId"], $_POST["itemAmount"]);
        break;
    case "edituserdetails":
        $return = $r->editUserDetails($_POST["userId"], $_POST["userName"], $_POST["userTeam"], $_POST["userClass"], $_POST["userHP"], $_POST["userStamina"], $_POST["userReaction"], $_POST["userXP"]);
        break;
    case "setquestdone":
        $return = $r->setQuestDone($_POST["userId"], $_POST["questId"]);
        break;
    case "addquest":
        $return = $r->addQuest($_POST["quest_owner"], $_POST["quest_name"], $_POST["quest_description"]);
        break;
    case "editquest":
        $return = $r->editQuest($_POST["questid"],$_POST["quest_owner"], $_POST["quest_name"], $_POST["quest_description"]);
        break;
    case "removefromquestlog":
        $return = $r->removeQuestFromQuestLog($_POST["questid"], $_POST["userid"]);
        break;
    case "deletequest":
        $return = $r->deleteQuest($_POST["questid"]);
        break;
    case "setquestactive":
        $return = $r->setQuestActive($_POST["userid"], $_POST["questid"],true);
        break;
    case "removefrominventar":
        $return = $r->removeFromInventar($_POST["userid"], $_POST["itemid"]);
        break;
    case "edititem":
        $return = $r->editItem($_POST["itemid"], $_POST["item_name"], $_POST["item_owner"], $_POST["item_class"], $_POST["item_damage"], $_POST["item_range"], $_POST["item_accuracy"], $_POST["item_firerate"], $_POST["item_special"], $_POST["item_quality"], $_POST["item_dice"],$_POST["item_image"],$_POST["item_content"]);
        break;
    case "additemtosystem":
        $return = $r->addItemToSystem($_POST["item_name"], $_POST["item_owner"], $_POST["item_class"], $_POST["item_damage"], $_POST["item_range"], $_POST["item_accuracy"], $_POST["item_firerate"], $_POST["item_special"], $_POST["item_quality"], $_POST["item_dice"],$_POST["item_image"],$_POST["item_content"]);
        break;
    case "removeitemfromsystem":
        $return = $r->removeItemFromSystem($_POST["itemid"]);
        break;
    case "giveitem":
        $return = $r->giveItemTo($_POST["itemid"], $_POST["userid"], $_POST["amount"],true);
        break;
    case "createuser":
        $return = $r->createUser($_POST["username"], $_POST["pass1"], $_POST["pass2"]);
        break;
    case "addfield":
        $return = $r->addField($_POST["field_name"]);
        break;
    case "addplayer":
        $return = $r->addPlayer($_POST["fieldid"], $_POST["userid"],$_POST["weaponid"]);
        break;
    case "removeplayer":
        $return = $r->removePlayer($_POST["fieldid"], $_POST["userid"]);
        break;
    case "removefield":
        $return = $r->removeField($_POST["fieldid"]);
        break;
    case "removeuser":
        $return = $r->deleteUser($_POST["userid"]);
        break;
    case "addcode":
        $return = $r->addCode($_POST["code"],$_POST["code_type"],$_POST["code_reward_id"],$_POST["code_reward_amount"]);
        break;
    case "removecode":
        $return = $r->removeCode($_POST["code"],$_POST["code_type"]);
        break;
    case "resetuserstats":
        $return = $r->resetUserStats($_POST["userid"]);
        break;
    case "editmessage":
        $return = $r->updateMessage($_POST["teamid"],$_POST["message"]);
        break;
}
echo "Status: " . $return["status"]["status"] . " <br>Message: " . $return["status"]["message"];
?>