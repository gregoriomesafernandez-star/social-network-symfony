$(document).ready(function (){
    if($(".label-notifications").text() == 0){

        $(".label-notifications").addClass("d-none");

    }else{
        $(".label-notifications").removeClass("d-none");
    }

    if($(".label-notifications-msg").text() == 0){

        $(".label-notifications-msg").addClass("d-none");

    }else{
        $(".label-notifications-msg").removeClass("d-none");
    }

    if(window.userLogged){
        notifications();

        setInterval(function (){
            notifications();
        }, 13000);
    }
    
});

function notifications(){

        $.ajax({
            url: window.routes.notifications,
            type: 'GET',
            success: function (response) {

                $(".label-notifications").html(response);

                if(response == 0){

                    $(".label-notifications").addClass("d-none");

                }else{

                    $(".label-notifications").removeClass("d-none");

                }
            }
        });

        $.ajax({
            url: window.routes.messages,
            type: 'GET',
            success: function (response) {

                $(".label-notifications-msg").html(response);

                if(response == 0){

                    $(".label-notifications-msg").addClass("d-none");

                }else{

                    $(".label-notifications-msg").removeClass("d-none");

                }
            }
        });
    }