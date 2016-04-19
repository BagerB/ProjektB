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
                <input type="text" name="id" value="ID">
                <input type="text" name="team" value ="1 oder 2">
                <input type="submit" name="submit" value="team beitreten">
            </form>
        </div>

        <div>
            Klasse wählen:
            <form action="user.php" method="POST">
                <input type="hidden" name="action" value="selectclass">
                <input type="hidden" name="debug" value="true">
                <input type="text" name="id" value="ID">
                <input type="text" name="class" value ="1 bis 12">
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
                <input type="text" name="team" value ="1 oder 2">
                <input type="submit" name="submit" value="Klassen anzeigen">
            </form>
        </div>



    </body>
</html>