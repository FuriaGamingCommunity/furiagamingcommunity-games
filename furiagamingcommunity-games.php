<?php
/**
 * @author Xavier Giménez Segovia <xavier.gimenez.segovia@gmail.com>
 * @license GPL-2.0+
 * 
 * Plugin Name: Furia Gaming Community - Games
 * Plugin URI: https://github.com/nottu2584/furiagamingcommunity-games
 * Description: Sets a new post type named slides and adds a custom widget to display them into slideshows.
 * Author: Xavier Giménez Segovia
 * Version: 1.0.0
 * Author URI: https://es.linkedin.com/pub/javier-gimenez-segovia/
 * Author Email: xavier.gimenez.segovia@gmail.com
 * Text Domain: furiagamingcommunity_games
**/

defined( 'ABSPATH' ) or die( __( 'No script kiddies please!', 'furiagamingcommunity_games' ) );

/**
 * Registers the "Game" custom post type.
 * Games contain several taxonomies to display related in-game characters features.
 *
 * @author Xavier Giménez Segovia
 * @version 1.0.0
 */
class FuriaGamingCommunity_Games {

	/**
	 * Register the custom post and taxonomy with WordPress on init
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add universal actions
		add_action( 'init'							, array( $this , 'register_games' 			) );
		add_action( 'init'							, array( $this , 'register_races' 			) );
		add_action( 'init'							, array( $this , 'register_classes' 		) );
		add_action( 'init'							, array( $this , 'register_roles' 			) );
		
		// Add universal filters
		add_filter( 'bp_notifications_get_registered_components' 	, array( $this , 'register_notification' ) 	, 9 , 1 );
		add_filter( 'bp_notifications_get_notifications_for_user' 	, array( $this , 'format_notification' ) 	, 9 , 5 );

		// Admin-only methods
		if ( is_admin() ) {

			// Admin Actions
			add_action( 'save_post'					, array( $this , 'save_game' )	, 10, 2 );

			add_action( 'edited_race'				, array( $this , 'save_race' ) , 10, 2 );
			add_action( 'create_race'				, array( $this , 'save_race' ), 10, 2 );
			add_action( 'edited_class'				, array( $this , 'save_class' ) , 10, 2 );  
			add_action( 'create_class'				, array( $this , 'save_class' ), 10, 2 );
			add_action( 'edited_role'				, array( $this , 'save_roles' ) , 10, 2 );
			add_action( 'create_role'				, array( $this , 'save_roles' ), 10, 2 );
				
			// Admin Filters
			add_filter( 'post_updated_messages'		, array( $this , 'update_messages') );
		}
	}
	
	/**
	 * Register a custom post type for Games
	 * @version 1.0.0
	 */
	public function register_games() {

		// Labels for the backend Game publisher
		$game_labels = array(
			'name'					=> __('Games', 'furiagamingcommunity_games'),
			'singular_name'			=> __('Game', 'furiagamingcommunity_games'),
			'add_new'				=> __('Add new', 'furiagamingcommunity_games'),
			'add_new_item'			=> __('Add new game', 'furiagamingcommunity_games'),
			'edit_item'				=> __('Edit game', 'furiagamingcommunity_games'),
			'new_item'				=> __('New game', 'furiagamingcommunity_games'),
			'view_item'				=> __('View game', 'furiagamingcommunity_games'),
			'search_items'			=> __('Search games', 'furiagamingcommunity_games'),
			'not_found'				=> __('No events found', 'furiagamingcommunity_games'),
			'not_found_in_trash'	=> __('No events found in Trash', 'furiagamingcommunity_games'), 
			'parent_item_colon'		=> '',
			'menu_name'				=> __('Games', 'furiagamingcommunity_games'),
			'all_items'				=> __('All games', 'furiagamingcommunity_games')
			);
		
		$game_capabilities = array(
			'edit_post'				=> 'edit_post',
			'edit_posts'			=> 'edit_posts',
			'edit_others_posts'		=> 'edit_others_posts',
			'publish_posts'			=> 'publish_posts',
			'read_post'				=> 'read_post',
			'read_private_posts'	=> 'read_private_posts',
			'delete_post'			=> 'delete_post'
			);			
		
		// Construct the arguments for our custom slide post type
		$game_args = array(
			'labels'				=> $game_labels,
			'description'			=> __('Custom games played by the community', 'furiagamingcommunity_games'),
			'public'				=> true,
			'publicly_queryable'	=> true,
			'exclude_from_search'	=> true,
			'show_ui'				=> true,
			'show_in_menu'			=> true,
			'show_in_nav_menus'		=> false,
			'menu_icon'				=> 'dashicons-video-alt3',
			'capabilities'			=> $game_capabilities,
			'map_meta_cap'			=> true,
			'hierarchical'			=> false,
			'supports'				=> array( 'title', 'editor' ),
			'taxonomies'			=> array( 'game-races' , 'game-classes' ),
			'has_archive'			=> false,
			'rewrite'				=> array(
				'slug' 	=> 'game',
				'feeds'	=> false,
				'pages'	=> false,
				),
			'query_var'				=> true,
			'can_export'			=> true,
			);

		
		// Register the Game post type!
		register_post_type( 'game', $game_args );
	}

	/**
	 * Register a Races taxonomy for Games
	 * @since 1.0.0
	 */
	public function register_races() {
		
		/* Races */
		$race_tax_labels = array(			
			'name'							=> __('Races', 'furiagamingcommunity_games'),
			'singular_name'					=> __('Race', 'furiagamingcommunity_games'),
			'search_items'					=> __('Search Races', 'furiagamingcommunity_games'),
			'popular_items'					=> __('Popular Races', 'furiagamingcommunity_games'),
			'all_items'						=> __('All Races', 'furiagamingcommunity_games'),
			'edit_item'						=> __('Edit Race', 'furiagamingcommunity_games'),
			'update_item'					=> __('Update Race', 'furiagamingcommunity_games'),
			'add_new'						=> __('New Race', 'furiagamingcommunity_games'),
			'add_new_item'					=> __('Add New Race', 'furiagamingcommunity_games'),
			'new_item_name'					=> __('New Race Name', 'furiagamingcommunity_games'),
			'menu_name'						=> __('Races', 'furiagamingcommunity_games'),
			'separate_items_with_commas'	=> __('Separate races with commas', 'furiagamingcommunity_games'),
			'choose_from_most_used'			=> __('Choose from the most used races', 'furiagamingcommunity_games'),
			);
		
		$race_tax_caps = array(
			'manage_terms'	=> 'manage_categories',
			'edit_terms'	=> 'manage_categories',
			'delete_terms'	=> 'manage_categories',
			'assign_terms'	=> 'edit_posts'
			);
		
		$race_tax_args = array(
			'labels'				=> $race_tax_labels,
			'public'				=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_tagcloud'			=> false,
			'hierarchical'			=> false,
			'rewrite'				=> array( 'slug' => 'race' ),
			'capabilities'    	  	=> $race_tax_caps,
			);		

		/* Register the Race post taxonomy! */
		register_taxonomy( 'game-races', 'game', $race_tax_args );
	}

	/**
	 * Register a Classes taxonomy for Games
	 * @since 1.0.0
	 */
	public function register_classes() {
		
		/* Classes */
		$class_tax_labels = array(			
			'name'							=> __('Classes', 'furiagamingcommunity_games'),
			'singular_name'					=> __('Class', 'furiagamingcommunity_games'),
			'search_items'					=> __('Search Classes', 'furiagamingcommunity_games'),
			'popular_items'					=> __('Popular Classes', 'furiagamingcommunity_games'),
			'all_items'						=> __('All Classes', 'furiagamingcommunity_games'),
			'edit_item'						=> __('Edit Classes', 'furiagamingcommunity_games'),
			'update_item'					=> __('Update Classes', 'furiagamingcommunity_games'),
			'add_new'						=> __('New Class', 'furiagamingcommunity_games'),
			'add_new_item'					=> __('Add New Class', 'furiagamingcommunity_games'),
			'new_item_name'					=> __('New Class Name', 'furiagamingcommunity_games'),
			'menu_name'						=> __('Classes', 'furiagamingcommunity_games'),
			'separate_items_with_commas'	=> __('Separate classes with commas', 'furiagamingcommunity_games'),
			'choose_from_most_used'			=> __('Choose from the most used classes', 'furiagamingcommunity_games'),
			);
		
		$class_tax_caps = array(
			'manage_terms'	=> 'manage_categories',
			'edit_terms'	=> 'manage_categories',
			'delete_terms'	=> 'manage_categories',
			'assign_terms'	=> 'edit_posts'
			);
		
		$class_tax_args = array(
			'labels'				=> $class_tax_labels,
			'public'				=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_tagcloud'			=> false,
			'hierarchical'			=> false,
			'rewrite'				=> array( 'slug' => 'class' ),
			'capabilities'    	  	=> $class_tax_caps,
			);		

		/* Register the Class post taxonomy! */
		register_taxonomy( 'game-classes', 'game', $class_tax_args );
	}

	/**
	 * Register a Roles taxonomy for Games
	 * @since 1.0.0
	 */
	public function register_roles() {
		
		/* Classes */
		$class_tax_labels = array(			
			'name'							=> __('Roles', 'furiagamingcommunity_games'),
			'singular_name'					=> __('Role', 'furiagamingcommunity_games'),
			'search_items'					=> __('Search Roles', 'furiagamingcommunity_games'),
			'popular_items'					=> __('Popular Roles', 'furiagamingcommunity_games'),
			'all_items'						=> __('All Roles', 'furiagamingcommunity_games'),
			'edit_item'						=> __('Edit Roles', 'furiagamingcommunity_games'),
			'update_item'					=> __('Update Roles', 'furiagamingcommunity_games'),
			'add_new'						=> __('New Role', 'furiagamingcommunity_games'),
			'add_new_item'					=> __('Add New Role', 'furiagamingcommunity_games'),
			'new_item_name'					=> __('New Role Name', 'furiagamingcommunity_games'),
			'menu_name'						=> __('Roles', 'furiagamingcommunity_games'),
			'separate_items_with_commas'	=> __('Separate roles with commas', 'furiagamingcommunity_games'),
			'choose_from_most_used'			=> __('Choose from the most used roles', 'furiagamingcommunity_games'),
			);
		
		$class_tax_caps = array(
			'manage_terms'	=> 'manage_categories',
			'edit_terms'	=> 'manage_categories',
			'delete_terms'	=> 'manage_categories',
			'assign_terms'	=> 'edit_posts'
			);
		
		$class_tax_args = array(
			'labels'				=> $class_tax_labels,
			'public'				=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_tagcloud'			=> false,
			'hierarchical'			=> false,
			'rewrite'				=> array( 'slug' => 'role' ),
			'capabilities'    	  	=> $class_tax_caps,
			);		

		/* Register the Class post taxonomy! */
		register_taxonomy( 'game-roles', 'game', $class_tax_args );
	}

	/**
	 * Customize backend messages when an event is updated.
	 * @since 1.0.0
	 */
	private function update_messages( $game_messages ) {
		global $post, $post_ID;
		
		/* Set some simple messages for editing slides, no post previews needed. */
		$game_messages['game'] = array( 
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Game updated.', 'furiagamingcommunity_games' ),
			2  => __( 'Custom field updated.', 'furiagamingcommunity_games' ),
			3  => __( 'Custom field deleted.', 'furiagamingcommunity_games' ),
			4  => __( 'Game updated.', 'furiagamingcommunity_games' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Game restored to revision from %s', 'furiagamingcommunity_games' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Game published.', 'furiagamingcommunity_games' ),
			7  => __( 'Game saved.', 'furiagamingcommunity_games' ),
			8  => __( 'Game submitted.', 'furiagamingcommunity_games' ),
			9  => sprintf(
				__( 'Game scheduled for: <strong>%1$s</strong>.', 'furiagamingcommunity_games' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'j M Y @ G:i', 'furiagamingcommunity_games' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Game draft updated.', 'furiagamingcommunity_games' )
			);
		return $game_messages;
	}

	/**
	 * Save or update a new event
	 * @since 1.0.0
	 */
	public function save_game( $post_id , $post = '' ) {
		
		// Don't do anything if it's not a game
		if ( 'game' != $post->post_type ) return;
	}

	/**
	 * Save custom race taxonomy.
	 * @since 1.0.0
	 */
	public function save_race( $term_id ) {
		
		$term_meta 	= get_option( "taxonomy_$term_id" );
		
		// Otherwise, if it had a value, remove it
		if ( !empty( $term_meta ) )
			delete_option( "taxonomy_$term_id" );
	}

	/**
	 * Save custom class taxonomy.
	 * @since 1.0.0
	 */
	public function save_class( $term_id ) {
		
		$term_meta 	= get_option( "taxonomy_$term_id" );
		
		// Otherwise, if it had a value, remove it
		if ( !empty( $term_meta ) )
			delete_option( "taxonomy_$term_id" );
	}

	/**
	 * Save custom class taxonomy.
	 * @since 1.0.0
	 */
	public function save_role( $term_id ) {
		
		$term_meta 	= get_option( "taxonomy_$term_id" );
		
		// Otherwise, if it had a value, remove it
		if ( !empty( $term_meta ) )
			delete_option( "taxonomy_$term_id" );
	}

	/**
	 * Register "games" as a valid notification type
	 * @since 1.0.0
	 */	
	public function register_notification( $names ) {
		$names[] = 'games';
		return $names;
	}
	
	/**
	 * Format the text for race event notifications
	 * @since 1.0.0
	 */		
	public function format_notification( $action, $item_id, $secondary_item_id, $total_items , $format = 'string' ) {
		return $action;
	}
	
} // class FuriaGamingCommunity_Games
register_activation_hook( basename( __FILE__ ), new FuriaGamingCommunity_Games );

/**
 * Helper functions to set context on game pages
 * @since 1.0.0
*/
function is_game_race() {
	global $wp_query;
	if ( 'game-races' == $wp_query->queried_object->taxonomy )
		return true;
}
function is_game_class() {
	global $wp_query;
	if ( 'game-classes' == $wp_query->queried_object->taxonomy )
		return true;
}
function is_game_role() {
	global $wp_query;
	if ( 'game-roles' == $wp_query->queried_object->taxonomy )
		return true;
}

/**
 * Get all games
 * @since 1.0.0
*/
function get_games() {
	
	$args = array(
		'post_type' 		=> 'game',
		'post_status' 		=> 'publish'
		);

	return get_posts( $args );
}

/**
 * Get a single game by the slug
 * @since 1.0.0
*/
function get_game_by_slug( $slug ) {
	if ( '' != $slug ) {
		$args = array(
			'name'        => $slug,
			'post_type'   => 'game',
			'post_status' => 'publish',
			'numberposts' => 1
		);
		return get_posts($args);
	} else return false;
}

/**
 * Get game races
 * @since 1.0.0
*/
function get_game_races( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-races' );
}

/**
 * Get game classes
 * @since 1.0.0
*/
function get_game_classes( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-classes' );
}

/**
 * Get game roles
 * @since 1.0.0
*/
function get_game_roles( $post_id ) {
	return wp_get_post_terms( $post_id, 'game-roles' );
}

// Register the text domain.
function FuriaGamingCommunity_Games_load_textdomain() {
	load_plugin_textdomain( 'furiagamingcommunity_games', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'FuriaGamingCommunity_Games_load_textdomain');
?>