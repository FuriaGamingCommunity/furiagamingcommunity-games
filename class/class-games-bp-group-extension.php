<?php
/**
 * Furia Gaming Community BuddyPress Group Extension Game Class.
 *
 * @uses BP_Group_Extension
 * @since 1.0.1
 * @todo Fill the class with games data instead of the default placeholder.
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :
  
	class Group_Extension_Games extends BP_Group_Extension {
		/**
		 * Your __construct() method will contain configuration options for 
		 * your extension, and will pass them to parent::init()
		 */
		function __construct() {
			$args = array(
				'slug' => 'group-extension-games',
				'name' => __('Games', 'furiagamingcommunity_games'),
			);
			parent::init( $args );
		}
	 
		/**
		 * display() contains the markup that will be displayed on the main 
		 * plugin tab
		 */
		function display( $group_id = NULL ) {
			$group_id = bp_get_group_id();
			echo 'What a cool plugin!';
		}
	 
		/**
		 * settings_screen() is the catch-all method for displaying the content 
		 * of the edit, create, and Dashboard admin panels
		 */
		function settings_screen( $group_id = NULL ) {

			// Get the meta.
			$setting = groups_get_groupmeta( $group_id, 'group-game' );
			// Get all games.
			$games = get_games();
			?>

			<?php if ( !$games ) : ?>
			<div id="message" class="info">
				<p><?php _e('You need to set up some games before being able to assign them to any group.'); ?></p>
			</div>
			<?php endif; ?>
			
			<div>
				<label for="group-game"><?php _e('Group game', 'furiagamingcommunity_games');?></label>
				<select name="group-game" id="group-game" aria-required="true" <?php disabled( $games, '' ); ?> >
					<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
					<?php
						print_r($games);
					?>
				</select>
			</div>
			<?php
		}
	 
		/**
		 * settings_screen_save() contains the catch-all logic for saving 
		 * settings from the edit, create, and Dashboard admin panels
		 */
		function settings_screen_save( $group_id = NULL ) {
			$setting = '';
	 
			if ( isset( $_POST['group-game'] ) ) {
				$setting = $_POST['group-game'];
			}
	 
			groups_update_groupmeta( $group_id, 'group-game', $setting );
		}
	}
	bp_register_group_extension( 'Group_Extension_Games' );

else:
	if ( is_admin() )
		furiagamingcommunity_games_notices( _e('BuddyPress Group Extension not found!', 'furiagamingcommunity_games'), 'warning' );
endif;
?>
