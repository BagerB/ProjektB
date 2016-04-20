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
            Quests von Questgeber anzeigen
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="listquests">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="ownerid" value ="OwnerId">
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
    </body>
</html>