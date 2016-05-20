<?php
/**
 * Furia Gaming Community Game Class.
 *
 * @since 1.0.2
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Game" custom post type.
 * Games contain several taxonomies to display related in-game characters features.
 *
 * @author Xavier Giménez Segovia
 * @version 1.1.0
 */
if ( !class_exists('Games') ) :

class Games {

	/**
	 * Holds the values to be used in callbacks
	 */
	private $options;

	/**
	 * Build the class.
	 * @since 1.0.1
	 */
	public function __construct() {

		$this->setup_actions();
	}

	/**
	 * Register the custom post and taxonomy with WordPress
	 * @since 1.0.0
	 */
	public function setup_actions() {

		// Add universal actions
		add_action( 'init'	, array( $this , 'register_games' ) 	);
		add_action( 'init'	, array( $this , 'register_races' ) 	);
		add_action( 'init'	, array( $this , 'register_classes' ) 	);
		add_action( 'init'	, array( $this , 'register_roles' ) 	);
		add_action( 'init'	, array( $this , 'register_types' ) 	);
		add_action( 'init'	, array( $this , 'register_settings' ) 	);
		
		// Add universal filters
		add_filter( 'bp_notifications_get_registered_components'	, array( $this , 'register_notification' ) 	, 9 , 1 );
		add_filter( 'bp_notifications_get_notifications_for_user'	, array( $this , 'format_notification' ) 	, 9 , 5 );

		// Admin-only methods
		if ( is_admin() ) {

			// Admin Filters
			add_filter( 'post_updated_messages'		, array( $this , 'update_messages' 	)	);
			add_filter( 'manage_edit-game_columns'	, array( $this , 'edit_game_columns' )	);
			
			// Admin Actions
			add_action( 'save_post'							, array( $this , 'save_game' )				, 10, 2 );
			add_action( 'edited_race'						, array( $this , 'save_race' )				, 10, 2 );
			add_action( 'create_race'						, array( $this , 'save_race' )				, 10, 2 );
			add_action( 'edited_class'						, array( $this , 'save_class' )				, 10, 2 );  
			add_action( 'create_class'						, array( $this , 'save_class' )				, 10, 2 );
			add_action( 'edited_role'						, array( $this , 'save_role' )				, 10, 2 );
			add_action( 'create_role'						, array( $this , 'save_role' )				, 10, 2 );
			add_action( 'edited_type'						, array( $this , 'save_type' )				, 10, 2 );
			add_action( 'create_type'						, array( $this , 'save_type' )				, 10, 2 );
			add_action( 'manage_game_posts_custom_column'	, array( $this , 'manage_game_columns' )	, 10, 2	);

			// Settings
			add_action( 'admin_menu'						, array( $this, 'add_plugin_page' ) 				);
			add_action( 'admin_init'						, array( $this, 'page_init' ) 						);
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
			'not_found'				=> __('No games found', 'furiagamingcommunity_games'),
			'not_found_in_trash'	=> __('No games found in Trash', 'furiagamingcommunity_games'), 
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
			'taxonomies'			=> array( 'game-races' , 'game-classes', 'game-roles', 'game-types' ),
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
		$role_tax_labels = array(			
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
		
		$role_tax_caps = array(
			'manage_terms'	=> 'manage_categories',
			'edit_terms'	=> 'manage_categories',
			'delete_terms'	=> 'manage_categories',
			'assign_terms'	=> 'edit_posts'
			);
		
		$role_tax_args = array(
			'labels'				=> $role_tax_labels,
			'public'				=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_tagcloud'			=> false,
			'hierarchical'			=> false,
			'rewrite'				=> array( 'slug' => 'role' ),
			'capabilities'    	  	=> $role_tax_caps,
			);		

		/* Register the Class post taxonomy! */
		register_taxonomy( 'game-roles', 'game', $role_tax_args );
	}

	/**
	 * Register a Types taxonomy for Games
	 * @since 1.0.0
	 */
	public function register_types() {
		
		/* Classes */
		$type_tax_labels = array(			
			'name'							=> __('Types', 'furiagamingcommunity_games'),
			'singular_name'					=> __('Type', 'furiagamingcommunity_games'),
			'search_items'					=> __('Search Types', 'furiagamingcommunity_games'),
			'popular_items'					=> __('Popular Types', 'furiagamingcommunity_games'),
			'all_items'						=> __('All Types', 'furiagamingcommunity_games'),
			'edit_item'						=> __('Edit Types', 'furiagamingcommunity_games'),
			'update_item'					=> __('Update Types', 'furiagamingcommunity_games'),
			'add_new'						=> __('New Type', 'furiagamingcommunity_games'),
			'add_new_item'					=> __('Add New Type', 'furiagamingcommunity_games'),
			'new_item_name'					=> __('New Type Name', 'furiagamingcommunity_games'),
			'menu_name'						=> __('Types', 'furiagamingcommunity_games'),
			'separate_items_with_commas'	=> __('Separate types with commas', 'furiagamingcommunity_games'),
			'choose_from_most_used'			=> __('Choose from the most used types', 'furiagamingcommunity_games'),
			);
		
		$type_tax_caps = array(
			'manage_terms'	=> 'manage_categories',
			'edit_terms'	=> 'manage_categories',
			'delete_terms'	=> 'manage_categories',
			'assign_terms'	=> 'edit_posts'
			);
		
		$type_tax_args = array(
			'labels'				=> $type_tax_labels,
			'public'				=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_tagcloud'			=> false,
			'hierarchical'			=> true,
			'rewrite'				=> array( 'slug' => 'type' ),
			'capabilities'    	  	=> $type_tax_caps,
			);		

		/* Register the Class post taxonomy! */
		register_taxonomy( 'game-types', 'game', $type_tax_args );
	}

	/**
	 * Add default values for game settings.
	 * @since 1.0.0
	 */
	public function register_settings() {
		
		if ( !isset($this->options['dedicated']) )
			$this->options['dedicated'] = '';
		if ( !isset($this->options['semi_dedicated']) )
			$this->options['semi_dedicated'] = '';
	}

	/**
	 * Customize backend messages when an event is updated.
	 * @since 1.0.0
	 */
	public function update_messages( $game_messages ) {
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
	 * Save custom role taxonomy.
	 * @since 1.0.0
	 */
	public function save_role( $term_id ) {
		
		$term_meta 	= get_option( "taxonomy_$term_id" );
		
		// Otherwise, if it had a value, remove it
		if ( !empty( $term_meta ) )
			delete_option( "taxonomy_$term_id" );
	}

	/**
	 * Save custom type taxonomy.
	 * @since 1.1.0
	 */
	public function save_type( $term_id ) {
		
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

	/**
	 * Adds the slide featured image and link to the slides page
	 * @since 1.0.0
	 */
	public function edit_game_columns( $columns ) {
		$columns = array(		
			'cb'			=> '<input type="checkbox" />',
			'title'			=> __( 'Game Title', 'furiagamingcommunity_games' ),
			'type'			=> __( 'Type', 'furiagamingcommunity_games' ),
			'date'			=> __( 'Date', 'furiagamingcommunity_games' )
			);
		return $columns; 
	}
	
	/**
	 * Adds content to the custom column format
	 * @since 1.0.0
	 */
	public function manage_game_columns( $columns ) {
		
		global $post;
		
		switch ( $columns ) {
			
			case 'type' :
				// Get the types for the post.
				$terms = get_the_terms( $post->ID, 'game-types' );

				// If terms were found.
				if ( !empty( $terms ) ) {

					$out = array();

					// Loop through each term, linking to the 'edit posts' page for the specific term.
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'type' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'type', 'display' ) )
						);
					}

					// Join the terms, separating them with a comma.
					echo join( ', ', $out );
				}

				// If no terms were found, output a default message.
				else {
					_e( 'None', 'furiagamingcommunity_games' );
				}
			break;
		}
	}

	/**
	 * Game Settings
	 */

	/**
	 * Add options page
	 * @since 1.1.0
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			__('Games Settings', 'furiagamingcommunity_games'), 
			__('Games', 'furiagamingcommunity_games'), 
			'manage_options', 
			'games-admin', 
			array( $this, 'create_admin_page' )
			);
	}

	/**
	 * Options page callback
	 * @since 1.1.0
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'games_option' );
		?>
		<div class="wrap">
			<h2><?php _e('Games Settings', 'furiagamingcommunity_games'); ?></h2>
			<p><?php _e('Here you can change the default settings for any game.', 'furiagamingcommunity_games'); ?></p>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'games_group' );   
				do_settings_sections( 'games-admin' );
				submit_button(); 
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 * @since 1.1.0
	 */
	public function page_init() {        
		register_setting(
			'games_group', // Option group
			'games_option', // Option name
			array( $this, 'sanitize' ) // Sanitize
			);

		add_settings_section(
			'games_dedicated_groups', // ID
			__('Game Type Settings', 'furiagamingcommunity_games'), // Title
			array( $this, 'games_dedicated_groups_info' ), // Callback
			'games-admin' // Page
			);  

		add_settings_field(
			'dedicated', // ID
			__('Dedicated', 'furiagamingcommunity_games'), // Title 
			array( $this, 'dedicated_callback' ), // Callback
			'games-admin', // Page
			'games_dedicated_groups' // Section
			);      

		add_settings_field(
			'semi_dedicated', // ID
			__('Semi-dedicated', 'furiagamingcommunity_games'), // Title 
			array( $this, 'semi_dedicated_callback' ), // Callback
			'games-admin', // Page
			'games_dedicated_groups' // Section
			);      
	}

	/**
	 * Sanitize each setting field as needed
	 * @since 1.1.0
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if( isset( $input['dedicated'] ) )
			$new_input['dedicated'] = absint( $input['dedicated'] );

		if( isset( $input['semi_dedicated'] ) )
			$new_input['semi_dedicated'] = absint( $input['semi_dedicated'] );

		return $new_input;
	}

	/** 
	 * Print the Section text
	 * @since 1.1.0
	 */
	public function games_dedicated_groups_info() {
		_e('Sets the selected game type as the default <strong>dedicated</strong> or <strong>semi-dedicated</strong> nomenclature for game groups.', 'furiagamingcommunity_games');
	}

	/** 
	 * Get the settings option array and print one of its values
	 * @since 1.1.0
	 */
	public function dedicated_callback() {
		
		// Get all game types
		$types = get_terms_types();
		?>
		<select name="games_option[dedicated]" id="dedicated" aria-required="true" <?php disabled( empty( $types ), true ); ?> >
			<option value="" default><?php _e( 'None', 'furiagamingcommunity_games' ); ?></option>
			<?php if ( !empty( $types ) ) : foreach( $types as $type ) : ?>
				<option value="<?php echo strtolower( $type->slug ); ?>" id="<?php echo 'game-type-' . $type->term_id; ?>" <?php selected( $this->options['dedicated'], $type->slug ); ?> ><?php echo $type->name; ?></option>
			<?php endforeach; endif; ?>
		</select>
		<?php
	}

	/** 
	 * Get the settings option array and print one of its values
	 * @since 1.1.0
	 */
	public function semi_dedicated_callback() {
		
		// Get all game types
		$types = get_terms_types();
		?>
		<select name="games_option[semi_dedicated]" id="semi_dedicated" aria-required="true" <?php disabled( empty( $types ), true ); ?> >
			<option value="" default><?php _e( 'None', 'furiagamingcommunity_games' ); ?></option>
			<?php if ( !empty( $types ) ) : foreach( $types as $type ) : ?>
				<option value="<?php echo strtolower( $type->slug ); ?>" id="<?php echo 'game-type-' . $type->term_id; ?>" <?php selected( $this->options['semi_dedicated'], $type->slug ); ?> ><?php echo $type->name; ?></option>
			<?php endforeach; endif; ?>
		</select>
		<?php
	}
	
} // class Games

endif;
?>