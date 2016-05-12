$(document).ready(function () {
    var postUrl = "http://pb.ingamelandscapes.de/query.php";


    $("#login-btn").on("click", function () {

        var loginStr = $("#inputUser").val();
        var passwordStr = $("#inputPassword").val();

        //Daten die gesendet werden müssen für die funktion "loginuser"
        //Vergleiche mit quellcode von: http://pb.ingamelandscapes.de/

        var sendData = {action: "loginuser", username: loginStr, password: passwordStr}; //Array 

        $.ajax({
            url: postUrl,
            type: "POST",
            data: sendData,
            success: function (data, textStatus, jqXHR)
            {
                //data ist die rückgabe vom server, zurückgegeben wird ein JSON Array (bestimmte art der Formatierung)
                //man kann dann einfach drauf zugreifen z.b via returnData.status (gibt das Array zurück mit dem Inhalt "status" und "userInfo")
                //returnData.status.status ist entweder "ok" oder "error" was genau für antworten kommen kannst du auch oben auf der testseite ausprobieren und danach dann die funktionen schreiben

                var returnData = JSON.parse(data);

                //Ist der login ok? 

                if (returnData.status.status === "ok") {
                    //Login ist richtig
                    //hier müssen wir jetzt der App sagen, dass sie die ID speichern soll damit wir auf jeder "Seite" wissen, dass der User eingeloggt ist
                    /*                    
                     speichern:
                     localStorage.setItem("pref1", "val1");
                     
                     lesen:
                     var pref1 = localStorage.getItem("pref1");
                     */

                    localStorage.setItem("USERID", returnData.userInfo.id);


                    // !!! eigentlich müssten wir hier jetzt testen ob der user bereits in einem team ist und  schon eine klasse hat wenn nicht, dann weiterleitung an die jeweilige seite, wenn doch dann leiten wir direkt zur profilübersicht weiter
                    
                    if(returnData.userInfo.team === "0"){
                        //user hat noch kein team gewählt, weiterleitung zur team seite
                        alert("Team wählen!");
                    }else if(returnData.userInfo.class === "0"){
                        //user hat noch keine klasse gewählt, weiterleitung zur klassen seite
                        alert("Klasse wählen!");
                    }else{
                        //user hat bereits alles gewählt weiterleitung zum profil
                        alert("Anmeldung abgeschlossen!");
                    }

                    //hier jetzt zum testen und weil ich nicht weis wie genau und wann man team und klasse wählt leiten wir einfach an irgendeine seite weiter
                    //auf allen seiten  außer der login seite müsste am anfang bevor alles andere kommt erst mal abgefragt werden ob bereits ein user eingeloggt ist ich denke hier reicht die abfrage ob im localStorage die USERID gesetzt ist oder nicht, wenn ja kennen wir ja den user und können alle funktionen mit diesder ID abrufen
                    window.location = "faction.html";

                } else {
                    //nur zum debuggen
                    alert(returnData.status.status + ": " + returnData.status.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Connection failed");
            }
        });
    });
});