<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    </head>
    <body>


        <div>
            Userliste:
            <?php
            include_once("rpg.php");
            $rpg = new RPG();
            $rpg->listUser();
            ?>
        </div>
        <div>
            User anzeigen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="getuserlist">
                <input type="hidden" name="debug" value="true">

                <input type="submit" name="submit" value="anzeigen">
            </form>
        </div>

        <div>
            User erstellen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="createuser">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="username" value="username">
                <input type="text" name="password"  value="password">
                <input type="text" name="password2"  value="password2">
                <input type="submit" name="submit" value="erstellen">
            </form>
        </div>

        <div>
            Login:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="loginuser">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="username" value="username">
                <input type="text" name="password" value="password">
                <input type="submit" name="submit" value="login">
            </form>
        </div>

        <div>
            Team beitreten:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="selectteam">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="userid" value="User ID">
                <input type="text" name="teamid" value ="1=Team1, 2=Team2">
                <input type="submit" name="submit" value="team beitreten">
            </form>
        </div>

        <div>
            Klasse wählen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="selectclass">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="userid" value="ID">
                <input type="text" name="classid" value ="1=Klasse1,2=Klasse2,3=Klasse3,...">
                <input type="submit" name="submit" value="Klasse wählen">
            </form>
        </div>

        <div>
            Teams anzeigen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="listteams"> 
                <input type="hidden" name="debug" value="true">
                <input type="submit" name="submit" value="Teams anzeigen">
            </form>
        </div>
        <div>
            Klassen anzeigen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="listclasses">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="teamid" value ="1=Klassen von Team1,2=Klassen von Team2">
                <input type="submit" name="submit" value="Klassen anzeigen">
            </form>
        </div>

        <div><p><strong>Quests</strong></p></div>

        <div>
            Questlog von User anzeigen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="listquestlog">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="userid" value ="User id">
                <input type="text" name="done" value ="0=nicht abgeschlossen, 1=abgeschlossen, 2=alle">
                <input type="submit" name="submit" value="Quest anzeigen">
            </form>
        </div>

        <div>
            Quests anzeigen
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="listquests">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="ownerid" value ="0=Beide Teams, 1=Techno, 2=Avant, nichts=alle Quests">
                <input type="submit" name="submit" value="Quest anzeigen">
            </form>
        </div>
        <div>
            Quests vergeben
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="setquest">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="userid" value ="User Id">
                <input type="text" name="questid" value ="Quest Id">
                <input type="submit" name="submit" value="Quest vergeben">
            </form>
        </div>
        <div>
            Quests abschließen
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="setquestdone">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="userid" value ="User Id">
                <input type="text" name="questid" value ="Quest Id">
                <input type="submit" name="submit" value="Quest abschließen">
            </form>
        </div>
        <div><p><strong>Inventar</strong></p></div>
        <div>
            Inventar anzeigen
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="listinventar">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="userid" value ="User Id">
                <input type="submit" name="submit" value="Inventar anzeigen">
            </form>
        </div>
        <div>
            Transfer Item
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="transferitem">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="itemid" value ="Item id">
                <input type="text" name="fromuserid" value ="Von UserId">
                <input type="text" name="touserid" value ="Zu UserId">
                <input type="text" name="amount" value ="Anzahl">
                <input type="submit" name="submit" value="Item übertragen">
            </form>
        </div>
        <div>
            Enter Code
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="loadcode">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="code" value ="Code">
                <input type="text" name="userid" value ="UserId">

                <input type="submit" name="submit" value="Test Code">
            </form>
        </div>
        <div>
            Add Code
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="addcode">
                <input type="hidden" name="debug" value="true">
                <input name="code" type="text" value="0000">

                <select name="code_type">
                    <option value="item">Item</option>
                    <option value="quest">Quest</option>
                </select>

                <input name="code_reward_id" type="text" value="ID">

                <input name="code_reward_amount" type="text" value="AMOUNT">
                

                <input type="submit" name="submit" value="Add Code">
            </form>
        </div>
    </body>
</html>