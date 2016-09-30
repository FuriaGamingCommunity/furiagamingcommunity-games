<?php
/** 
 * Plugin messages.
 */

/**
 * Echoes missing BP_Group_Extension message as an administration notice.
 *
 * @since 1.0.0
 * @return null
 */
function admin_notices_invalid_nonce(){
	$class = 'notice notice-error';
	$message = message_invalid_nonce();

	printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message); 

	return null;
}

/**
 * Echoes missing BP_Group_Extension message as an administration notice.
 *
 * @since 1.0.0
 * @return null
 */
function admin_notices_missing_bp_groups(){
	$class = 'notice notice-warning';
	$message = message_missing_bp_groups();

	printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message); 

	return null;
}

/**
 * Echoes missing games message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_games(){
	$class = 'notice notice-warning';
	$message = message_missing_games();

	printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message); 

	return null;
}

/**
 * Echoes missing BP_Group_Extension message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_game_type(){
	$class = 'notice notice-error';
	$message = message_missing_game_type();

	printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message); 

	return null;
}

/**
 * Echoes missing term message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_term(){
	$class = 'notice notice-error';
	$message = message_missing_term();

	printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message); 

	return null;
}

/**
 * Echoes missing term permalink message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_term_permalink(){
	$class = 'notice notice-error';
	$message = message_missing_term_permalink();

	printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message); 

	return null;
}

/**
 * Returns a formatted message for invalid game groups.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_invalid_bp_groups_groupmeta_groupgame(){
	return __('Game Group was no longer valid and was unset.', 'furiagamingcommunity_games');
}

/**
 * Returns a formatted message for invalid nonces.
 *
 * @since 1.2.0
 * @return string A formatted message
 */
function message_invalid_nonce(){
	return __('Game nonce field validation failed.', 'furiagamingcommunity_games');
}

/**
 * Returns a formatted message for missing BP_Group_Extension.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_bp_groups(){
	return sprintf(__('<em>Game Group Extension</em> needs <strong><a href="%1$s">BuddyPress <em>Groups Extension</em></a></strong> extension enabled in order to function correctly.', 'furiagamingcommunity_games'), admin_url('options-general.php?page=bp-components'));
}

/**
 * Returns a formatted message for missing games.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_games(){
	return __('There are not any games to display.','furiagamingcommunity_games');
}

/**
 * Returns a formatted message for missing terms.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_term($term){
	return __('An error occurred while retrieving the term', 'furiagamingcommunity_games') . ((empty($type)) ? '.' : sprintf(': %1$s', $term->get_error_message()));
}

/**
 * Returns a formatted message for missing terms permalinks.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_term_permalink($term){
	return __('An error occurred while retrieving the term permalink', 'furiagamingcommunity_games') . ((empty($type)) ? '.' : sprintf(': %1$s', $term->get_error_message()));
}

/**
 * Returns a formatted message for missing game types.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_game_type($type){
	return __('An error occurred while adding the selected game group type', 'furiagamingcommunity_games') . ((empty($type)) ? '.' : sprintf(': %1$s', $type->slug));
}

/**
 * Returns a formatted message for a game group update.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_updated_game_group(){
	return __('<em>Game</em> group type was updated successfully.',  'furiagamingcommunity_games');
}
?>
