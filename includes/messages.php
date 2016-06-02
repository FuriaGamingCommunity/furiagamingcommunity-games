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
function admin_notices_missing_bp_groups() {	
	echo '<div class="notice-warning">' . message_missing_bp_groups() . '</div>';
	return null;
}

/**
 * Echoes missing games message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_games() {
	echo '<div class="notice-warning">' . message_missing_games() . '</div>';
	return null;
}

/**
 * Echoes missing BP_Group_Extension message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_game_type() {	
	echo '<div class="notice-error">' . message_missing_game_type() . '</div>';
	return null;
}

/**
 * Echoes missing term message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_term() {
	echo '<div class="notice-error">' . message_missing_term() . '</div>';
	return null;
}

/**
 * Echoes missing term permalink message as an administration notice.
 * 
 * @since 1.1.0
 * @return null
 */
function admin_notices_missing_term_permalink() {
	echo '<div class="notice-error">' . message_missing_term_permalink() . '</div>';
	return null;
}

/**
 * Returns a formatted message for invalid game groups.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_invalid_bp_groups_groupmeta_groupgame() {
	return __( 'Game Group was no longer valid and was unset.', 'furiagamingcommunity_games');
}

/**
 * Returns a formatted message for missing BP_Group_Extension.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_bp_groups() {
	return sprintf( __( 'Game Group extension needs <strong><a href="%1$s">BuddyPress</a> <em>Groups</em></strong> extension enabled in order to function correctly.', 'furiagamingcommunity_games'), admin_url('options-general.php?page=bp-components') );
}

/**
 * Returns a formatted message for missing games.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_games() {
	return __('There are not any games to display.','furiagamingcommunity_games');
}

/**
 * Returns a formatted message for missing terms.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_term( $term ) {
	return __( 'An error occurred while retrieving the term', 'furiagamingcommunity_games') . ( ( empty( $type ) ) ? '.' : sprintf( ': %1$s', $term->get_error_message() ) );
}

/**
 * Returns a formatted message for missing terms permalinks.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_term_permalink( $term ) {
	return __( 'An error occurred while retrieving the term permalink', 'furiagamingcommunity_games') . ( ( empty( $type ) ) ? '.' : sprintf( ': %1$s', $term->get_error_message() ) );
}

/**
 * Returns a formatted message for missing game types.
 *
 * @since 1.1.0
 * @return string A formatted message
 */
function message_missing_game_type( $type ) {
	return __( 'An error occurred while adding the selected game group type', 'furiagamingcommunity_games') . ( ( empty( $type ) ) ? '.' : sprintf( ': %1$s', $type->slug ) );
}
?>