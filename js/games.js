jQuery( document ).on( 'change', '#user_game', function() {
    jQuery.ajax({
        url : games.ajax_url,
        type : 'post',
        data : {
            action : 'get_game_terms'
        },
        success : function( response ) {
            alert(response)
        }
    });
})