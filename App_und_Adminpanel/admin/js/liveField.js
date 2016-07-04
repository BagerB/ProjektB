$(document).ready(function () {
    setInterval(function () {

        $.post("../../query.php", {name: "John", time: "2pm"})
                .done(function (data) {
                    alert("Data Loaded: " + data);
                });


    }, 3000);
});