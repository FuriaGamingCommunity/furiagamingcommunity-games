<?php

/** 
 * Plugin functions.
 *
 * General plugin functions and helpers for the game post type.
 */

/**
 * Helper function to set context on game race
 * @since 1.0.0
 *
 * @return bool Returns true if the selected race taxonomy is inside the queried post.
 */
function is_game_race() {
	global $wp_query;
	if ( 'game-races' == $wp_query->queried_object->taxonomy )
		return true;
}

/**
 * Helper function to set context on game class
 * @since 1.0.0
 *
 * @return bool Returns true if the selected class taxonomy is inside the queried post.
 */
function is_game_class() {
	global $wp_query;
	if ( 'game-classes' == $wp_query->queried_object->taxonomy )
		return true;
}

/**
 * Helper function to set context on game class
 * @since 1.0.0
 *
 * @return bool Returns true if the selected role taxonomy is inside the queried post.
 */
function is_game_role() {
	global $wp_query;
	if ( 'game-roles' == $wp_query->queried_object->taxonomy )
		return true;
}

/**
 * Helper function to set context on game class
 * @since 1.0.0
 *
 * @return bool Returns true if the selected role taxonomy is inside the queried post.
 */
function is_game_type() {
	global $wp_query;
	if ( 'game-types' == $wp_query->queried_object->taxonomy )
		return true;
}

/**
 * Get all games
 * @since 1.0.0
 *
 * @return array|bool Retrieves a list of posts matching the game type. Returns false if empty.
 */
function get_games() {
	
	$args = array(
		'post_type'         => 'game',
		'post_status'       => 'publish'
		);

	return get_posts( $args );
}

/**
 * Get all races terms
 * @since 1.1.0
 *
 * @return array List of terms under game-races.
 */
function get_terms_races() {
	return get_terms( 'game-races' );
}

/**
 * Get all classes terms
 * @since 1.1.0
 *
 * @return array List of terms under game-classes.
 */
function get_terms_classes() {
	return get_terms( 'game-classes' );
}

/**
 * Get all roles terms
 * @since 1.1.0
 *
 * @return array List of terms under game-roles.
 */
function get_terms_roles() {
	return get_terms( 'game-roles' );
}

/**
 * Get all types terms
 * @since 1.1.0
 *
 * @return array List of terms under game-types.
 */
function get_terms_types() {
	return get_terms( 'game-types' );
}

/**
 * Get a single game by the slug
 * @since 1.0.0
 *
 * @return array|bool Retrieves a posts matching the game slug. Returns false in case the slug was not provided.
 */
function get_game_by_slug( $slug ) {
	
	if ( '' != $slug ) {
		$args = array(
			'post_name'   => $slug,
			'post_type'   => 'game',
			'post_status' => 'publish',
			'numberposts' => 1
			);
		
		$game = get_posts($args);

		return $game[0];

	} else return false;
}

/**
 * Get a single term from game races by the slug
 * @since 1.1.0
 *
 * @param string $slug Term slug to look after.
 * @return WP_Term|bool Returns the term object.
 */
function get_term_race_by_slug( $slug ) {
	if ( $slug)
		return get_term_by( 'slug', $slug, 'game-races' );
	else
		return false;
}

/**
 * Get a single term from game classes by the slug
 * @since 1.1.0
 *
 * @param string $slug Term slug to look after.
 * @return WP_Term|bool Returns the term object.
 */
function get_term_class_by_slug( $slug ) {
	if ( $slug)
		return get_term_by( 'slug', $slug, 'game-classes' );
	else
		return false;
}

/**
 * Get a single term from game roles by the slug
 * @since 1.1.0
 *
 * @param string $slug Term slug to look after.
 * @return WP_Term|bool Returns the term object.
 */
function get_term_role_by_slug( $slug ) {
	if ( $slug)
		return get_term_by( 'slug', $slug, 'game-roles' );
	else
		return false;
}

/**
 * Get a single term from game types by the slug
 * @since 1.1.0
 *
 * @param string $slug Term slug to look after.
 * @return WP_Term|bool Returns the term object.
 */
function get_term_type_by_slug( $slug ) {
	if ( $slug)
		return get_term_by( 'slug', $slug, 'game-types' );
	else
		return false;
}

/**
 * Get game races
 * @since 1.0.0
 *
 * @return array List of post terms under game races.
 */
function get_game_races( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-races' );
}

/**
 * Get game classes
 * @since 1.0.0
 *
 * @return array List of post terms under game classes.
 */
function get_game_classes( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-classes' );
}

/**
 * Get game roles
 * @since 1.0.0
 *
 * @return array List of post terms under game roles.
 */
function get_game_roles( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-roles' );
}

/**
 * Get game types
 * @since 1.1.0
 *
 * @return array List of post terms under game types.
 */
function get_game_types( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-types' );
}

/**
 * Admin Messages
 */

function admin_notices_bp_groups_missing() {	
	echo '<div class="notice update-nag">' . sprintf( __( 'The Game Group extension needs <strong><a href="%1$s">BuddyPress</a> <em>Groups</em></strong> extension enabled in order to function correctly.', 'furiagamingcommunity_games'), admin_url('options-general.php?page=bp-components') ) . '</div>';
}
?>
