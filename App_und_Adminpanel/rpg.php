<?php

include_once 'db/db.class.php';
include 'db/dbinfo.php';

class RPG {

    public $debug = false;
    public $mysql = null;
    public $db;
    public $connected = false;

    function RPG() {
        $db = new DBInfo();

        $this->mysql = new DB($db->base, $db->db_server, $db->db_user, $db->db_pass);

        if (isset($_POST["debug"]) && $_POST["debug"] === "true") {
            $this->debug = true;
        }

        if ($this->mysql != null) {
            $this->connected = true;
        } else {
            $this->connected = false;
            echo "===================== NOT CONNECTED TO DATABASE =====================";
        }
    }

    public function updateMessage($teamID, $message) {
        $user = $this->mysql->query("UPDATE rpg_message SET message='{$message}' WHERE team_id='{$teamID}'", $this->debug);
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Nachrichten gespeichert";
        return array("status" => $statusArray);
    }

    public function getMessage($team_ID) {
        $message = $this->mysql->query("SELECT message FROM rpg_message WHERE team_id=" . $team_ID);
        $message = $this->mysql->fetchNextObject($message);
        return array("status" => array("status" => "ok", "message" => $message->message));
    }

    /**
     * lists all Userinformation
     */
    function listUser() {
        $this->mysql->query("SELECT * FROM rpg_character, rpg_classes WHERE rpg_character.class = rpg_classes.class_id ORDER BY rpg_character.id", $this->debug);
    }

    /**
     * Erstellt User
     * @param type $name username
     * @param type $pass1 password1
     * @param type $pass2 password2
     * @return type Array ("status","userInfo")
     */
    public function createUser($name, $pass1, $pass2) {
        $statusArray = array("status" => null, "message" => null);
        $returnInfo = null;
        if (!isset($name)) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Nutzername nicht gesetzt";
        } else if (strlen($name) < 4) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Name zu kurz.";
        } elseif (strcmp($pass1, $pass2) !== 0) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Passwoerter sind nicht gleich.";
        } elseif (strlen($pass1) < 4 || strlen($pass2) < 4) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Passwort zu kurz";
        } else {
            //alles ok user kann angelegt werden      
            $user = $this->mysql->query("SELECT * FROM rpg_character WHERE name='{$name}'", $this->debug);
            if ($this->mysql->numRows($user) != 0) {
                //user schon vorhanden
                $statusArray["status"] = "error";
                $statusArray["message"] = "User existiert bereits";
            } else {
                //user wird angelegt und user id gespeichert
                $this->mysql->query("INSERT INTO rpg_character (name, password) VALUES ('{$name}','" . md5($pass1) . "')");
                $statusArray["status"] = "ok";
                $statusArray["message"] = "User angelegt";

                $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='{$this->mysql->lastInsertedId()}'", $this->debug);
                $user = $this->mysql->fetchNextObject();
                $returnInfo = $this->getUserInfo($user->id);
            }
        }
        return array("status" => $statusArray, "userInfo" => $returnInfo);
    }

    public function deleteUser($userID) {
        $this->mysql->execute("DELETE FROM rpg_character WHERE id=" . $userID, $this->debug);
        $this->mysql->execute("DELETE FROM rpg_questlog WHERE user_id=" . $userID, $this->debug);
        $this->mysql->execute("DELETE FROM rpg_inventar WHERE user_id=" . $userID, $this->debug);
        $this->mysql->execute("DELETE FROM rpg_spielfeld_spieler WHERE user_id=" . $userID, $this->debug);
        $statusArray["status"] = "ok";
        $statusArray["message"] = "User komplett entfernt";
        return array("status" => $statusArray);
    }

    /**
     * Loggt User ein
     * @param type $name username
     * @param type $pass password
     * @return type Array ("status","userInfo")
     */
    public function loginUser($name, $pass) {
        $statusArray = array("status" => null, "message" => null);
        $returnInfo = null;

        if (!isset($name)) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Nutzername nicht gesetzt";
        } elseif (strlen($pass) < 3) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Passwort zu kurz";
        } else {
            $user = $this->mysql->query("SELECT * FROM rpg_character WHERE name='" . $name . "'", $this->debug);
            if ($this->mysql->numRows($user) != 0) {
                $user = $this->mysql->fetchNextObject();
                if ($user->password === md5($pass)) {
                    //user einloggen
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "User eingeloggt";
                    //$returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
                    $returnInfo = $this->getUserInfo($user->id);
                } else {
                    //passwort falsch
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Passwort falsch";
                }
            } else {
                //User nicht gefunden
                $statusArray["status"] = "error";
                $statusArray["message"] = "User nicht vorhanden";
            }
        }
        return array("status" => $statusArray, "userInfo" => $returnInfo);
    }

    /**
     * Zeigt alle verfügbaren Teams an
     * @return type
     */
    public function listTeams() {
        $statusArray = array("status" => null, "message" => null);

        $teamArray = array();

        $teams = $this->mysql->query("SELECT * FROM rpg_teams", $this->debug);

        if ($this->mysql->numRows($teams) != 0) {
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste der Teams";

            while ($line = $this->mysql->fetchNextObject($teams)) {
                $teamArray[] = array("id" => $line->id, "team_name" => $line->team_name);
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Teams gefunden";
        }
        return array("status" => $statusArray, "teams" => $teamArray);
    }

    public function getUserList() {
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Userliste";
        $userArray = array();
        $userList = $this->mysql->query("SELECT * FROM rpg_character, rpg_classes WHERE rpg_character.class = rpg_classes.class_id ORDER BY rpg_character.id", $this->debug);
        while ($line = $this->mysql->fetchNextObject($userList)) {
            $userArray[] = $line;
        }
        return array("status" => $statusArray, "userlist" => $userArray);
    }

    public function getUserDetails($id) {
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Userdetails";


        if ($this->userExists($id)) {
            $user = $this->mysql->query("SELECT * FROM rpg_character, rpg_classes WHERE rpg_character.class = rpg_classes.class_id AND rpg_character.id =" . $id, $this->debug);
            $user = $this->mysql->fetchNextObject($user);

            $inventar = $this->listInventar($id)["userInventar"];
            $quests = $this->listQuestlog($id, "2")["quests"];
            $message = $this->getMessage($user->team);
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User existiert nicht";
        }

        return array("status" => $statusArray, "userDetails" => $user, "userInventar" => $inventar, "quests" => $quests, "message" => $message);
    }

    public function editUserDetails($userId, $name, $team, $class, $hp, $stamina, $reaction, $xp) {

        if ($this->userExists($userId)) {

            $this->mysql->query("UPDATE rpg_character SET name='" . $name . "',class='" . $class . "',team='" . $team . "', hp='" . $hp . "', stamina='" . $stamina . "', reaction='" . $reaction . "', xp='" . $xp . "' WHERE id='" . $userId . "'");
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Userdetails geändert";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray);
    }

    /**
     * Zeigt alle verfügbaren Klassen an
     * @param type $teamID zeigt nur klassen eines Teams, sonst alle Klassen
     * @return type
     */
    public function listClasses($teamID) {
        $statusArray = array("status" => null, "message" => null);

        $classArray = array();

        if ($teamID === "1" || $teamID === "2") {
            $classes = $this->mysql->query("SELECT * FROM rpg_classes WHERE class_team='" . $teamID . "'", $this->debug);
        } else {
            $classes = $this->mysql->query("SELECT * FROM rpg_classes", $this->debug);
        }


        if ($this->mysql->numRows($classes) != 0) {
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste der Klassen";

            while ($line = $this->mysql->fetchNextObject($classes)) {
                $classArray[] = $line;
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Klassen gefunden";
        }
        return array("status" => $statusArray, "classes" => $classArray);
    }

    /**
     * teilt dem User ein Team zu
     * @param type $id id
     * @param type $team (1 oder 2)
     * @return type Array ("status","userInfo")
     */
    public function selectTeam($id, $team) {
        $statusArray = array("status" => null, "message" => null);
        $returnInfo = null;
        $team = (int) $team;

        if ($team === 1 || $team === 2) {
            $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $id . "'", $this->debug);
            if ($this->mysql->numRows($user) != 0) {
                $user = $this->mysql->fetchNextObject();
                if ($user->team === "0") {
                    $this->mysql->query("UPDATE rpg_character SET team='" . $team . "' WHERE id='" . $id . "'", $this->debug);
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Dem Team wurde beigetreten";
                    $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $id . "'");
                    $user = $this->mysql->fetchNextObject();
                    $returnInfo = $this->getUserInfo($user->id);
                } else {
                    //user bereits in einem team
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Bereits in einem Team";
                    $returnInfo = $this->getUserInfo($user->id);
                }
            } else {
                //user nicht gefunden
                $statusArray["status"] = "error";
                $statusArray["message"] = "User nicht gefunden";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Ungültiges Team";
        }
        return array("status" => $statusArray, "userInfo" => $returnInfo);
    }

    public function resetUserStats($id) {
        $statusArray = array("status" => null, "message" => null);

        $user = $this->getUserInfo($id);

        $class = $this->mysql->query("SELECT * FROM rpg_classes WHERE class_id='" . $user->class_id . "'", $this->debug);
        $class = $this->mysql->fetchNextObject();

        $this->mysql->query("UPDATE rpg_character SET class='" . $user->class_id . "',hp='" . $class->class_hp . "', stamina='" . $class->class_stamina . "',reaction='" . $class->class_reaction . "' WHERE id='" . $id . "'", $this->debug);
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Charakter auf standard zurückgesetzt: " . $class->class_name;
        return array("status" => $statusArray);
    }

    /**
     * Wählt die Klasse aus
     * @param type $id user ID
     * @param type $classID ID der zu wählenden Klasse (Nur die aus dem Team des Users)
     * @return type Array ("status","userInfo")
     */
    public function selectClass($id, $classID) {
        $statusArray = array("status" => null, "message" => null);
        $returnInfo = null;
        $classID = (int) $classID;


        if ($this->userExists($id)) {
            $user = $this->getUserInfo($id);

            $class = $this->mysql->query("SELECT * FROM rpg_classes WHERE class_id='" . $classID . "'", $this->debug);
            $class = $this->mysql->fetchNextObject();

            if ($user->class === "0") {

                //Gibt es die Klasse?
                if ($class) {
                    $this->mysql->query("UPDATE rpg_character SET class='" . $classID . "',hp='" . $class->class_hp . "', stamina='" . $class->class_stamina . "',reaction='" . $class->class_reaction . "' WHERE id='" . $id . "'", $this->debug);
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Charakter hat seine Klasse gewählt";
                    $this->autoLoadCode("class_id", $classID, $user->id);
                    $returnInfo = $this->getUserInfo($user->id);
                } else {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Klasse nicht gefunden";
                }
            } else {
                //user hat bereits eine klasse
                $statusArray["status"] = "error";
                $statusArray["message"] = "Bereits Klasse gewählt";
                $returnInfo = $this->getUserInfo($user->id);
            }
        } else {
            //user nicht gefunden
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray, "userInfo" => $returnInfo);
    }

    //########################### QUEST ###############################//
    public function listQuestlog($userId, $done) {
        $statusArray = array("status" => null, "message" => null);
        if ($done === "1") {
            $quests = $this->mysql->query("SELECT * FROM rpg_questlog, rpg_quests WHERE rpg_questlog.user_id='" . $userId . "' AND rpg_questlog.quest_done='1' AND rpg_questlog.quest_id = rpg_quests.id ORDER BY `rpg_questlog`.`quest_done` ASC", $this->debug);
        } elseif ($done === "0") {
            $quests = $this->mysql->query("SELECT * FROM rpg_questlog, rpg_quests WHERE rpg_questlog.user_id='" . $userId . "' AND rpg_questlog.quest_done='0' AND rpg_questlog.quest_id = rpg_quests.id ORDER BY `rpg_questlog`.`quest_done` ASC", $this->debug);
        } elseif ($done === "2") {
            $quests = $this->mysql->query("SELECT * FROM rpg_questlog, rpg_quests WHERE rpg_questlog.user_id='" . $userId . "' AND rpg_questlog.quest_id = rpg_quests.id ORDER BY `rpg_questlog`.`quest_done` ASC", $this->debug);
        }



        if ($this->mysql->numRows($quests) != 0) {
            while ($line = $this->mysql->fetchNextObject($quests)) {
                $questArray[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Questlog von UserId: " . $userId;
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Quests gefunden";
        }

        return array("status" => $statusArray, "quests" => $questArray);
    }

    public function listQuests($ownerId) {
        $statusArray = array("status" => null, "message" => null);
        if ($ownerId === "1") {
            $quests = $this->mysql->query("SELECT * FROM rpg_quests WHERE quest_owner=1", $this->debug);
            $statusArray["message"] = "Liste der Quests der Technokraten";
        } elseif ($ownerId === "2") {
            $quests = $this->mysql->query("SELECT * FROM rpg_quests WHERE quest_owner=2", $this->debug);
            $statusArray["message"] = "Liste der Quests der Avantgardisten";
        } elseif ($ownerId === "0") {
            $quests = $this->mysql->query("SELECT * FROM rpg_quests WHERE quest_owner=0", $this->debug);
            $statusArray["message"] = "Liste der Quests beider Teams";
        } else {
            $quests = $this->mysql->query("SELECT * FROM rpg_quests", $this->debug);
            $statusArray["message"] = "Liste aller quests";
        }

        if ($this->mysql->numRows($quests) != 0) {
            while ($line = $this->mysql->fetchNextObject($quests)) {
                $questArray[] = $line;
            }
            $statusArray["status"] = "ok";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Quests gefunden";
        }
        return array("status" => $statusArray, "quests" => $questArray);
    }

    public function addQuest($teamId, $name, $description) {
        $statusArray = array("status" => null, "message" => null);
        if (isset($teamId) && strlen($name) > 3 && strlen($description) > 3) {
            $this->mysql->execute("INSERT INTO `rpg_quests` (`id`, `quest_owner`, `quest_name`, `quest_description`, `quest_reward_item_id`, `quest_reward_amount`) VALUES (NULL, '" . $teamId . "', '" . $name . "', '" . $description . "', '1', '100');", $this->debug);
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Quest hinzugefügt";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Fehler bei der Eingabe";
        }
        return array("status" => $statusArray);
    }

    public function editQuest($id, $owner, $name, $description) {
        $statusArray = array("status" => null, "message" => null);

        $this->mysql->query("UPDATE `rpg_quests` SET `quest_owner`='{$owner}',`quest_name`='{$name}',`quest_description`='{$description}' WHERE `id`='{$id}'", $this->debug);

        $statusArray["status"] = "ok";
        $statusArray["message"] = "Quest geändert";


        return array("status" => $statusArray);
    }

    function removeQuestFromQuestLog($questID, $userID) {
        $statusArray = array("status" => null, "message" => null);

        $this->mysql->execute("DELETE FROM rpg_questlog WHERE quest_id='" . $questID . "' AND user_id='" . $userID . "'", $this->debug);

        $statusArray["status"] = "ok";
        $statusArray["message"] = "Quest ({$questID}) aus Questlog ({$userID}) entfernt";


        return array("status" => $statusArray);
    }

    public function setQuestActive($userId, $questId, $admin = false) {
        $statusArray = array("status" => null, "message" => null);


        if ($this->userExists($userId)) {

            $user = $this->getUserInfo($userId);
            $quest = $this->getQuestInfo($questId);

            if ($user === NULL || $quest === null) {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Anfrage nicht vollständig";
                return array("status" => $statusArray);
            }

            if ($user->team === $quest->quest_owner || $quest->quest_owner === "3") {
                if (!$this->userHasQuest($userId, $questId)) {
                    $this->mysql->query("INSERT INTO rpg_questlog (user_id, quest_id, quest_done) VALUES ('" . $userId . "','" . $questId . "','0')");
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Quest wurde hinzugefügt";
                } else {
                    if ($admin) {
                        $this->mysql->query("UPDATE rpg_character SET xp=xp-1 WHERE id='" . $userId . "'", $this->debug);
                        $this->mysql->query("UPDATE rpg_questlog SET quest_done='0' WHERE user_id='" . $userId . "' AND quest_id='" . $questId . "'", $this->debug);
                        $statusArray["status"] = "error";
                        $statusArray["message"] = "Quest wurde aktiv gesetzt";
                    } else {
                        $statusArray["status"] = "error";
                        $statusArray["message"] = "User hat Quest schon angenommen";
                    }
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Nicht im richtigen Team";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray);
    }

    public function setQuestDone($userId, $questId) {
        $statusArray = array("status" => null, "message" => null);
        $questInfo = $this->getQuestInfo($questId);
        if ($this->userExists($userId)) {
            if ($this->userHasQuest($userId, $questId)) {
                $questDone = $this->userHasQuestDone($userId, $questId);

                if (!$questDone) {
                    $this->mysql->query("UPDATE rpg_questlog SET quest_done='1' WHERE user_id='" . $userId . "' AND quest_id='" . $questId . "'", $this->debug);
                    $this->mysql->query("UPDATE rpg_character SET xp=xp+1 WHERE id='" . $userId . "'", $this->debug);
                    $this->autoLoadCode("quest_id", $questId, $userId);
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Quest abgeschlossen: " . $questInfo->quest_name;
                } else {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Quest bereits abgeschlossen";
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "User hat Quest noch nicht angenommen";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray);
    }

    //########################### INVENTAR ###############################//
    public function listInventar($userId) {
        $statusArray = array("status" => null, "message" => null);
        $user = $this->getUserInfo($userId);
        if ($this->userExists($userId)) {
            $inventar = $this->mysql->query("SELECT * FROM rpg_inventar, rpg_items WHERE rpg_inventar.item_id = rpg_items.id and rpg_inventar.user_id = '" . $userId . "'");
            if ($this->mysql->numRows($inventar) > 0) {
                while ($line = $this->mysql->fetchNextObject($inventar)) {

                    if (strpos($line->item_name, ',') !== false) {

                        $names = Array();
                        $names = explode(",", $line->item_name);
                        $line->item_name = $names[(int) $user->team];
                    }
                    $inventarArray[] = $line;
                }
                $statusArray["status"] = "ok";
                $statusArray["message"] = "Inventar von UserId: " . $userId;
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "leer";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray, "userInventar" => $inventarArray);
    }

    public function removeFromInventar($userid, $itemid) {
        $statusArray = array("status" => null, "message" => null);
        if ($this->userExists($userid)) {
            if ($this->itemExists($itemid)) {
                if ($this->userHasItem($userid, $itemid, 1) === "ok") {
                    $this->mysql->execute("DELETE FROM rpg_inventar WHERE user_id = '" . $userid . "' AND item_id = '" . $itemid . "'", $this->debug);
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Item entfernt";
                } else {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "User hat das Item nicht (mehr)";
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Item nicht gefunden";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray);
    }

    public function transferToInventar($fromUserId, $itemId, $toUserId, $amount) {
        $statusArray = array("status" => null, "message" => null);
        //existieren user?
        if ($this->userExists($toUserId) && $this->userExists($fromUserId)) {
            //existiert item?
            if ($this->itemExists($itemId)) {
                //besitzt user das item?
                $uhItem = $this->userHasItem($fromUserId, $itemId, $amount);
                if ($uhItem === "ok") {
                    if ($this->transferItem($fromUserId, $toUserId, $itemId, $amount)) {
                        $statusArray["status"] = "ok";
                        $statusArray["message"] = "Item wurde versendet";
                    } else {
                        $statusArray["status"] = "error";
                        $statusArray["message"] = "Transferfehler";
                    }
                } else if ($uhItem === "itemerror") {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "User hat das Item nicht";
                } else if ($uhItem === "itemamounterror") {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "User hat zu wenig Items zum transferieren";
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Item nicht gefunden";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }


        $inv = $this->listInventar($fromUserId);
        return array("status" => $statusArray, "userInventar" => $inv["userInventar"]);
    }

    public function listItems() {
        $statusArray["status"] = "";
        $statusArray["message"] = "";
        $itemsList = $this->mysql->query("SELECT * FROM rpg_items WHERE 1");
        if ($this->mysql->numRows($itemsList) > 0) {
            while ($line = $this->mysql->fetchNextObject($itemsList)) {
                $items[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste aller Items";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Items gefunden.";
        }
        return array("status" => $statusArray, "itemslist" => $items);
    }

    public function listWeapons($userId) {
        $statusArray = array("status" => null, "message" => null);
        if ($this->userExists($userId)) {
            $inventar = $this->mysql->query("SELECT * FROM rpg_inventar, rpg_items WHERE rpg_inventar.item_id = rpg_items.id and rpg_inventar.user_id = '" . $userId . "' AND item_class='weapon'");
            if ($this->mysql->numRows($inventar) > 0) {
                while ($line = $this->mysql->fetchNextObject($inventar)) {
                    $inventarArray[] = $line;
                }
                $statusArray["status"] = "ok";
                $statusArray["message"] = "Waffen von UserId: " . $userId;
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "leer";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User nicht gefunden";
        }
        return array("status" => $statusArray, "weaponlist" => $inventarArray);
    }

    public function editItem($id, $name, $owner, $class, $damage, $range, $accuracy, $firerate, $special, $quality, $dice, $image, $content) {
        if ($this->itemExists($id)) {
            $this->mysql->query("UPDATE rpg_items SET item_name='" . $name . "',item_owner='" . $owner . "', item_class='" . $class . "', item_damage='" . $damage . "', item_range='" . $range . "', item_accuracy='" . $accuracy . "', item_firerate='" . $firerate . "', item_special='" . $special . "', item_quality='" . $quality . "', item_dice='" . $dice . "', item_image='" . $image . "', item_content='" . $content . "' WHERE id='" . $id . "'");
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Item geändert";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Item nicht gefunden";
        }
        return array("status" => $statusArray);
    }

    public function addItemToSystem($name, $owner, $class, $damage, $range, $accuracy, $firerate, $special, $quality, $dice, $image, $content) {
        $this->mysql->query("INSERT INTO `rpg_items` (`id`, `item_name`, `item_owner`, `item_class`, `item_damage`, `item_range`, `item_accuracy`, `item_firerate`, `item_special`, `item_quality`, `item_dice`, `item_image`, `item_content`) VALUES (null,'" . $name . "'," . $owner . ",'" . $class . "'," . $damage . "," . $range . "," . $accuracy . "," . $firerate . ",'" . $special . "','" . $quality . "','" . $dice . "','" . $image . "','" . $content . "')");
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Item hinzugefügt: " . $this->mysql->lastInsertedId();
        return array("status" => $statusArray);
    }

    public function removeItemFromSystem($itemid) {
        if ($this->itemExists($itemid)) {
            $this->mysql->query("DELETE FROM rpg_items WHERE id=" . $itemid);
            $this->mysql->query("DELETE FROM rpg_inventar WHERE item_id=" . $itemid);
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Item gelöscht";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Item nicht gefunden";
        }
        return array("status" => $statusArray);
    }

    public function giveItemTo($itemid, $userid, $amount, $teamOverLap = false) {

        $user = $this->getUserInfo($userid);
        $item = $this->getItemInfo($itemid);

        if ($user == NULL || $item == null) {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Anfrage nicht vollständig";
            return array("status" => $statusArray);
        }


        if ($this->itemExists($itemid)) {
            if ($this->userExists($userid)) {

                if ($user->team === $item->item_owner || $item->item_owner === "3" || $teamOverLap === true) {
                    if ($user->class_id === $item->item_accuracy || $item->item_accuracy === "0") {


                        if ($this->userHasItem($userid, $itemid, 1) === "ok") {
                            $this->mysql->query("UPDATE rpg_inventar SET item_amount = item_amount + " . $amount . " WHERE user_id = '" . $userid . "' AND item_id='" . $itemid . "'");
                        } else {
                            $this->mysql->query("INSERT INTO rpg_inventar VALUES (" . $userid . "," . $itemid . "," . $amount . ")");
                        }
                        $statusArray["status"] = "ok";
                        $statusArray["message"] = "Item versendet";
                    } else {
                        $statusArray["status"] = "error";
                        $statusArray["message"] = "Item nicht für Klasse bestimmt";
                    }
                } else {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Du bist nicht im richtigen Team";
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "User nicht gefunden";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Item nicht gefunden";
        }

        return array("status" => $statusArray);
    }

    // SPIELFELDER //

    public function addField($name) {
        $this->mysql->execute("INSERT INTO rpg_spielfeld VALUES (null,'{$name}')");
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Spielfeld: {$name} erstellt";
        return array("status" => $statusArray);
    }

    public function removeField($id) {
        $this->mysql->execute("DELETE FROM rpg_spielfeld WHERE id=" . $id);
        $this->mysql->execute("DELETE FROM rpg_spielfeld_spieler WHERE spielfeld_id=" . $id);
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Spielfeld: {$id} entfernt";
        return array("status" => $statusArray);
    }

    public function addPlayer($fieldid, $userid, $weaponid) {
        if ($fieldid > 0 && $userid > 0 && $weaponid > 0) {


            if (count($weaponid) > 1) {
                $ids = implode(",", $weaponid);
            } else {
                $ids = $weaponid[0];
            }
            $this->mysql->execute("INSERT INTO rpg_spielfeld_spieler VALUES ('{$userid}','{$fieldid}','{$ids}')");
            $statusArray["status"] = "ok";
            $statusArray["message"] = "User dem Spielfeld hinzugefügt";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Fehlende Angabe";
        }

        return array("status" => $statusArray);
    }

    public function removePlayer($fieldid, $userid) {
        $this->mysql->execute("DELETE FROM rpg_spielfeld_spieler WHERE user_id='{$userid}' AND spielfeld_id='{$fieldid})'");
        $statusArray["status"] = "ok";
        $statusArray["message"] = "User vom Spielfeld entfernt";
        return array("status" => $statusArray);
    }

    public function listFields() {
        $fieldList = $this->mysql->query("SELECT * FROM rpg_spielfeld WHERE 1");
        if ($this->mysql->numRows($fieldList) > 0) {
            while ($line = $this->mysql->fetchNextObject($fieldList)) {
                $fields[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste der Spielfelder";
        }
        return array("status" => $statusArray, "fields" => $fields);
    }

    public function listPlayers($fieldid) {
        $playerList = $this->mysql->query("SELECT * FROM rpg_spielfeld_spieler, rpg_character, rpg_spielfeld WHERE spielfeld_id='{$fieldid}' AND rpg_spielfeld_spieler.user_id = rpg_character.id AND rpg_spielfeld.id='{$fieldid}'");
        if ($this->mysql->numRows($playerList) > 0) {
            while ($line = $this->mysql->fetchNextObject($playerList)) {
                $players[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste der Spieler";
        }
        return array("status" => $statusArray, "players" => $players);
    }

    public function autoLoadCode($type, $id, $userID) {
        $codes = $this->mysql->query("SELECT * FROM rpg_auto_codes WHERE " . $type . "='" . $id . "'", $this->debug);
        if ($this->mysql->numRows($codes) > 0) {
            while ($line = $this->mysql->fetchNextObject($codes)) {
                $code = $line->execute_code;
                $this->loadCode($code, $userID);
            }
        }
    }

    public function loadCode($code, $userID) {
        if ($this->userExists($userID)) {
            if ($this->codeExists($code)) {
                if ($this->userUsedCode($code, $userID)) {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Code wurde schon genutzt";
                } else {
                    $codeResponse = $this->processCode($code, $userID);

                    if ($codeResponse["status"] === "ok") {
                        $statusArray["status"] = "ok";
                        $statusArray["message"] = $codeResponse["message"];
                    } else {
                        $statusArray["status"] = "error";
                        $statusArray["message"] = $codeResponse["message"];
                    }
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Code nicht gefunden";
            }
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "User existiert nicht";
        }
        return array("status" => $statusArray);
    }

    public function processCode($codeNumber, $userID) {
        $statusArray["status"] = "error";
        $statusArray["message"] = "funktion";

        $code = $this->mysql->query("SELECT * FROM rpg_codes WHERE code='" . $codeNumber . "'", $this->debug);
        $code = $this->mysql->fetchNextObject($code);

        if ($code->code_type === "item") {

            $item_id = $code->code_reward_id;
            if ($this->itemExists($item_id)) {
                $item = $this->getItemInfo($item_id);
                $item_amount = $code->code_reward_amount;
                $itemResponse = $this->giveItemTo($item_id, $userID, $item_amount, false);
                if ($itemResponse["status"]["status"] === "ok") {
                    $this->mysql->execute("INSERT INTO rpg_codes_used (code, user_id) VALUES ('" . $codeNumber . "','" . $userID . "')");
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Item hinzugefügt: " . $item->item_name . " (" . $item_amount . ")";
                }else{
                    $statusArray["status"] = $itemResponse["status"]["status"];
                    $statusArray["message"] = $itemResponse["status"]["message"];
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Item existiert nicht";
            }
        } else if ($code->code_type === "quest") {
            $quest_id = $code->code_reward_id;
            $questInfo = $this->getQuestInfo($quest_id);
            if ($questInfo !== null) {
                if ($this->userHasQuest($userID, $quest_id)) {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Quest bereits angenommen";
                } else {
                    $questResponse = $this->setQuestActive($userID, $quest_id, false);
                    if ($questResponse["status"]["status"] === "ok") {
                        $this->mysql->execute("INSERT INTO rpg_codes_used (code, user_id) VALUES ('" . $codeNumber . "','" . $userID . "')");
                        $statusArray["status"] = "ok";
                        $statusArray["message"] = "Quest hinzugefügt: " . $questInfo->quest_name;
                    } else {
                        $statusArray["status"] = "error";
                        $statusArray["message"] = $questResponse["status"]["message"];
                    }
                }
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Quest existiert nicht";
            }
        } else if ($code->code_type === "quest_done") {
            $quest_id = $code->code_reward_id;
            $questInfo = $this->getQuestInfo($quest_id);
            if ($questInfo !== null) {
                if ($this->userHasQuest($userID, $quest_id)) {

                    $questResponse = $this->setQuestDone($userID, $quest_id);
                    $statusArray["status"] = $questResponse["status"]["status"];
                    $statusArray["message"] = $questResponse["status"]["message"];
                } else {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Du hast diese Quest noch nicht angenommen";
                }
            }
        } else if ($code->code_type === "join") {
            $team = 0;
            if ($code->code === "1111") {
                $team = 1;
                $statusArray["message"] = "Technokraten beigetreten!";
            } elseif ($code->code === "2222") {
                $team = 2;
                $statusArray["message"] = "Avangardisten beigetreten!";
            } else {
                $statusArray["status"] = "error";
                $statusArray["message"] = "Team nicht erkannt";
                return $statusArray;
            }
            $response = $this->selectTeam($userID, $team);

            $statusArray["status"] = $response["status"]["status"];
            $statusArray["message"] = $response["status"]["message"];
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Code type nicht erkannt";
        }
        return $statusArray;
    }

    public function listPlayersUsedCodes($code) {
        $pList = $this->mysql->query("SELECT * FROM rpg_codes_used,rpg_character WHERE rpg_character.id = rpg_codes_used.user_id AND rpg_codes_used.code = '{$code}'");

        if ($this->mysql->numRows($pList) > 0) {
            while ($line = $this->mysql->fetchNextObject($pList)) {
                $u[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste aller Codes Connections";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Code nicht genutzt";
        }
        return array("status" => $statusArray, "userusedcode" => $u);
    }

    public function listCodes() {
        $statusArray["status"] = "";
        $statusArray["message"] = "";
        $codeList = $this->mysql->query("SELECT * FROM rpg_codes WHERE 1");
        if ($this->mysql->numRows($codeList) > 0) {
            while ($line = $this->mysql->fetchNextObject($codeList)) {
                $codes[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste aller Codes";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Codes gefunden.";
        }
        return array("status" => $statusArray, "codes" => $codes);
    }

    public function getCodesByID($type, $id) {
        $codeList = $this->mysql->query("SELECT * FROM rpg_codes WHERE code_reward_id='" . $id . "' AND code_type='" . $type . "'");
        if ($this->mysql->numRows($codeList) > 0) {
            while ($line = $this->mysql->fetchNextObject($codeList)) {
                $codes[] = $line;
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Liste aller Codes";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Codes";
        }
        return array("status" => $statusArray, "codes" => $codes);
    }

    public function addCode($code, $type, $id, $amount) {
        $statusArray["status"] = "";
        $statusArray["message"] = "";

        if ((int) $code < 1111) {
            $code = $this->generateRandomString(4);
            while ($this->codeExists($code)) {
                $code = $this->generateRandomString(4);
            }
        }

        if (!$this->codeExists($code)) {
            $this->mysql->execute("INSERT INTO `rpg_codes`(`code`, `code_type`, `code_reward_id`, `code_reward_amount`) VALUES ('" . $code . "','" . $type . "','" . $id . "','" . $amount . "')");
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Code hinzugefügt: " . $code;
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Code existiert bereits";
        }
        return array("status" => $statusArray);
    }

    public function removeCode($code, $type) {
        $statusArray = array("status" => null, "message" => null);
        $this->mysql->query("DELETE FROM rpg_codes WHERE code='{$code}'");
        $this->mysql->query("DELETE FROM rpg_codes_used WHERE code='{$code}'");
        $statusArray["status"] = "ok";
        $statusArray["message"] = "Code gelöscht: " . $code;
        return array("status" => $statusArray);
    }

    //INTERNE FUNKTIONEN//
    /**
     * Session Funktionen
     * @param type $userId
     * @return type
     */
    function code_random($digits) {
        $temp = "";

        for ($i = 0; $i < $digits; $i++) {
            $temp .= rand(0, 9);
        }
        if (strpos($temp, '0') !== false) {
            $temp = code_random($digits);
        } else {
            return (int) $temp;
        }
    }

    public function userUsedCode($code, $userID) {
        $code = $this->mysql->query("SELECT * FROM rpg_codes_used WHERE code='" . $code . "' AND user_id='" . $userID . "'", $this->debug);
        if ($this->mysql->numRows($code) === 0) {
            return false;
        } else {
            return true;
        }
    }

    public function codeExists($code) {
        $code = $this->mysql->query("SELECT * FROM rpg_codes WHERE code='" . $code . "'", $this->debug);
        if ($this->mysql->numRows($code) === 0) {
            return false;
        } else {
            return true;
        }
    }

    public function createSession($userId) {
        $statusArray = array("status" => null, "message" => null);
        $this->mysql->query("DELETE FROM rpg_session WHERE session_time < CURRENT TIMESTAMP - 1 day");
        $this->mysql->query("DELETE FROM rpg_session WHERE user_id='" . $userId . "'");

        $uniqueSessionId = generateRandomString();
        $session = $this->mysql->query("INSERT INTO rpg_session (user_id, session_id) VALUES ('" . $userId . "','" . $uniqueSessionId . "')");
        $session = $this->mysql->fetchNextObject();

        return $uniqueSessionId;
    }

    function generateRandomString($length = 4) {
        return substr(str_shuffle("123456789"), 0, $length);
    }

    function userExists($userId) {
        $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $userId . "'", $this->debug);
        if ($this->mysql->numRows($user) != 0) {
            return true;
        } else {
            return false;
        }
    }

    function itemExists($itemId) {
        $items = $this->mysql->query("SELECT * FROM rpg_items WHERE id='" . $itemId . "'", $this->debug);
        if ($this->mysql->numRows($items) != 0) {
            return true;
        } else {
            return false;
        }
    }

    function userHasQuest($userId, $questId) {
        $quest = $this->mysql->query("SELECT * FROM rpg_questlog WHERE user_id='" . $userId . "' AND quest_id='" . $questId . "'", $this->debug);
        if ($this->mysql->numRows($quest) != 0) {
            return true;
        } else {
            return false;
        }
    }

    function userHasItem($userId, $itemId, $amount) {
        $quest = $this->mysql->query("SELECT *,IF(max(item_amount)>='" . $amount . "','ok','error') as amount_check FROM rpg_inventar WHERE user_id=" . $userId . " AND item_id=" . $itemId, $this->debug);
        $quest = $this->mysql->fetchNextObject();
        if ($quest->user_id != null) {
            if ($quest->amount_check == "ok") {
                return "ok";
            } else {
                return "itemamounterror";
            }
        } else {
            return "itemerror";
        }
    }

    function transferItem($from, $to, $item, $amount) {
        $this->mysql->query("UPDATE rpg_inventar SET item_amount = item_amount - " . $amount . " WHERE user_id = '" . $from . "' AND item_id='" . $item . "'");

        if ($this->userHasItem($to, $item, 1) === "ok") {
            $this->mysql->query("UPDATE rpg_inventar SET item_amount = item_amount + " . $amount . " WHERE user_id = '" . $to . "' AND item_id='" . $item . "'");
        } else {
            $this->mysql->query("INSERT INTO rpg_inventar VALUES (" . $to . "," . $item . "," . $amount . ")");
        }
        $this->mysql->query("DELETE FROM rpg_inventar WHERE item_amount < 1");
        return true;
    }

    function getUserInfo($userId) {
        if ($userId > 0) {
            $user = $this->mysql->query("SELECT * FROM rpg_character, rpg_classes WHERE rpg_character.class = rpg_classes.class_id AND rpg_character.id=" . $userId);
            $user = $this->mysql->fetchNextObject();
            return $user;
        } else {
            return null;
        }
    }

    function getQuestInfo($questId) {
        if ($questId > 0) {
            $quest = $this->mysql->query("SELECT * FROM rpg_quests WHERE id=" . $questId);
            $quest = $this->mysql->fetchNextObject();
            return $quest;
        } else {
            return null;
        }
    }

    function userHasQuestDone($userId, $questId) {
        $this->mysql->query("SELECT * FROM rpg_questlog WHERE user_id='{$userId}' AND quest_id='{$questId}' AND quest_done='1'");
        if ($this->mysql->numRows($quest) != 0) {
            return true;
        } else {
            return false;
        }
    }

    function getItemInfo($itemid) {
        if ($itemid > 0) {
            $item = $this->mysql->query("SELECT * FROM rpg_items WHERE id=" . $itemid);
            $item = $this->mysql->fetchNextObject();
            return $item;
        } else {
            return null;
        }
    }

}
