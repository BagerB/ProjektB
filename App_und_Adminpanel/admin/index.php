
<?php
$username = "systopia";
$password = "haw";
$nonsense = "hgi3uhi3njr3bfho34hif33o4jbg3";

if (isset($_COOKIE['PrivatePageLogin'])) {
   if ($_COOKIE['PrivatePageLogin'] == md5($password.$nonsense)) {
?>


<?php
include_once '../rpg.php';
$rpg = new RPG();

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$action = $_POST["action"];

$page = $_GET["page"];
$selectedUser = $_GET["user"];
$selectedItem = $_GET["item"];
$selectedQuest = $_GET["quest"];
$selectedField = $_GET["field"];
$selectedCode = $_GET["code"];


?>
<html>

    <head>
        <title>Admin</title>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>

        <script src="js/jquery-1.12.3.min.js" type="text/javascript"></script>
        <script src="js/functions.js" type="text/javascript"></script>

    </head>

    <body>
        <div class="loading"></div>


        <?php
        if (isset($action)) {
            echo '<div class="response">';
            include 'action.php';
            echo '</div>';
        }

        $userlist = $rpg->getUserList();
        $quests = $rpg->listQuests(null);
        $items = $rpg->listItems();
        $fields = $rpg->listFields();
        $codes = $rpg->listCodes();
        ?>

        <div class="nav">
            <a href="?page=users" >User</a>
            <a href="?page=items" >Items</a>
            <a href="?page=quests"  >Quests</a>
            <a href="?page=fields"  >Fields</a>
            <a href="?page=codes"  >Codes</a>
            <a href="?page=messages"  >Messages</a>
        </div>

        <div class="leftContent">
            <div id="searchBox">
                <input name="search" id="search" placeholder="Filter">
            </div>
            <div class="content">
                <?php
                if (strcasecmp($page, "items") == 0) {
                    foreach ($items["itemslist"] as $item) {
                        $itemteam = $item->item_owner;
                        ?>
                        <a class="userTeam<?php echo($itemteam); ?>" href="?page=items&item=<?php echo($item->id); ?>" id="leftContentButton"><?php echo($item->item_name);
                        ?></a>
                        <?php
                    }
                } elseif (strcasecmp($page, "quests") == 0) {

                    foreach ($quests["quests"] as $quest) {
                        $team = $quest->quest_owner;
                        ?>
                        <a class="userTeam<?php echo($quest->quest_owner); ?>" href="?page=quests&quest=<?php echo($quest->id); ?>" id="leftContentButton"><?php echo($quest->quest_name);
                        ?></a>
                        <?php
                    }
                } elseif (strcasecmp($page, "users") == 0) {

                    foreach ($userlist["userlist"] as $user) {
                        ?>
                        <a class="userTeam<?php echo($user->team); ?>" href="?page=users&user=<?php echo($user->id); ?>" id="leftContentButton"><?php echo($user->name); ?></a>
                        <?php
                    }
                } elseif (strcasecmp($page, "fields") == 0) {
                    foreach ($fields["fields"] as $field) {
                        ?>
                        <a  href="?page=fields&field=<?php echo($field->id); ?>" id="leftContentButton"><?php echo($field->feld_name); ?></a>
                        <?php
                    }
                } elseif (strcasecmp($page, "codes") == 0) {
                    foreach ($codes["codes"] as $code) {
                        ?>
                        <a  href="?page=codes&code=<?php echo($code->code); ?>" id="leftContentButton"><?php echo($code->code_type . ": " . $code->code); ?></a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="rightContent">
            <div class="content">
                <?php
                if (strcasecmp($page, "messages") == 0) {
                    $message1 = $rpg->getMessage("0");
                    $message2 = $rpg->getMessage("1");
                    $message3 = $rpg->getMessage("2");
                    ?>
                    <h1>Nachrichten:</h1>
                    <div class="row">
                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" value="editmessage" type="hidden">
                            <input name="teamid" value="0" type="hidden">
                            <strong> Nachricht für Verschwörungstheoretiker:</strong> <input size="50" name="message" value="<?php echo $message1["status"]["message"]; ?>" type="text">
                            <input name="submit" value="Speichern" type="submit">
                        </form>
                    </div>
                    <div class="row">
                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" value="editmessage" type="hidden">
                            <input name="teamid" value="1" type="hidden">
                            <strong class="w300"> Nachricht für Technokraten: </strong><input size="50" name="message" value="<?php echo $message2["status"]["message"]; ?>" type="text">
                            <input name="submit" value="Speichern" type="submit">
                        </form>
                    </div>
                    <div class="row">
                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" value="editmessage" type="hidden">
                            <input name="teamid" value="2" type="hidden">
                            <strong class="w300"> Nachricht für Avantgardisten:</strong> <input  size="50"name="message" value="<?php echo $message3["status"]["message"]; ?>" type="text">
                            <input name="submit" value="Speichern" type="submit">
                        </form>
                    </div>
                    <?php
                }
                if (strcasecmp($page, "users") == 0 && $selectedUser < 1) {
                    ?>
                    <h1>User erstellen:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <strong>Name: </strong>
                            <input name="action" value="createuser" type="hidden">
                            <input name="username" value="username" type="text">
                            <strong>Pass 1: </strong>
                            <input name="pass1" value="" type="password">
                            <strong>Pass 2: </strong>
                            <input name="pass2" value="" type="password">

                            <input name="submit" value="Erstellen" type="submit">
                        </form>
                    </div>
                    <?php
                }
                if (strcasecmp($page, "items") == 0 && $selectedItem < 1) {
                    ?>

                    <h1>Item hinzufügen:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="additemtosystem">
                            <strong class="w300">Für Team: </strong>
                            <select name="item_owner">
                                <option value="0">
                                    Verschwörungstheoretiker
                                </option>
                                <option value="1">
                                    Technokraten
                                </option>
                                <option value="2">
                                    Avantgardisten
                                </option>
                                <option selected="true" value="3">
                                    Alle
                                </option>
                            </select><br>
                            <strong class="w300">Item für Klasse: </strong>
                            <select name="item_accuracy">
                                <option value="0">
                                    Alle
                                </option>
                                <option value="1">
                                    Infanterist / Average Joe
                                </option>
                                <option value="2">
                                    Disassembler / Brawler
                                </option>
                                <option value="3">
                                    Mindscaper / Limit Breaker 	
                                </option>f
                                <option value="4">
                                    Ambidexter / Pistolero
                                </option>
                                <option value="5">
                                    Enforcer / Specialist
                                </option>
                            </select><br>  
                            <strong class="w300">Name: </strong>
                            <input name="item_name" type="text" value=""><br>
                            <strong class="w300">Item Klasse: </strong>
                            <input name="item_class" type="text" value=""><br>
                            <strong class="w300">Damage: </strong>
                            <input name="item_damage" type="number" value="0" min="0" max="2000"><br>
                            <strong class="w300">Range: </strong>
                            <input name="item_range" type="number" value="0" min="0" max="2000"><br>

                            <strong class="w300">Firerate: </strong>
                            <input name="item_firerate" type="number" value="0" min="0" max="2000"><br>
                            <strong class="w300">Special: </strong>
                            <input name="item_special" type="text" value=""><br>
                            <strong class="w300">Quality: </strong>
                            <input name="item_quality" type="text" value="default"><br>
                            <strong class="w300">Dice Acc: </strong>
                            <input name="item_dice" type="number" value="0" min="0" max="2000"><br>
                            <strong class="w300">Image: </strong>
                            <input name="item_image" type="text" value="default.png"><br>
                            <strong class="w300">Content: </strong>
                            <input name="item_content" type="text" value="item.png"><br>
                            <input type="submit" value="Item hinzufügen">
                        </form>
                    </div>
                    <h1>Item versenden:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="giveitem">

                            <select name="itemid" size="15">
                                <?php
                                foreach ($items["itemslist"] as $item) {
                                    echo '<option class="userTeam' . $item->item_owner . '" value="' . $item->id . '">' . $item->item_name . '</option>';
                                }
                                ?>
                            </select>
                            <strong style="font-size: 25px">&rarr;</strong>
                            <select name="userid" size="15">
                                <?php
                                foreach ($userlist["userlist"] as $userName) {
                                    echo '<option class="userTeam' . $userName->team . '" value="' . $userName->id . '">' . $userName->name . '</option>';
                                }
                                ?>
                            </select>
                            <strong>Anzahl: </strong> <input type="number" name="amount" min="1" value="1">
                            <input type="submit" value="Item zuweisen">
                        </form>
                    </div>
                    <?php
                }

                if (strcasecmp($page, "quests") == 0 && $selectedQuest < 1) {
                    /* ADD QUEST */
                    ?>
                    <h1>Quest hinzufügen:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="addquest">
                            <strong>Für Team: </strong>
                            <select name="quest_owner" >
                                <option value="0">
                                    Verschwörungstheoretiker
                                </option>
                                <option value="1">
                                    Technokraten
                                </option>
                                <option value="2">
                                    Avantgardisten
                                </option>
                                <option selected="true" value="3">
                                    Alle
                                </option>
                            </select>
                            <strong>Name:</strong>
                            <input name="quest_name" type="text" value="">
                            <strong>Beschreibung</strong>
                            <textarea name="quest_description" col="40" type="text" value=""></textarea>
                            <input type="submit" value="Quest hinzufügen">
                        </form>
                    </div>
                    <h1>Quest vergeben:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="setquestactive">

                            <select name="questid" size="15">
                                <?php
                                foreach ($quests["quests"] as $quest) {
                                    echo '<option class="userTeam' . $quest->quest_owner . '" value="' . $quest->id . '">' . $quest->quest_name . '</option>';
                                }
                                ?>
                            </select>
                            <strong style="font-size: 25px">&rarr;</strong>
                            <select name="userid" size="15">
                                <?php
                                foreach ($userlist["userlist"] as $userName) {
                                    echo '<option class="userTeam' . $userName->team . '" value="' . $userName->id . '">' . $userName->name . '</option>';
                                }
                                ?>
                            </select>
                            <input type="submit" value="Quest vergeben">
                        </form>
                    </div>

                    <?php
                }

                if (strcasecmp($page, "fields") == 0 && $selectedField < 1) {
                    ?>
                    <h1>Field hinzufügen:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="addfield">

                            <strong>Name:</strong>
                            <input name="field_name" type="text" value="">

                            <input type="submit" value="Field hinzufügen">
                        </form>
                    </div>
                    <h1>Spieler hinzufügen:</h1>
                    <div class='row'>

                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="addplayer">

                            <select name="fieldid" size="15">
                                <?php
                                foreach ($fields["fields"] as $field) {
                                    echo '<option  value="' . $field->id . '">' . $field->feld_name . '</option>';
                                }
                                ?>
                            </select>
                            <strong style="font-size: 25px">&rarr;</strong>
                            <select id="weaponChange" name="userid" size="15" onchange="">
                                <?php
                                foreach ($userlist["userlist"] as $userName) {
                                    echo '<option class="userTeam' . $userName->team . '" value="' . $userName->id . '">' . $userName->name . '</option>';
                                }
                                ?>
                            </select>
                            <strong style="font-size: 25px">&rarr;</strong>
                            <select id="weaponChangeTarget" name="weaponid[]" multiple="true" size="15">
                                <?php
                                foreach ($userlist["userlist"] as $userName) {
                                    $weapons = $rpg->listWeapons($userName->id);
                                    foreach ($weapons["weaponlist"] as $weapon) {
                                        echo '<option class="user' . $userName->id . ' hidden" value="' . $weapon->id . '">' . $weapon->item_name . '</option>';
                                    }
                                }
                                ?>
                            </select>

                            <input type="submit" value="Spieler hinzufügen">
                        </form>
                    </div>

                    <?php
                }
                if (strcasecmp($page, "codes") == 0 && $selectedCode < 1) {
                    ?>
                    <h1>Code hinzufügen:</h1>
                    <div class='row'>
                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="addcode">

                            <strong>Code (4 stellig ohne 0):</strong>
                            <input name="code" type="text" value="0000">
                            <strong>Typ:</strong>
                            <input type="text" name="code_type" value="item">

                            <strong>ID:</strong>
                            <input name="code_reward_id" type="text" value="0">
                            <strong>Anzahl(Nur für Items wichtig):</strong>
                            <input name="code_reward_amount" type="number"  min="1" value="1">
                            <input type="submit" value="Code hinzufügen">
                        </form>
                    </div>


                    <?php
                }

//selected
                if (isset($selectedUser)) {
                    if ($rpg->userExists($selectedUser)) {
                        $user = $rpg->getUserDetails($selectedUser)["userDetails"];
                        $inventar = $rpg->listInventar($selectedUser)["userInventar"];
                        $userQuests = $rpg->listQuestlog($selectedUser, "2");
                        ?>
                        <div style="float: right;">
                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="removeuser">
                                <input name="userid" type="hidden" value="<?php echo $user->id; ?>">
                                <input type="submit" value="! User komplett entfernen !" style="color: darkred">
                            </form>
                        </div>
                        <div class="overhead">
                            <?php echo $user->name; ?>
                        </div>
                        <h1>Details</h1>
                        <div class='row'>

                            <form  class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="edituserdetails">
                                <input name="userId" type="hidden" value="<?php echo $user->id; ?>">
                                <strong>ID</strong>: <?php echo $user->id; ?> 
                                <strong> Name:</strong>
                                <input name="userName" type='text' value="<?php echo $user->name; ?>">

                                <strong>Team</strong>: 
                                <select name="userTeam">
                                    <option <?php
                                    if ($user->team === "0") {
                                        echo 'selected="true"';
                                    }
                                    ?> value="0">
                                        Verschwörungstheoretiker
                                    </option>
                                    <option <?php
                                    if ($user->team === "1") {
                                        echo 'selected="true"';
                                    }
                                    ?> value="1">
                                        Technokraten
                                    </option>
                                    <option <?php
                                    if ($user->team === "2") {
                                        echo 'selected="true"';
                                    }
                                    ?> value="2">
                                        Avantgardisten
                                    </option>
                                </select>
                                <strong>Klasse</strong>: 
                                <select name="userClass">
                                    <?php
                                    $classes = $rpg->listClasses();
                                    foreach ($classes["classes"] as $class) {

                                        if ($user->class_id === $class->class_id) {
                                            $selected = 'selected="true"';
                                        } else {
                                            $selected = "";
                                        }
                                        if ($class->class_team === "1") {
                                            $classTeam = "T(1): ";
                                        } else if ($class->class_team === "2") {
                                            $classTeam = "A(2): ";
                                        } else {
                                            $classTeam = "N(0): ";
                                        }
                                        echo '<option ' . $selected . 'value="' . $class->class_id . '">' . $classTeam . $class->class_name . '</option>';
                                    }
                                    ?>
                                </select>
                                <strong>HP</strong>: 
                                <input name="userHP" type='number' min="0" value="<?php echo $user->hp; ?>">
                                <strong>Stamina</strong>:
                                <input name="userStamina" type='number' min="0" value="<?php echo $user->stamina; ?>">
                                <strong>Reaction</strong>:
                                <input name="userReaction" type='number' min="0" value="<?php echo $user->reaction; ?>">
                                <strong>XP</strong>:
                                <input name="userXP" type='number' min="0" value="<?php echo $user->xp; ?>">
                                | <input type="submit" value="Speichern">
                            </form>
                        </div>

                        <h1>Items</h1>
                        <?php
                        if (count($inventar) < 1) {
                            echo"<p>Keine Einträge<p>";
                        }
                        foreach ($inventar as $inventarItem) {
                            ?> 
                            <div class='row'>
                                <strong>Name: </strong> <?php echo $inventarItem->item_name; ?> | <strong>Anzahl:</strong> <?php echo $inventarItem->item_amount; ?> | <strong>Transfer: </strong> 
                                <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                    <input name="action" type="hidden" value="transferitem">
                                    <input name="itemId" type="hidden" value="<?php echo $inventarItem->item_id; ?> ">
                                    <input name="fromUserId" type="hidden" value="<?php echo $user->id; ?>">
                                    <select name="toUserId">
                                        <?php
                                        foreach ($userlist["userlist"] as $userName) {
                                            echo '<option value="' . $userName->id . '">' . $userName->name . '</option>';
                                        }
                                        ?>
                                    </select> 
                                    <input name="itemAmount" type='number' min="1" value="1" width="2"> 
                                    <input type="submit" value="Transfer starten">
                                </form>
                                <div style="float: right;">
                                    <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                        <input name="action" type="hidden" value="removefrominventar">
                                        <input name="itemid" type="hidden" value="<?php echo $inventarItem->item_id; ?> ">
                                        <input name="userid" type="hidden" value="<?php echo $user->id; ?>">
                                        <input type="submit" value="! Item aus Inventar entfernen !" style="color: darkred">
                                    </form>
                                </div>
                            </div>
                        <?php } ?>


                        <h1>Quests</h1>
                        <?php
                        if (count($userQuests["quests"]) < 1) {
                            echo"<p>Keine Einträge<p>";
                        }
                        foreach ($userQuests["quests"] as $quest) {
                            ?>
                            <div class='row'>

                                <strong>ID: </strong><?php echo $quest->id; ?> | <strong>Questname: </strong><?php echo $quest->quest_name; ?> | <strong>Auftraggeber</strong> <?php echo $quest->quest_owner; ?> |
                                <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                    <?php
                                    if ($quest->quest_done == 1) {
                                        ?>
                                        <strong>Status: </strong>Quest abgeschlossen!
                                        <input name="action" type="hidden" value="setquestactive">
                                        <input name="userid" type="hidden" value="<?php echo $quest->user_id; ?> ">
                                        <input name="questid" type="hidden" value="<?php echo $quest->quest_id; ?>">
                                        <input type="submit" value="Quest wieder aktivieren">
                                        <?php
                                    } else {
                                        ?>
                                        <input name="action" type="hidden" value="setquestdone">
                                        <input name="userId" type="hidden" value="<?php echo $quest->user_id; ?> ">
                                        <input name="questId" type="hidden" value="<?php echo $quest->quest_id; ?>">
                                        <input type="submit" value="Quest Abschließen">
                                        <?php
                                    }
                                    ?>
                                </form>

                                <div style="float: right;">
                                    <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                        <input name="action" type="hidden" value="removefromquestlog">
                                        <input name="questid" type="hidden" value="<?php echo $quest->quest_id; ?> ">
                                        <input name="userid" type="hidden" value="<?php echo $user->id; ?>">
                                        <input type="submit" value="! Quest aus Questlog entfernen !" style="color: darkred">
                                    </form>
                                </div>
                            </div> 
                            <?php
                        }
                    } else {
                        echo '<div id="error">User nicht gefunden.</div>';
                    }
                } elseif (isset($selectedQuest)) {

                    $questInfo = $rpg->getQuestInfo($selectedQuest);
                    $userlist = $rpg->getUserList();

                    if ($questInfo != null) {



                        $questTeam = $questInfo->quest_owner;
                        ?>
                        <div class="overhead">
                            <?php echo $questInfo->quest_name ?>
                        </div>
                        <h1>Quest Details:</h1>
                        <div class='row'>
                            <div style="float: right;">
                                <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING']; ?>">
                                    <input name="action" type="hidden" value="deletequest">
                                    <input name="questid" type="hidden" value="<?php echo $questInfo->id ?>">
                                    <input type="submit" value="! Quest löschen !" style="color: darkred">
                                </form>
                            </div>
                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING']; ?>">
                                <input name="action" type="hidden" value="editquest">
                                <input name="questid" type="hidden" value="<?php echo $questInfo->id ?>">
                                <div>
                                    <strong>Team: </strong>
                                    <p>
                                        <select name="quest_owner" >
                                            <option <?php
                                            if ($questTeam === "0") {
                                                echo 'selected="true"';
                                            }
                                            ?> value="0">
                                                Verschwörungstheoretiker
                                            </option>
                                            <option <?php
                                            if ($questTeam === "1") {
                                                echo 'selected="true"';
                                            }
                                            ?> value="1">
                                                Technokraten
                                            </option>
                                            <option <?php
                                            if ($questTeam === "2") {
                                                echo 'selected="true"';
                                            }
                                            ?> value="2">
                                                Avantgardisten
                                            </option>
                                            <option <?php
                                            if ($questTeam === "3") {
                                                echo 'selected="true"';
                                            }
                                            ?>  value="3">
                                                Alle
                                            </option>
                                        </select>

                                    </p>
                                </div>
                                <div><strong>ID: </strong><p><?php echo $questInfo->id; ?></p> 
                                    <strong>Name: </strong>
                                    <p>
                                        <input name="quest_name" type="text" value="<?php echo $questInfo->quest_name ?>">
                                    </p>
                                </div>

                                <div>
                                    <strong>Beschreibung: </strong>
                                    <p> 
                                        <textarea name="quest_description" col="40" type="text" value=""><?php echo nl2br($questInfo->quest_description) ?></textarea>
                                    </p>
                                </div>
                                <input type="submit" value="Quest speichern">
                            </form>
                        </div>
                        <div class="row">
                            <div>
                                <strong>Quest vergeben an:</strong>

                                <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                    <input name="action" type="hidden" value="setquestactive">
                                    <input name="questid" type="hidden" value="<?php echo $questInfo->id; ?>">
                                    <select name="userid">
                                        <?php
                                        foreach ($userlist["userlist"] as $userName) {
                                            echo '<option value="' . $userName->id . '">' . $userName->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <input type="submit" value="Quest vergeben">
                                </form>
                            </div>

                        </div>
                        <h1>Code erstellen: Quest vergeben</h1>
                        <div class='row'>
                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="addcode">

                                <strong>Code (4 stellig ohne 0):</strong>
                                <input name="code" type="text" value="0000">
                                <input type="hidden" name="code_type" value="quest">
                                <input name="code_reward_id" type="hidden" value="<?php echo $questInfo->id; ?>">
                                <input name="code_reward_amount" type="hidden" value="1">
                                <input type="submit" value="Code hinzufügen">
                            </form>
                        </div>



                        <?php
                        $codes = $rpg->getCodesByID("quest", $questInfo->id);
                        if (count($codes["codes"]) > 0) {
                            foreach ($codes["codes"] as $code) {
                                ?>
                                <div class="row code">
                                    <strong>Code: </strong><?php echo $code->code; ?> | <strong>Typ: </strong><?php echo $code->code_type; ?>
                                    <div style='float:right'>
                                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                            <input name="action" type="hidden" value="removecode">
                                            <input type="hidden" name="code_type" value="quest">
                                            <input name="code" type="hidden" value="<?php echo $code->code; ?>">
                                            <input type="submit" value="! Code entfernen !">
                                        </form>
                                    </div>    
                                </div>
                                <?php
                            }
                        } else {
                            echo "Keine Codes";
                        }
                        ?>
                        <h1>Code erstellen: Quest abschließen</h1>
                        <div class='row'>
                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="addcode">

                                <strong>Code (4 stellig ohne 0):</strong>
                                <input name="code" type="text" value="0000">
                                <input type="hidden" name="code_type" value="quest_done">
                                <input name="code_reward_id" type="hidden" value="<?php echo $questInfo->id; ?>">
                                <input name="code_reward_amount" type="hidden" value="1">
                                <input type="submit" value="Code hinzufügen">
                            </form>
                        </div>

                        <?php
                        $codes = $rpg->getCodesByID("quest_done", $questInfo->id);
                        if (count($codes["codes"]) > 0) {
                            foreach ($codes["codes"] as $code) {
                                ?>
                                <div class="row code">
                                    <strong>Code: </strong><?php echo $code->code; ?> | <strong>Typ: </strong><?php echo $code->code_type; ?>
                                    <div style='float:right'>
                                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                            <input name="action" type="hidden" value="removecode">
                                            <input type="hidden" name="code_type" value="quest_done">
                                            <input name="code" type="hidden" value="<?php echo $code->code; ?>">
                                            <input type="submit" value="! Code entfernen !">
                                        </form>
                                    </div>    
                                </div>
                                <?php
                            }
                        } else {
                            echo "Keine Codes";
                        }
                        ?>
                        <?php
                    } else {
                        ?><h1>Fehler</h1>
                        <div class='row'>

                            <strong>Quest nicht gefunden</strong>

                        </div>
                        <?php
                    }
                } elseif (isset($selectedItem)) {
                    $itemInfo = $rpg->getItemInfo($selectedItem);
                    if ($itemInfo != null) {
                        ?>
                        <div class="overhead">
                            <?php echo $itemInfo->item_name ?>
                        </div>
                        <h1>Details</h1>
                        <div class='row'>

                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="edititem">
                                <input name="itemid" type="hidden" value="<?php echo $itemInfo->id ?>">
                                <strong class="w300">Team (1=T / 2=A / 0=Beide): </strong>

                                <select name="item_owner">
                                    <option <?php
                                    if ($itemInfo->item_owner === "0") {
                                        echo'selected="true" ';
                                    }
                                    ?> value="0">
                                        Verschwörungstheoretiker
                                    </option>
                                    <option <?php
                                    if ($itemInfo->item_owner === "1") {
                                        echo'selected="true" ';
                                    }
                                    ?> value="1">
                                        Technokraten
                                    </option>
                                    <option <?php
                                    if ($itemInfo->item_owner === "2") {
                                        echo'selected="true" ';
                                    }
                                    ?> value="2">
                                        Avantgardisten
                                    </option>
                                    <option <?php
                                    if ($itemInfo->item_owner === "3") {
                                        echo'selected="true" ';
                                    }
                                    ?>  value="3">
                                        Alle
                                    </option>
                                </select><br>
                                <strong class="w300">Für Klassen ID:</strong>
                                <?php
                                $selectedIndex = $itemInfo->item_accuracy;
                                $selectedEcho = " selected='true'";
                                ?>
                                <select name="item_accuracy">
                                    <option <?php
                                    if ($selectedIndex === "0") {
                                        echo $selectedEcho;
                                    }
                                    ?> value="0">
                                        Alle
                                    </option>
                                    <option <?php
                                    if ($selectedIndex === "1") {
                                        echo $selectedEcho;
                                    }
                                    ?> value="1">
                                        Infanterist / Average Joe
                                    </option>
                                    <option <?php
                                    if ($selectedIndex === "2") {
                                        echo $selectedEcho;
                                    }
                                    ?> value="2">
                                        Disassembler / Brawler
                                    </option>
                                    <option <?php
                                    if ($selectedIndex === "3") {
                                        echo $selectedEcho;
                                    }
                                    ?> value="3">
                                        Mindscaper / Limit Breaker 	
                                    </option>
                                    <option <?php
                                    if ($selectedIndex === "4") {
                                        echo $selectedEcho;
                                    }
                                    ?> value="4">
                                        Ambidexter / Pistolero
                                    </option>
                                    <option <?php
                                    if ($selectedIndex === "5") {
                                        echo $selectedEcho;
                                    }
                                    ?>value="5">
                                        Enforcer / Specialist
                                    </option>
                                </select><br>  
                                <strong class="w300">Name:</strong>
                                <input name="item_name" type="text" value="<?php echo $itemInfo->item_name; ?>"><br>
                                <strong class="w300">Item Klasse:</strong>
                                <input name="item_class" type="text" value="<?php echo $itemInfo->item_class; ?>"><br>
                                <strong class="w300">Damage:</strong>
                                <input name="item_damage" type="number" value="<?php echo $itemInfo->item_damage; ?>" min="0" max="2000"><br>
                                <strong class="w300">Range:</strong>
                                <input name="item_range" type="number" value="<?php echo $itemInfo->item_range; ?>" min="0" max="2000"><br>

                                <strong class="w300">Firerate:</strong>
                                <input name="item_firerate" type="number" value="<?php echo $itemInfo->item_firerate; ?>" min="0" max="2000"><br>
                                <strong class="w300">Special:</strong>
                                <input name="item_special" type="text" value="<?php echo $itemInfo->item_special; ?>"><br>
                                <strong class="w300">Quality:</strong>
                                <input name="item_quality" type="text" value="<?php echo $itemInfo->item_quality; ?>"><br>
                                <strong class="w300">Dice Acc:</strong>
                                <input name="item_dice" type="number" value="<?php echo $itemInfo->item_dice; ?>" min="0" max="2000"><br>
                                <strong class="w300">Image: </strong>
                                <input name="item_image" type="text" value="<?php echo $itemInfo->item_image; ?>"><br>
                                <strong class="w300">Content: </strong>
                                <input name="item_content" type="text" value="<?php echo $itemInfo->item_content; ?>"><br>
                                <input type="submit" value="Item speichern">
                            </form>
                            <div style="float:right">
                                <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                    <input name="action" type="hidden" value="removeitemfromsystem">
                                    <input name="itemid" type="hidden" value="<?php echo $itemInfo->id ?>">
                                    <input type="submit" style="color:darkred" value="! Item löschen !">
                                </form>  
                            </div>

                        </div>
                        <h1>Code erstellen</h1>
                        <div class='row'>
                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="addcode">

                                <strong>Code (4 stellig ohne 0):</strong>
                                <input name="code" type="text" value="0000">
                                <input type="hidden" name="code_type" value="item">
                                <input name="code_reward_id" type="hidden" value="<?php echo $itemInfo->id ?>">
                                <strong>Anzahl:</strong>
                                <input name="code_reward_amount" type="number" min="1" value="1">
                                <input type="submit" value="Code hinzufügen">
                            </form>
                        </div>
                        <h1>Bestehende Codes</h1>
                        <?php
                        $codes = $rpg->getCodesByID("item", $itemInfo->id);
                        if (count($codes["codes"]) > 0) {
                            foreach ($codes["codes"] as $code) {
                                ?>
                                <div class='row'>
                                    <strong>Code: </strong><?php echo $code->code; ?> <strong>| Ergibt: </strong><?php echo $code->code_reward_amount; ?> Items
                                    <div style='float:right'>
                                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                            <input name="action" type="hidden" value="removecode">
                                            <input type="hidden" name="code_type" value="item">
                                            <input name="code" type="hidden" value="<?php echo $code->code; ?>">
                                            <input type="submit" value="! Code entfernen !">
                                        </form>
                                    </div>    
                                </div>
                                <?php
                            }
                        } else {
                            echo "Keine Codes";
                        }
                        ?>
                    <?php } else {
                        ?>
                        <h1>Fehler</h1>
                        <div class='row'>

                            <strong>Item nicht gefunden</strong>

                        </div>
                        <?php
                    }
                } elseif (isset($selectedField)) {
                    ?>
                    <div style="float: right;">
                        <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                            <input name="action" type="hidden" value="removefield">
                            <input name="fieldid" type="hidden" value="<?php echo $selectedField; ?> "> 
                            <input type="submit" value="! Spielfeld entfernen !" style="color: darkred">
                        </form>
                    </div>
                    <?php
                    echo '<h1>Link: <a target="_blank" href="field.php?id=' . $selectedField . '">Spielfeld</a></h1>';
                    echo "<h1>Spieler:</h1>";
                    $playerList = $rpg->listPlayers($selectedField);
                    if (count($playerList["players"]) > 0) {
                        foreach ($playerList["players"] as $player) {
                            $playerInfo = $rpg->getUserInfo($player->user_id);
                            $playerInv = $rpg->listInventar($player->user_id)["userInventar"];
                            $medic = null;

                            foreach ($playerInv as $invItem) {

                                if ($invItem->id === "93") {
                                    $medic = $invItem;
                                }
                            }
                            ?>
                            <div class="row userTeam<?php echo($playerInfo->team); ?>">

                                <form  class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                    <input name="action" type="hidden" value="edituserdetails">
                                    <input name="userTeam" type="hidden" value="<?php echo $playerInfo->team; ?>">
                                    <input name="userClass" type="hidden" value="<?php echo $playerInfo->class_id; ?>">
                                    <input name="userId" type="hidden" value="<?php echo $playerInfo->id; ?>">
                                    <strong>ID</strong>: <?php echo $playerInfo->id; ?> 
                                    <strong> Name:</strong>
                                    <input name="userName" type='text' value="<?php echo $playerInfo->name; ?>">
                                    <strong>HP</strong>: 
                                    <input name="userHP" type='number' min="0" value="<?php echo $playerInfo->hp; ?>">
                                    <strong>Stamina</strong>:
                                    <input name="userStamina" type='number' min="0" value="<?php echo $playerInfo->stamina; ?>">
                                    <strong>Reaction</strong>:
                                    <input name="userReaction" type='number' min="0" value="<?php echo $playerInfo->reaction; ?>">
                                    <strong>XP</strong>:
                                    <input name="userXP" type='number' min="0" value="<?php echo $playerInfo->xp; ?>">
                                    | <input type="submit" value="Speichern">
                                </form>
                                <form  class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                    <input name="action" type="hidden" value="resetuserstats">
                                    <input name="userid" type="hidden" value="<?php echo $playerInfo->id; ?>">
                                    | <input type="submit" value="Reset Stats"> |
                                </form>
                                <a href="?page=users&user=<?php echo $playerInfo->id; ?>" target="_blank"> User bearbeiten</a>

                                <div style="float:right">
                                    <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                        <input name="action" type="hidden" value="removeplayer">
                                        <input name="userid" type="hidden" value="<?php echo $player->user_id; ?>">
                                        <input name="fieldid" type="hidden" value="<?php echo $selectedField; ?>">
                                        <input type="submit" style="color:darkred" value="! User entfernen !">
                                    </form>  
                                </div>
                            </div>

                            <?php
                            if (strpos($player->weapon_id, ',') !== false) {

                                $weapons = Array();
                                $weapons = explode(",", $player->weapon_id);

                                foreach ($weapons as $weaponid) {
                                    $weapon = $rpg->getItemInfo($weaponid);

                                    if (strpos($weapon->item_name, ',') !== false) {
                                        $names = Array();
                                        $names = explode(",", $weapon->item_name);
                                        $weapon->item_name = $names[(int) $playerInfo->team];
                                    }
                                    echo '<div class="weaponOfUser userTeam' . $playerInfo->team . '">';
                                    echo "<strong>Gewählte Waffe:</strong> {$weapon->item_name} | <strong>Damage:</strong> {$weapon->item_damage} | <strong>Range:</strong> {$weapon->item_range} | <strong>Firerate:</strong> {$weapon->item_firerate} | <strong>Special:</strong> {$weapon->item_special} | <strong>Dice:</strong> {$weapon->item_dice}";
                                    echo "</div>";
                                }
                            } else {
                                $weapon = $rpg->getItemInfo($player->weapon_id);
                                if (strpos($weapon->item_name, ',') !== false) {
                                    $names = Array();
                                    $names = explode(",", $weapon->item_name);

                                    $weapon->item_name = $names[(int) $playerInfo->team];
                                }
                                echo '<div class="weaponOfUser userTeam' . $playerInfo->team . '">';
                                echo "<strong>Gewählte Waffe:</strong> {$weapon->item_name} | <strong>Damage:</strong> {$weapon->item_damage} | <strong>Range:</strong> {$weapon->item_range} | <strong>Firerate:</strong> {$weapon->item_firerate} | <strong>Special:</strong> {$weapon->item_special} | <strong>Dice:</strong> {$weapon->item_dice}";
                                echo "</div>";
                            }
                            if ($medic !== null) {
                                echo '<div class="weaponOfUser userTeam' . $playerInfo->team . '">';
                                echo "<strong>Medinject:</strong> {$medic->item_name} | Anzahl: {$medic->item_amount}";
                                echo "</div>";
                            }
                            ?>

                            <?php
                        }
                    } else {
                        echo "<p>Keine Spieler</p>";
                    }
                } elseif (isset($selectedCode)) {
                    if ($rpg->codeExists($selectedCode)) {
                        ?>

                        <div style="float: right;">
                            <form class="inlineForm" method="POST" action="?<?php echo $_SERVER['QUERY_STRING'] ?>">
                                <input name="action" type="hidden" value="removecode">
                                <input name="code" type="hidden" value="<?php echo $selectedCode; ?> "> 
                                <input type="submit" value="! Code entfernen !" style="color: darkred">
                            </form>
                        </div>
                        <h1>Code genutzt:</h1>
                        <?php
                        $usersuCode = $rpg->listPlayersUsedCodes($selectedCode);
                        if (count($usersuCode["userusedcode"]) > 0) {
                            foreach ($usersuCode["userusedcode"] as $userUcode) {
                                $playerInfo = $rpg->getUserInfo($userUcode->user_id);
                                ?>
                                <div class="row">
                                    <strong>Name: </strong><span><?php echo $playerInfo->name ?>
                                    </span>| <a href="?users&user=<?php echo $playerInfo->id; ?>" target="_blank">User bearbeiten</a>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p>Code nicht genutzt</p>";
                        }
                    } else {
                        ?><h1>Fehler</h1>
                        <div class='row'>

                            <strong>Code nicht gefunden</strong>

                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

    </body>

</html>


<?php
      exit;
   } else {
      echo "Bad Cookie.";
      exit;
   }
}

if (isset($_GET['p']) && $_GET['p'] == "login") {
   if ($_POST['user'] != $username) {
      echo "Sorry, that username does not match.";
      exit;
   } else if ($_POST['keypass'] != $password) {
      echo "Sorry, that password does not match.";
      exit;
   } else if ($_POST['user'] == $username && $_POST['keypass'] == $password) {
      setcookie('PrivatePageLogin', md5($_POST['keypass'].$nonsense));
      header("Location: $_SERVER[PHP_SELF]");
   } else {
      echo "Sorry, you could not be logged in at this time.";
   }
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?p=login" method="post">
<label><input type="text" name="user" id="user" /> Name</label><br />
<label><input type="password" name="keypass" id="keypass" /> Password</label><br />
<input type="submit" id="submit" value="Login" />
</form>