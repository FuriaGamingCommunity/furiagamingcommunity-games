<?php
/** 
 * Plugin functions.
 */

/**
 * Helper function to check if the current post is a game.
 * @since 1.0.0
 *
 * @param int|string|array $post Post ID, title, slug, or array of such.
 * @return bool Returns true if the selected race taxonomy is inside the queried post.
 */
function is_game( $post = '' ) {
	if ( !empty( $post ) ) {
		if ( is_string( $post ) ) $post = get_game_by_slug( $post );
		if ( get_post_type( $post ) == 'game' ) {
			return true;
		} else {
			return false;
		}
	} else {
		if ( is_singular( 'game' ) ) {
			return true;
		} else {
			return false;
		}
	}
}

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
 * Helper function to set context on game role
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
 * Helper function to set context on game type
 * @since 1.0.0
 *
 * @return bool Returns true if the selected type taxonomy is inside the queried post.
 */
function is_game_type() {
	global $wp_query;
	if ( 'game-types' == $wp_query->queried_object->taxonomy )
		return true;
}

/**
 * Helper function to set context on game type
 * @since 1.1.0
 *
 * @return bool Returns true if the selected type taxonomy is inside the queried post.
 */
function is_game_group() {
	if ( class_exists( 'BP_Group_Extension' ) ) {
		
		// Check for group page.
		if ( bp_is_group() ) {

			$group_game = groups_get_groupmeta( bp_get_group_id(), 'group-game' );

			// Check if group meta was set correctly.
			if ( $group_game && is_game( $group_game ) ) {
				// Ok.
				return true;
			} else {
				// No game set.
				return false;
			}

		} else {
			// Not a group.
			return false;
		}

	} else {

		// Add an admin message.
		if ( is_admin() ) {
			if ( !has_action( 'admin_notices', 'admin_notices_missing_bp_groups' ) )
				add_action( 'admin_notices', 'admin_notices_missing_bp_groups' );
		}
		// Add a BuddyPress message.
		if ( is_buddypress() )
			bp_core_add_message( message_missing_bp_groups(), 'error' );

		// Not a group (as groups are not enabled).
		return false;
	}
}

function is_dedicated_game_group() {
	if ( class_exists( 'BP_Group_Extension' ) ) {

		if ( is_game_group() ) {
			
			$game_group_type = groups_get_groupmeta( bp_get_group_id() , 'group-game-type' );

			if ( $game_group_type ) {

				$game_option = get_game_dedicated_setting();

				if ( ( $game_group_type == $game_option ) && !empty( $game_option ) )
					return true;
				else
					// Not a dedicated group.
					return false;
			} else {
				
				// Not a group.
				return false;
			}

		}
	} else {

		// Add an admin message.
		if ( is_admin() ) {
			if ( !has_action( 'admin_notices', 'admin_notices_missing_bp_groups' ) )
				add_action( 'admin_notices', 'admin_notices_missing_bp_groups' );
		}
		// Add a BuddyPress message.
		if ( is_buddypress() )
			bp_core_add_message( message_missing_bp_groups(), 'error' );

		// Not a group (as groups are not enabled).
		return false;
	}
}

function is_semidedicated_game_group() {
	if ( class_exists( 'BP_Group_Extension' ) ) {

		if ( is_game_group() ) {
			
			$game_group_type = groups_get_groupmeta( bp_get_group_id() , 'group-game-type' );

			if ( $game_group_type ) {

				$game_option = get_game_semidedicated_setting();

				if ( ( $game_group_type == $game_option ) && !empty( $game_option ) )
					return true;
				else
					// Not a dedicated group.
					return false;
			} else {
				
				// Not a group.
				return false;
			}

		}
	} else {

		// Add an admin message.
		if ( is_admin() ) {
			if ( !has_action( 'admin_notices', 'admin_notices_missing_bp_groups' ) )
				add_action( 'admin_notices', 'admin_notices_missing_bp_groups' );
		}
		// Add a BuddyPress message.
		if ( is_buddypress() )
			bp_core_add_message( message_missing_bp_groups(), 'error' );

		// Not a group (as groups are not enabled).
		return false;
	}
}

/**
 * Retrieves game data given a game post ID or post object.
 * @since 1.1.0
 *
 * @param int|WP_Post|null $post   Optional. Post ID or post object. Defaults to global $post.
 * @param string           $output Optional, default is Object. Accepts OBJECT, ARRAY_A, or ARRAY_N.
 *                                 Default OBJECT.
 * @param string           $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db',
 *                                 or 'display'. Default 'raw'.
 * @return WP_Post|array|null Type corresponding to $output on success or null on failure.
 *                            When $output is OBJECT, a `WP_Post` instance is returned.
 */
function get_game( $post = null, $output = OBJECT, $filter = 'raw' ) {
	if( is_game( $post ) ) {
		return get_post( $post, $output, $filter );
	}
	return null;
}

/**
 * Retrieves all games
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
 * @since 1.0.0
 *
 * @return array List of terms under game-races.
 */
function get_terms_races() {
	return get_terms( 'game-races', array( 'hide_empty' => false ) );
}

/**
 * Get all classes terms
 * @since 1.0.0
 *
 * @return array List of terms under game-classes.
 */
function get_terms_classes() {
	return get_terms( 'game-classes', array( 'hide_empty' => false ) );
}

/**
 * Get all roles terms
 * @since 1.0.0
 *
 * @return array List of terms under game-roles.
 */
function get_terms_roles() {
	return get_terms( 'game-roles', array( 'hide_empty' => false ) );
}

/**
 * Get all types terms
 * @since 1.0.0
 *
 * @return array List of terms under game-types.
 */
function get_terms_types() {
	return get_terms( 'game-types', array( 'hide_empty' => false ) );
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
 * @since 1.0.0
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
 * @since 1.0.0
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
 * @since 1.0.0
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
 * @since 1.0.0
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
 * Generate a formatted tag permalink
 * @since 1.0.0
 *
 * @param int|WP_Term $term The term ID or the term object.
 * @param string $taxonomy The taxonomy name.
 * @return bool|string A fully HTML generated permalink to the tag or a boolean in case of error.
 */
function get_term_permalink( $term, $taxonomy ) {

	// Get the term.
	$term = get_term( $term, $taxonomy );

	// Check if the term could not be retrieved.
	if ( is_wp_error( $term ) ) {
		if ( is_admin() ) {
			admin_notices_missing_term( $result->get_error_message(), 'error is-dismissible' );
		} elseif ( is_buddypress() ) {
			bp_core_add_message( admin_notices_missing_term(), 'error' );
		} return false;
	}

	// Get the term link.
	$result = get_term_link( $term->term_id, $taxonomy );

	// Check if the term link could not be retrieved.
	if ( is_wp_error( $result ) ) {
		if ( is_admin() ) {
			admin_notices_missing_term_permalink( $result->get_error_message(), 'error is-dismissible' );
		} elseif ( is_buddypress() ) {
			bp_core_add_message( admin_notices_missing_term_permalink(), 'error' );
		} return false;
	} else
		return '<a href="' . $result . '">' . $term->name . '</a>';
}

/**
 * Get game terms
 * @since 1.2.0
 *
 * @param int $post_id Post ID.
 * @return array An array filled with all terms associated to the game meta.
 */
function get_game_terms( $post_id ) {
	$terms['races'] = get_game_races($post_id);
	$terms['classes'] = get_game_classes($post_id);
	$terms['roles'] = get_game_roles($post_id);
	$terms['types'] = get_game_types($post_id);

	var_dump($terms);
	
	return $terms;
}

/**
 * Get game races
 * @since 1.0.0
 *
 * @param int $post_id Post ID.
 * @return array List of post terms under game races.
 */
function get_game_races( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-races' );
}

/**
 * Get game classes
 * @since 1.0.0
 *
 * @param int $post_id Post ID.
 * @return array List of post terms under game classes.
 */
function get_game_classes( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-classes' );
}

/**
 * Get game roles
 * @since 1.0.0
 *
 * @param int $post_id Post ID.
 * @return array List of post terms under game roles.
 */
function get_game_roles( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-roles' );
}

/**
 * Get game types
 * @since 1.0.0
 *
 * @param int $post_id Post ID.
 * @return array List of post terms under game types.
 */
function get_game_types( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-types' );
}

/**
 * Get game options
 * @since 1.0.0
 *
 * @return array|bool Array with all game options or FALSE if games_option was not set.
 */
function get_game_options() {
	return get_option( 'games_option' );
}

/**
 * Retrieves the setting for the game dedicated group option.
 * @since 1.1.0
 *
 * @return string|null Slug for the dedicated setting option or null.
 */
function get_game_dedicated_setting() {
	$option = get_game_options();

	if( !empty( $option['dedicated'] ) )
		return $option['dedicated']; 
	else return null; 
}

/**
 * Retrieves the setting for the game semi-dedicated group option.
 * @since 1.1.0
 *
 * @return string|null Slug for the semi-dedicated setting option or null.
 */
function get_game_semidedicated_setting() {
	$option = get_game_options();

	if( !empty( $option['semi_dedicated'] ) )
		return $option['semi_dedicated'];
	else return null;
}

/**
 * Get a specified game race term permalink.
 * @since 1.0.0
 * 
 * @param int|WP_Term $term The term ID or the term object.
 */
function get_game_races_permalink( $term ) {
	get_term_permalink( $term, 'game-races' );
}

/**
 * Get a specified game class term permalink.
 * @since 1.0.0
 * 
 * @param int|WP_Term $term The term ID or the term object.
 */
function get_game_classes_permalink( $term ) {
	get_term_permalink( $term, 'game-classes' );
}

/**
 * Get a specified game role term permalink.
 * @since 1.0.0
 * 
 * @param int|WP_Term $term The term ID or the term object.
 */
function get_game_roles_permalink( $term ) {
	get_term_permalink( $term, 'game-roles' );
}

/**
 * Get a specified game type term permalink.
 * @since 1.0.0
 * 
 * @param int|WP_Term $term The term ID or the term object.
 */
function get_game_types_permalink( $term ) {
	get_term_permalink( $term, 'game-types' );
}
?>