<?php
/**
 * Plugin Game Class.
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists('Games') ) :
/**
 * Registers the "Game" custom post type.
 * Games contain several taxonomies to display related in-game characters features.
 *
 * @author Xavier Giménez
 * @version 1.1.1
 */
class Games {

	/**
	 * Holds the values to be used in callbacks
	 */
	private $options;

	/**
	 * Build the class.
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->setup_actions();
	}

	/**
	 * Register the custom post and taxonomy with WordPress
	 * @since 1.0.0
	 */
	public function setup_actions() {

		// Object registration actions
		add_action( 'init', 							array( $this, 'register_games' ) );
		add_action( 'init', 							array( $this, 'register_races' ) );
		add_action( 'init', 							array( $this, 'register_classes' ) );
		add_action( 'init', 							array( $this, 'register_roles' ) );
		add_action( 'init', 							array( $this, 'register_types' ) );
		add_action( 'init', 							array( $this, 'register_settings' ) );

		// User profile actions
		add_action( 'show_user_profile', 				array( $this, 'add_game_show_user_profile' ) );
		add_action( 'edit_user_profile', 				array( $this, 'add_game_edit_user_profile' ) );
		add_action( 'personal_options_update', 			array( $this, 'save_game_personal_options_update' ) );
		add_action( 'edit_user_profile_update', 		array( $this, 'save_game_edit_user_profile_update' ) );

		add_action( 'wp_ajax_nopriv_add_game_terms', 	array( $this, 'ajax_get_terms' ) );
		add_action( 'wp_ajax_post_add_game_terms', 		array( $this, 'ajax_get_terms' ) );
		

		// Admin-only methods
		if ( is_admin() ) {

			// Admin filters
			add_filter( 'post_updated_messages', 		array( $this, 'update_messages' ) );
			add_filter( 'manage_edit-game_columns', 	array( $this, 'edit_game_columns' )	);
			
			// Admin actions
			add_action( 'save_post', 					array( $this, 'save_game' ), 				10, 2 );
			add_action( 'edited_race', 					array( $this, 'save_race' ), 				10, 2 );
			add_action( 'create_race', 					array( $this, 'save_race' ), 				10, 2 );
			add_action( 'edited_class', 				array( $this, 'save_class' ), 				10, 2 );  
			add_action( 'create_class', 				array( $this, 'save_class' ), 				10, 2 );
			add_action( 'edited_role', 					array( $this, 'save_role' ), 				10, 2 );
			add_action( 'create_role', 					array( $this, 'save_role' ), 				10, 2 );
			add_action( 'edited_type', 					array( $this, 'save_type' ), 				10, 2 );
			add_action( 'create_type', 					array( $this, 'save_type' ), 				10, 2 );
			add_action( 'admin_menu', 					array( $this, 'remove_game_meta_box' ), 	10, 2 );
			add_action( 'admin_menu', 					array( $this, 'add_plugin_page' ), 			10, 2 );
			add_action( 'admin_init', 					array( $this, 'page_init' ), 				10, 2 );
			add_action( 'add_meta_boxes', 				array( $this, 'add_game_meta_box' ), 		10, 2 );
			add_action( 'manage_posts_custom_column', 	array( $this, 'manage_game_columns' ), 		10, 2 );

			// Enqueue scripts
			add_action( 'admin_enqueue_scripts', 		array( $this, 'enqueue_game_scripts' ) );
		}

		// Add notification filters
		add_filter( 'bp_notifications_get_registered_components', 	array( $this, 'register_notification' ), 	9, 1 );
		add_filter( 'bp_notifications_get_notifications_for_user', 	array( $this, 'format_notification' ), 		9, 5 );
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
			'supports'				=> array( 'title', 'editor', 'thumbnail', 'excerpt' ),
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
	 * Filters the default meta boxes.
	 * @since 1.1.0
	 * 
	 */
	public function remove_game_meta_box() {

		// Remove default featured image box.
		remove_meta_box( 'postimagediv', 'game', 'side' );
	}

	/**
	 * Adds the game custom meta boxes.
	 * @since 1.1.0
	 * 
	 */
	public function add_game_meta_box() {
		
		// Add our customized featured image box.
		add_meta_box( 'postimagediv', __('Game Logo', 'furiagamingcommunity_games'), 'post_thumbnail_meta_box' , 'game', 'normal', 'high' );

		// Add the Game URL meta box.
		add_meta_box( 'posturldiv', __( 'Game URL', 'furiagamingcommunity_games' ),	array( $this, 'game_url_meta_box' ), 'game', 'normal', 'high'	);
	}

	/**
	 * Render game url meta box content.
	 * @since 1.1.0
	 *
	 * @param WP_Post $post The post object.
	 */
	public function game_url_meta_box( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'game_url_meta_box', 'game_url_meta_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$url_value = get_post_meta( $post->ID, 'game_url', true );

		// Display the form, using the current value.
		?>
		<input type="url" id="game_url" name="game_url" value="<?php echo esc_url( $url_value ); ?>" size="50" />
		<?php
	}

	/**
	 * Save or update a new game.
	 * @since 1.0.0
	 */
	public function save_game( $post_id , $post = '' ) {
		
		// Don't do anything if it's not a game
		if ( 'game' != $post->post_type ) return;

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['game_url_meta_box_nonce'] ) ) {
        	return $post_id;
        }

        $nonce = $_POST['game_url_meta_box_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'game_url_meta_box' ) ) {
        	return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        	return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
        	if ( ! current_user_can( 'edit_page', $post_id ) ) {
        		return $post_id;
        	}
        } else {
        	if ( ! current_user_can( 'edit_post', $post_id ) ) {
        		return $post_id;
        	}
        }

        /* OK, it's safe for us to save the data now. */

        // Sanitize the user input.
        $mydata = sanitize_text_field( $_POST['game_url'] );

        // Update the meta field.
        update_post_meta( $post_id, 'game_url', $mydata );
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
	 * @since 1.0.0
	 */
	public function save_type( $term_id ) {
		
		$term_meta 	= get_option( "taxonomy_$term_id" );
		
		// Otherwise, if it had a value, remove it
		if ( !empty( $term_meta ) )
			delete_option( "taxonomy_$term_id" );
	}

	/**
	 * Register games as a valid notification type
	 * @since 1.0.0
	 */	
	public function register_notification( $names ) {
		$names[] = 'games';
		return $names;
	}
	
	/**
	 * Format the text for game events notifications
	 * @since 1.0.0
	 */		
	public function format_notification( $action, $item_id, $secondary_item_id, $total_items , $format = 'string' ) {
		return $action;
	}

	/**
	 * Edits the display of the game list columns
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
	 * Manages the display of the game list custom columns
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

	///////////////////
	// Game Settings //
	///////////////////

	/**
	 * Add options page
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 */
	public function page_init() {        
		register_setting(
			'games_group', // Option group
			'games_option', // Option name
			array( $this, 'sanitize' ) // Sanitize
			);

		add_settings_section(
			'games_group_types', // ID
			__('Game Type Settings', 'furiagamingcommunity_games'), // Title
			array( $this, 'games_group_types_info' ), // Callback
			'games-admin' // Page
			);  

		add_settings_field(
			'dedicated', // ID
			__('Dedicated', 'furiagamingcommunity_games'), // Title 
			array( $this, 'dedicated_callback' ), // Callback
			'games-admin', // Page
			'games_group_types' // Section
			);      

		add_settings_field(
			'semi_dedicated', // ID
			__('Semi-dedicated', 'furiagamingcommunity_games'), // Title 
			array( $this, 'semi_dedicated_callback' ), // Callback
			'games-admin', // Page
			'games_group_types' // Section
			);      
	}

	/**
	 * Sanitize each setting field as needed
	 * @since 1.0.0
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if( isset( $input['dedicated'] ) )
			$new_input['dedicated'] = sanitize_text_field( $input['dedicated'] );

		if( isset( $input['semi_dedicated'] ) )
			$new_input['semi_dedicated'] = sanitize_text_field( $input['semi_dedicated'] );

		return $new_input;
	}

	/** 
	 * Print the Section text
	 * @since 1.0.0
	 */
	public function games_group_types_info() {
		_e('Sets the selected game type as the default <strong>dedicated</strong> or <strong>semi-dedicated</strong> nomenclature for game groups.', 'furiagamingcommunity_games');
	}

	/** 
	 * Get the settings option array and print one of its values
	 * @since 1.0.0
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
	 * @since 1.0.0
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

	//////////////////
	// User Profile //
	//////////////////

	/**
	 * Adds a selectable area in the user profile to set the game, character and role that he prefers.
	 * @param WP_User $user 
	 */
	private function add_game_user_profile( $user ) {

		// Get all the games.
		$games 		= get_games();
		?>

		<?php if(!$games): ?>

		<?php endif; ?>
		
		<h2><?php _e('About your preferred game and character', 'furiagamingcommunity_games'); ?></h2>
		<table class="form-table">
			<tr>
				<th><label for="user_game"><?php _e('Game', 'furiagamingcommunity_games'); ?></label></th>
				<td>
					<select name="user_game" id="user_game">
						<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
						<?php foreach( $games as $game ): ?>
						<option value="<?php echo $game->ID; ?>" <?php if( get_the_author_meta( 'user_game', $user->ID ) ) selected( esc_attr( get_the_author_meta( 'user_game', $user->ID ) ), $game->ID ); ?>><?php echo $game->post_title; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="user_game_race"><?php _e('Race', 'furiagamingcommunity_games'); ?></label></th>
				<td>
					<select name="user_game_race" id="user_game_race">
						<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="user_game_class"><?php _e('Class', 'furiagamingcommunity_games'); ?></label></th>
				<td>
					<select name="user_game_class" id="user_game_class">
						<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="user_game_role"><?php _e('Role', 'furiagamingcommunity_games'); ?></label></th>
				<td>
					<select name="user_game_role" id="user_game_role">
						<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Saves the selectable game information to the user profile.
	 * @param  Integer $user_id The user ID
	 */
	private function save_game_user_profile( $user_id ) {
		update_user_meta( $user_id,'user_game', sanitize_text_field( $_POST['user_game'] ) );
	}

	/**
	 * add_user_profile alias.
	 * @since 1.1.0
	 */
	public function add_game_edit_user_profile( $user ) {
		return $this->add_game_user_profile( $user);
	}

	/**
	 * add_user_profile alias.
	 * @since 1.1.0
	 */
	public function add_game_show_user_profile( $user ) {
		return $this->add_game_user_profile( $user );
	}

	/**
	 * save_games_user_profile alias.
	 * @since 1.1.0
	 */
	public function save_game_personal_options_update( $user_id ) {
		return $this->save_game_user_profile( $user_id );
	}

	/**
	 * save_games_user_profile alias.
	 * @since 1.1.0
	 */
	public function save_game_edit_user_profile_update( $user_id ) {
		return $this->save_game_user_profile( $user_id );
	}

	/**
	 * Enqueue custom scripts.
	 * @since 1.1.1
	 */
	public function enqueue_game_scripts() {

		$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		);

		var_dump($translation_array);

		wp_enqueue_script( 'games', FGC_G_PLUGIN_URL . '/js/games.js', array('jquery'), '1.0', true );
		wp_localize_script( 'games', 'games', $translation_array );
	}

	/**
	 * Ajax helper function to get current game terms.
	 * @since 1.2.0
	 */
	public function ajax_get_terms() {
		$terms = get_game_terms( $this->ID );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo $terms;
		}

		die();
	}

} // class Games

endif;
?>