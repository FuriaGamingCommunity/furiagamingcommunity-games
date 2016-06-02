<?php
/**
 * Plugin Group Extension Game Class.
 *
 * @uses BP_Group_Extension
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) && !class_exists( 'FuriaGamingCommunity_Games_BP_Group_Extension' ) ) {

	class FuriaGamingCommunity_Games_BP_Group_Extension extends BP_Group_Extension {
		/**
		 * Your __construct() method will contain configuration options for 
		 * your extension, and will pass them to parent::init()
		 */
		function __construct() {
			$args = array(
				'slug' => 'group-extension-games',
				'name' => __( 'Games', 'furiagamingcommunity_games' ),
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
			
			<p><?php printf( __( 'This group members are players from <a href="%1$s">%2$s</a>.', 'furiagamingcommunity_games' ), get_permalink( $game ), $game->post_title ); ?><p>

			<?php if ( $group_game_type ) : ?>

			<?php $type = get_term_type_by_slug( $group_game_type ); ?>

			<p><?php printf( __( 'This group is a %1$s of %2$s.', 'furiagamingcommunity_games' ), get_term_permalink( $type, 'game-types' ), get_bloginfo( 'name' ) ); ?><p>

			<?php endif; ?>
			
			<?php endif; ?>

			<?php
			$options = get_game_options();
			var_dump($options);
		}
	 
		/**
		 * settings_screen() is the catch-all method for displaying the content 
		 * of the edit, create, and Dashboard admin panels
		 */
		function settings_screen( $group_id = NULL ) {

			// Get group game meta and game list
			$group_game = groups_get_groupmeta( $group_id, 'group-game' );
			$group_game_type = groups_get_groupmeta( $group_id, 'group-game-type' );

			// Get the games
			$games = get_games();
			// Get the game group types
			$types = get_terms_types();
			?>

			<?php if ( empty( $games ) && empty( $types ) ) : ?>
			<div id="message" class="info">
				<p><?php _e( 'You need to set up at least one game and one game type before being able to assign them to any group.', 'furiagamingcommunity_games' ); ?></p>
			</div>
			<?php elseif ( empty( $games) && !empty( $types ) ) : ?>
			<div id="message" class="info">
				<p><?php _e( 'You need to set up at least one game before being able to assign them to any group.', 'furiagamingcommunity_games' ); ?></p>
			</div>
			<?php elseif ( !empty( $games) && empty( $types ) ) : ?>
			<div id="message" class="info">
				<p><?php _e( 'You need to set up at least one game type before being able to assign them to any game group.', 'furiagamingcommunity_games' ); ?></p>
			</div>
			<?php endif; ?>
			
			<div>
				<p><?php _e( 'Set a game to this group to identify its members as players or as a gaming community. Once set, you will be able to assign it a <strong><em>group type</em></strong>.', 'furiagamingcommunity_games' ); ?></p>
				<p><?php _e( 'Game group types are used to define different <strong><em>group roles</em></strong>. Groups that are dedicated to the same game may have different purpouses, thus they may have a different group type set.', 'furiagamingcommunity_games' ); ?></p>
			</div>

			<div>
				<label for="group-game"><?php _e( 'Group Game', 'furiagamingcommunity_games' );?></label>
				<select name="group-game" id="group-game" aria-required="true" <?php disabled( empty( $games ), true ); ?> >
					<option value="" default><?php _e( 'None', 'furiagamingcommunity_games' ); ?></option>
					<?php if ( !empty( $games ) ) : foreach( $games as $game ) :	?>
					<option value="<?php echo strtolower( $game->post_name ); ?>" id="<?php echo 'game-' . $game->ID; ?>" <?php selected( $group_game, $game->post_name ); ?> ><?php echo $game->post_title; ?></option>
					<?php endforeach; endif; ?>
				</select>
			</div>

			<div>
				<label for="group-game-type"><?php _e( 'Group Type', 'furiagamingcommunity_games' );?></label>
				<select name="group-game-type" id="group-game-type" aria-required="true" <?php disabled( empty( $types ), true ); ?> >
					<option value="" default><?php _e( 'None', 'furiagamingcommunity_games' ); ?></option>
					<?php if ( !empty( $types ) ) : foreach( $types as $type ) :	?>
					<option value="<?php echo strtolower( $type->slug ); ?>" id="<?php echo 'game-type-' . $type->term_id; ?>" <?php selected( $group_game_type, $type->slug ); ?> ><?php echo $type->name; ?></option>
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
			$setting['group-game'] = '';
			$setting['group-game-type'] = '';

			if ( !empty( $_POST['group-game'] ) ) {
				$setting['group-game'] = absint( $_POST['group-game'] );

				groups_update_groupmeta( $group_id, 'group-game', $setting['group-game'] );
			}

			if ( !empty( $_POST['group-game-type'] ) ) { 
				$setting['group-game-type'] = absint(  $_POST['group-game-type'] );

				// Get game group and game group type.
				$game = get_game_by_slug( $_POST['group-game'] );
				$type = get_term_type_by_slug( $_POST['group-game-type'] );

				// Attempt to add the game group type to the game object.
				$term_taxonomy_ids = wp_set_object_terms( $game->ID, $type->term_id, 'game-types' );

				if ( is_wp_error( $term_taxonomy_ids ) ) {
					if ( is_admin() ) add_action( 'admin_notices', 'admin_notices_game_type_not_set' );
					else bp_core_add_message( sprintf( __( 'An error occurred while adding the selected game group type: %1$s',  'furiagamingcommunity_games' ), $type->slug ), 'error' );
				} else {
					// Context message.
					bp_core_add_message( __( '<em>Game</em> group type was updated successfully.',  'furiagamingcommunity_games' ) );
				}

				// Update the group meta.
				groups_update_groupmeta( $group_id, 'group-game-type', $setting['group-game-type'] );
			}
	 
			if( !empty( $setting['group-game'] || !empty( $setting['group-game-type'] ) ) )
				// Success message.
				bp_core_add_message( __( '<em>Game</em> group was updated successfully.',  'furiagamingcommunity_games' ) );
		}

	} // FuriaGamingCommunity_Games_BP_Group_Extension

	bp_register_group_extension( 'FuriaGamingCommunity_Games_BP_Group_Extension' );

	} else {
		if ( is_admin() && !class_exists( 'BP_Group_Extension' ) )
			add_action( 'admin_notices', 'admin_notices_bp_groups_missing' );
	}
?>