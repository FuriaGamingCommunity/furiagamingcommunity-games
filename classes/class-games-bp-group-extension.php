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
if ( class_exists( 'BP_Group_Extension' ) && !class_exists('Group_Extension_Games') ) :
  
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

			// Get group game meta and game list
			$group_game = groups_get_groupmeta( $group_id, 'group-game' );
			$games = get_games();
			?>
			
			<?php if ( !$group_game ) : ?>
			
			<p><?php _e( 'This group is not set to any game.', 'furiagamingcommunity_games' ); ?></p>
			
			<?php else : ?>
				
			<?php $game = get_game_by_slug( $group_game ); ?>
			
			<p><?php printf( __( 'This group is set to players from <a href="%1$s">%2$s</a>.', 'furiagamingcommunity_games' ), get_permalink( $game ), $game->post_title ); ?><p>
			
			<?php endif; ?>

			<?php
		}
	 
		/**
		 * settings_screen() is the catch-all method for displaying the content 
		 * of the edit, create, and Dashboard admin panels
		 */
		function settings_screen( $group_id = NULL ) {

			// Get group game meta and game list
			$group_game = groups_get_groupmeta( $group_id, 'group-game' );
			$games = get_games();
			?>

			<?php if ( empty( $games ) ) : ?>
			<div id="message" class="info">
				<p><?php _e('You need to set up some games before being able to assign them to any group.'); ?></p>
			</div>
			<?php endif; ?>
			
			<div>
				<label for="group-game"><?php _e('Group game', 'furiagamingcommunity_games');?></label>
				<select name="group-game" id="group-game" aria-required="true" <?php disabled( empty( $games ), true ); ?> >
					<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
					<?php if ( !empty( $games ) ) : foreach( $games as $game ) :	?>
					<option value="<?php echo strtolower( $game->post_name ); ?>" id="<?php echo 'game-' . $game->ID; ?>" <?php selected( $group_game, $game->post_name ); ?> ><?php echo $game->post_title; ?></option>
					<?php endforeach; endif; ?>
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
	
	if ( is_admin() && !class_exists( 'BP_Group_Extension' ) )
		furiagamingcommunity_games_notices( _e('BuddyPress Group Extension not found!', 'furiagamingcommunity_games'), 'warning' );
endif;
?>
