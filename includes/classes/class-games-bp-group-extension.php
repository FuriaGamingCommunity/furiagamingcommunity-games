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
if ( class_exists( 'BP_Group_Extension' ) && !class_exists('FuriaGamingCommunity_Games_BP_Group_Extension') ) :
  
	class FuriaGamingCommunity_Games_BP_Group_Extension extends BP_Group_Extension {
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
			$group_game_type = groups_get_groupmeta( $group_id, 'group-game-type' );

			$games = get_games();
			?>
			
			<?php if ( !$group_game ) : ?>
			
			<p><?php _e( 'This group is not set to any game.', 'furiagamingcommunity_games' ); ?></p>
			
			<?php else : ?>
				
			<?php $game = get_game_by_slug( $group_game ); ?>
			
			<p><?php printf( __( 'This group is set to players from <a href="%1$s">%2$s</a>.', 'furiagamingcommunity_games' ), get_permalink( $game ), $game->post_title ); ?><p>
			
			<?php endif; ?>

			<?php if ( $group_game_type ) : ?>

			<?php $type = get_term_type_by_slug( $group_game_type ); ?>

			<p><?php printf( __( 'This group is an official %1$s of %2$s.', 'furiagamingcommunity_games' ), $type->name, get_bloginfo('name') ); ?><p>

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
			$group_game_type = groups_get_groupmeta( $group_id, 'group-game-type' );

			$games = get_games();
			$types = get_terms_types();
			?>

			<?php if ( empty( $games ) ) : ?>
			<div id="message" class="info">
				<p><?php _e('You need to set up some games before being able to assign them to any group.', 'furiagamingcommunity_games'); ?></p>
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

			<?php if ( empty( $types ) ) : ?>
			<div id="message" class="info">
				<p><?php _e('You need to set up some game types before being able to assign them to any game group.', 'furiagamingcommunity_games'); ?></p>
			</div>
			<?php endif; ?>

			<div>
				<label for="group-game-type"><?php _e('Group type', 'furiagamingcommunity_games');?></label>
				<select name="group-game-type" id="group-game-type" aria-required="true" <?php disabled( empty( $types ), true ); ?> >
					<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
					<?php if ( !empty( $types ) ) : foreach( $types as $type ) :	?>
					<option value="<?php echo strtolower( $type->post_name ); ?>" id="<?php echo 'game-type-' . $type->ID; ?>" <?php selected( $group_game_type, $type->slug ); ?> ><?php echo $type->name; ?></option>
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
	 
			if ( isset( $_POST['group-game'] ) ) $setting = $_POST['group-game'];
			if ( isset( $_POST['group-game-type'] ) ) $setting = $_POST['group-game-type'];
	 
			groups_update_groupmeta( $group_id, 'group-game', $setting );
			groups_update_groupmeta( $group_id, 'group-game-type', $setting );
		}
	} // FuriaGamingCommunity_Games_BP_Group_Extension

	bp_register_group_extension( 'FuriaGamingCommunity_Games_BP_Group_Extension' );

else:
	if ( is_admin() && !class_exists( 'BP_Group_Extension' ) )
		add_action( 'admin_notices', 'admin_notices_bp_groups_missing' );
endif;
?>
