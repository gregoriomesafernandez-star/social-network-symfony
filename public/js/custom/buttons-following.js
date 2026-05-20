function followButtons() {
    
    $(document).off('click', '.btn-follow').on('click', '.btn-follow',function(){
        
        $(this).addClass("d-none");
        $(this).parent().find(".btn-unfollow").removeClass("d-none");

        $.ajax({
            url: BASE_URL + '/follow',
            type: 'POST',
            data: {
                followed: $(this).attr("data-followed")
            },
            success: function(response){
                console.log(response);
            }
        });
    });

    $(document).off('click', '.btn-unfollow').on('click', '.btn-unfollow',function(){
        
        $(this).addClass("d-none");
        $(this).parent().find(".btn-follow").removeClass("d-none");

        $.ajax({
            url: BASE_URL + '/unfollow',
            type: 'POST',
            data: {
                followed: $(this).attr("data-followed")
            },
            success: function(response){
                console.log(response);
            }
        });
    });
}