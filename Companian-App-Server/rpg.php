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

    /**
     * lists all Userinformation
     */
    function listUser() {
        $this->mysql->query("SELECT * FROM rpg_character, rpg_classes WHERE rpg_character.class = rpg_classes.id ORDER BY rpg_character.id", true);
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
                $returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
            }
        }
        return array("response" => $statusArray, "userInfo" => $returnInfo);
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
                    $returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
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
                $classArray[] = array("id" => $line->id, "class_team" => $line->class_team, "class_name" => $line->class_name);
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

        if ((is_int($team) && ($team == 1 || $team == 2))) {
            $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $id . "'", $this->debug);
            if ($this->mysql->numRows($user) != 0) {
                $user = $this->mysql->fetchNextObject();
                if ($user->team === "0") {
                    $this->mysql->query("UPDATE rpg_character SET team='" . $team . "' WHERE id='" . $id . "'", $this->debug);
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Dem Team wurde beigetreten";
                    $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $id . "'");
                    $user = $this->mysql->fetchNextObject();
                    $returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
                } else {
                    //user bereits in einem team
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Bereits in einem Team";
                    $returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
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

        $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $id . "'", $this->debug);





        if ($this->mysql->numRows($user) != 0) {
            $user = $this->mysql->fetchNextObject();

            $class = $this->mysql->query("SELECT * FROM rpg_classes WHERE id='" . $classID . "' AND class_team='" . $user->team . "'", $this->debug);
            $class = $this->mysql->fetchNextObject();

            if ($user->class === "0") {

                //Gibt es die Klasse?
                if ($class) {
                    $this->mysql->query("UPDATE rpg_character SET class='" . $classID . "' WHERE id='" . $id . "'", $this->debug);
                    $statusArray["status"] = "ok";
                    $statusArray["message"] = "Charakter hat seine Klasse gewählt";
                    $user = $this->mysql->query("SELECT * FROM rpg_character WHERE id='" . $id . "'");
                    $user = $this->mysql->fetchNextObject();
                    $returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
                } else {
                    $statusArray["status"] = "error";
                    $statusArray["message"] = "Klasse nicht gefunden (in diesem Team?)";
                }
            } else {
                //user hat bereits eine klasse
                $statusArray["status"] = "error";
                $statusArray["message"] = "Bereits Klasse gewählt";
                $returnInfo = array("id" => $user->id, "class" => $user->class, "team" => $user->team, "admin" => $user->admin);
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
            $quests = $this->mysql->query("SELECT * FROM rpg_questlog, rpg_quests WHERE rpg_questlog.quest_done='1' AND rpg_questlog.user_id='" . $userId . "' AND rpg_questlog.quest_id = rpg_quests.id",$this->debug);
        } elseif($done === "0") {
            $quests = $this->mysql->query("SELECT * FROM rpg_questlog, rpg_quests WHERE rpg_questlog.quest_done='0' AND rpg_questlog.user_id='" . $userId . "' AND rpg_questlog.quest_id = rpg_quests.id",$this->debug);
        }elseif($done === "2"){
            $quests = $this->mysql->query("SELECT * FROM rpg_questlog, rpg_quests WHERE rpg_questlog.user_id='" . $userId . "' AND rpg_questlog.quest_id = rpg_quests.id",$this->debug);
        }

        

        if ($this->mysql->numRows($quests) != 0) {
            while ($line = $this->mysql->fetchNextObject($quests)) {
                $questArray[] = array("quest_name" => $line->quest_name, "quest_description" => $line->quest_description, "quest_reward" => $line->quest_reward_item_id, "quest_reward_amount" => $line->quest_reward_amount,"quest_done"=>$line->quest_done);
            }
            $statusArray["status"] = "ok";
            $statusArray["message"] = "Questlogrückgabe";
        } else {
            $statusArray["status"] = "error";
            $statusArray["message"] = "Keine Quests gefunden";
        }
        
         return array("status" => $statusArray, "quests" => $questArray);
    }

    //INTERNE FUNKTIONEN//
    /**
     * Session Funktionen
     * @param type $userId
     * @return type
     */
    public function createSession($userId) {
        $statusArray = array("status" => null, "message" => null);
        $this->mysql->query("DELETE FROM rpg_session WHERE session_time < CURRENT TIMESTAMP - 1 day");
        $this->mysql->query("DELETE FROM rpg_session WHERE user_id='" . $userId . "'");

        $uniqueSessionId = generateRandomString();
        $session = $this->mysql->query("INSERT INTO rpg_session (user_id, session_id) VALUES ('" . $userId . "','" . $uniqueSessionId . "')");
        $session = $this->mysql->fetchNextObject();

        return $uniqueSessionId;
    }

    function generateRandomString($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

}
