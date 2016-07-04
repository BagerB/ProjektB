<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Live view</title>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <script src="js/jquery-1.12.3.min.js" type="text/javascript"></script>

        <script>
            $(document).ready(function () {
                
                function outerHTML(node) {
                    return node.outerHTML || new XMLSerializer().serializeToString(node);
                }
                
                setInterval(function () {

                    $.post("action.php", {action: "listplayers", fieldid: '<?php echo $_GET["id"]; ?>'})
                            .done(function (data) {
                                data = JSON.parse(data);

                                var container = document.createElement("div");
                                
                                


                                for (var i = 0; i < data["players"].length; i++) {
                                    var obj = data["players"][i];
                                    var row = document.createElement("div");
                                    row.className = "live-row";
                                    var name = document.createElement("div");
                                    var hp = document.createElement("div");
                                    var stamina = document.createElement("div");
                                    var reaction = document.createElement("div");
                                    hp.className = "live-row-stat";
                                    stamina.className = "live-row-stat";
                                    reaction.className = "live-row-stat";
                                    name.className = "live-row-name";
                                    
                                    
                                    
                                    name.innerHTML = obj.name;
                                    hp.innerHTML = "HP: " +obj.hp;
                                    stamina.innerHTML = "Stamina: "+ obj.stamina;
                                    reaction.innerHTML = "Reaction: " + obj.reaction;
                                    
                                    row.appendChild(name);
                                    row.appendChild(hp);
                                    row.appendChild(stamina);
                                    row.appendChild(reaction);
                                    container.appendChild(row);
                                    document.getElementById("feld").innerHTML = obj.feld_name;
                                }

                                document.getElementById("spieler").innerHTML = outerHTML(container);

                            });


                }, 200);
                
                

            });
        </script>
    </head>
    <body>
        <div id="feld">
            
        </div>
        <div id="spieler">
            
        </div>
    </body>
</html>