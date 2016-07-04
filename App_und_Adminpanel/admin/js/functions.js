$(document).ready(function () {

    $(".response").css("border-color", "#40b6ff");
    window.setTimeout(function () {
        $(".response").css("border-color", "#666");
        window.setTimeout(function () {
            $(".response").css("border-color", "#40b6ff");
            window.setTimeout(function () {
                $(".response").css("border-color", "#666");
                $(".response").delay(2000).fadeOut();
            }, 500);
        }, 500);
    }, 500);

    $("#weaponChange").change(function () {
        $("#weaponChangeTarget").find("option").addClass("hidden");
        $("#weaponChangeTarget").find(".user" + $(this).val()).removeClass("hidden");
    });

    $("a[id^='load']").on("click", function () {
        if (this.id === "load-user") {
            ajaxCall({"action": "getuserlist"});
        } else if (this.id === "load-items") {
            loadItems();
        } else if (this.id === "load-quests") {
            loadQuests();
        }
    });

    $(document).on("click", "a[id^='transferItem-']", function () {
        var itemId = $(this)["context"].id.replace("transferItem-", "");
        var fromUserId = $(this).attr("alt");
        var toUserId = $(this).parent().find("#toUserId").children(":selected").attr("id");
        var itemAmount = $(this).parent().find("#itemAmount").val();
        console.log("FROM: " + fromUserId + " - TO: " + toUserId + " - ITEM: " + itemId + " - AMOUNT: " + itemAmount);

        ajaxCall({action: "transferitem", itemid: itemId, fromuserid: fromUserId, touserid: toUserId, amount: itemAmount});

    });


    jQuery.expr[':'].Contains = function (a, i, m) {
        return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    function listFilter(list) {
        input = $("#search");

        $(input).change(function () {
            var filter = $(this).val();
            if (filter) {
                $(list).find("a:not(:Contains(" + filter + "))").slideUp();
                $(list).find("a:Contains(" + filter + ")").slideDown();
            } else {
                $(list).find("a").slideDown();
            }
            return false;
        })
                .keyup(function () {
                    $(this).change();
                });
    }

    listFilter($(".leftContent .content"));




});


function ajaxCall(postData) {
    // var postData = {name: "ravi", age: "31"};
    loading(true);
    $.ajax({
        url: "query.php",
        type: "post",
        data: postData,
        success: function (response) {
            if (response.indexOf("error#") === -1) {
                //console.log(response);
                switch (postData.action) {
                    case "getuserlist":
                        $(".leftContent .content").html(response);
                        break;
                    case "getuserdetails":
                        $(".rightContent .content").html(response);
                        break;
                    case "transferitem":
                        response = JSON.parse(response);
                        alert("Status: " + response.status.status + "\nMessage: " + response.status.message);
                        break;
                    default:
                        alert("Switch fail");
                }


            } else {
                alert("An error occurred: " + response);
            }
            loading(false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
            loading(false);
        }
    });
}

function loading(state) {
    if (state) {
        $(".loading").fadeIn("fast");
    } else {
        $(".loading").fadeOut("fast");
    }
}

function transferItem() {

}