jQuery(document).ready(function() {
	jQuery('#user_game').change(function() {

		var game_dropdown 			= jQuery(this);
		var inner_dropdowns 		= ['#user_game_races','#user_game_classes','#user_game_roles'];
		var nonce 					= jQuery('#user_profile_game_nonce');

		var inner_dropdowns_options = new Array();

		// Fill inner dropdown options array.
		for(var index = 0; index < inner_dropdowns.length; index++){			
			inner_dropdowns_options.push(inner_dropdowns[index] + ' option');
		}

		// Reset selections.
		resetMultipleDropdowns(inner_dropdowns);	
		// Disable the dropdowns while we load the terms.
		toggleMultipleObjects(inner_dropdowns,true);
		// Hide all options but default.
		toggleMultipleOptions(inner_dropdowns_options);	

		if(jQuery(this).val() != ''){

			// Disable the dropdowns while we load the terms.
			toggleMultipleObjects(inner_dropdowns,true);

			// Show the spinner.
			toggleFadeObject(jQuery('#user_game + .spinner'));

			jQuery.ajax({
				type : 'post',
				dataType : 'json',
				url : game_ajax.ajax_url,
				data : {
					action: 'fill_game_terms',
					post_id : game_dropdown.val()
				},

				error: function(response){
					console.log('fill_game_terms.error', response);
				},
				success: function(response){
					console.log('fill_game_terms.success', response);

					var game_terms = new Array();

					for(var taxonomy in response){

						select = document.getElementById('user_game_' + taxonomy);

						if(response[taxonomy].length > 0){

							jQuery('#user_game_races option').each(function(){
								for(var index = 0; index < response[taxonomy].length; index++){			
									// Show the allowed terms.
									if (jQuery(this).val() == response[taxonomy][index].term_id ) jQuery(this).show();
								}
							});

							jQuery('#user_game_classes option').each(function(){
								for(var index = 0; index < response[taxonomy].length; index++){				
									// Show the allowed terms.
									if (jQuery(this).val() == response[taxonomy][index].term_id ) jQuery(this).show();
								}
							});

							jQuery('#user_game_roles option').each(function(){
								for(var index = 0; index < response[taxonomy].length; index++){				
									// Show the allowed terms.
									if (jQuery(this).val() == response[taxonomy][index].term_id ) jQuery(this).show();
								}
							});

							// Re-enable the dropdowns.
							toggleMultipleObjects(inner_dropdowns,false);

							// Hide the spinners.
							toggleFadeObject(jQuery('#user_game + .spinner'));
						} else {
							// No terms.
						}
					}
				}
			});
		}

		function resetMultipleDropdowns(param){

			if(param.constructor === Array){
				for (var index = 0; index < param.length; ++index) {
					resetDropdown(jQuery(param[index]));
				}				
			} else {
				return false;
			}
			return true;
		}

		function resetDropdown(param){

			if(param!== undefined && param.length){
				param.prop('selectedIndex',0);	
			} else {
				return false;
			}
			return true;
		}

		function toggleMultipleOptions(param){
			
			if(param.constructor === Array){
				for (var index = 0; index < param.length; ++index) {
					jQuery(param[index]).each(function(){
						toggleOption(jQuery(this));
					});		
				}

			} else {
				return false;
			}
			return true;
		}

		function toggleOption(param){
			
			if(param!== undefined && param.length){
				if(param.val() != '' ) param.hide();
			} else {
				return false;
			}
			return true;
		}

		function toggleMultipleObjects(param,disable){
			disable = typeof disable !== 'undefined' ? disable : true;

			if(param.constructor === Array){
				for (var index = 0; index < param.length; ++index) {
					toggleObject(jQuery(param[index]),disable);
				}				
			} else {
				return false;
			}
			return true;
		}

		function toggleObject(param,disable){
			disable = typeof disable !== 'undefined' ? disable : true;

			if(param!== undefined && param.length){
				if(param.is(':disabled') && !disable){
					param.prop('disabled',false);
				} else {
					param.prop('disabled',true);
				}			
			} else {
				return false;
			}
			return true;
		}

		function toggleFadeMultipleObjects(param,delay){
			delay = typeof delay !== 'undefined' ? delay : 300;

			if(param.constructor === Array){
				for (var index = 0; index < param.length; ++index) {
					toggleFadeObject(jQuery(param[index],delay));
				}				
			} else {
				return false;
			}
			return true;
		}

		function toggleFadeObject(param,delay){
			delay = typeof delay !== 'undefined' ? delay : 300;

			if(param!== undefined && param.length){
				if(param.hasClass('is-active')){
					param.fadeOut(delay, function(){
						jQuery(this).removeClass('is-active');
					});
				} else {
					param.fadeIn(delay, function(){
						jQuery(this).addClass('is-active');
					});
				}			
			} else {
				return false;
			}
			return true;
		}
	});
});