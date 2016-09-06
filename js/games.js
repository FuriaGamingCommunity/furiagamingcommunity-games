jQuery( document ).on( 'change', '#user_game', function() {
    var game_id = jQuery(this).data('id');

    console.log(game_id);

    alert('USER GAME SELECT');
    /*jQuery.ajax({
        url : games.ajax_url,
        type : 'post',
        data : {
            action : '',
            post_id : post_id
        },
        success : function( response ) {
            alert(response)
        }
    });*/
})