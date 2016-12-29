<?php
/**
 * Plugin Group Extension Game Class.
 *
 * @uses BP_Group_Extension
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * The class_exists() check is recommended, to prevent problems
 * during upgrade or when the Groups component is disabled
 */
if(bp_is_active('groups') && !class_exists('FuriaGamingCommunity_Games_BP_Group_Extension')){

	/**
	 * Extends usability of BuddyPress Groups to let their user 
	 * administrators relate them to our games custom post type.
	 *
	 * @author Xavier Giménez
 	 * @version 1.1.0
	 */
	class FuriaGamingCommunity_Games_BP_Group_Extension extends BP_Group_Extension {
		/**
		 * Your __construct() method will contain configuration options
		 * for your extension, and will pass them to parent::init()
		 */
		function __construct(){
			$args = array(
				'slug'     => 'group-extension-games',
				'name'     => __('Games', 'furiagamingcommunity_games'),
				'show_tab' => 'noone'
				);
			parent::init($args);
		}

		/**
		 * settings_screen() is the catch-all method for displaying the content 
		 * of the edit, create, and Dashboard admin panels
		 */
		function settings_screen($group_id = NULL){

			if(empty($group_id))
				$group_id = bp_get_group_id();

			// Get group game meta.
			$group_game = groups_get_groupmeta($group_id, 'group-game');
			$group_game_type = groups_get_groupmeta($group_id, 'group-game-type');
			$group_game_rules = groups_get_groupmeta($group_id, 'group-game-rules');

			// Get the games.
			$games = get_games();
			// Get the game group types.
			$types = get_terms_types();
			?>

			<?php if(empty($games) && empty($types)) : ?>
				<div id="message" class="info">
					<p><?php _e('You need to set up at least one game and one game type before being able to assign them to any group.', 'furiagamingcommunity_games'); ?></p>
				</div>
			<?php elseif(empty($games) && !empty($types)) : ?>
				<div id="message" class="info">
					<p><?php _e('You need to set up at least one game before being able to assign them to any group.', 'furiagamingcommunity_games'); ?></p>
				</div>
			<?php elseif(!empty($games) && empty($types)) : ?>
				<div id="message" class="info">
					<p><?php _e('You need to set up at least one game type before being able to assign them to any game group.', 'furiagamingcommunity_games'); ?></p>
				</div>
			<?php endif; ?>
			
			<h3><?php _e('Game group settings', 'furiagamingcommunity_games'); ?></h3>
			<p><?php _e('Set a game to this group to identify its members as players or as a gaming community. Once set, you will be able to assign it a <strong><em>group type</em></strong>.', 'furiagamingcommunity_games'); ?></p>

			<label for="group-game"><?php _e('Group Game', 'furiagamingcommunity_games');?></label>
			<select name="group-game" id="group-game" aria-required="true" <?php disabled(empty($games), true); ?> >
				<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
				<?php if(!empty($games)) : foreach($games as $game) : ?>
					<option value="<?php echo strtolower($game->post_name); ?>" id="<?php echo 'game-' . $game->ID; ?>" <?php selected($group_game, $game->post_name); ?> ><?php echo $game->post_title; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<p><?php _e('Game group types are used to define different <strong><em>group roles</em></strong>. Groups that are dedicated to the same game may have different purpouses, thus they may have a different group type set.', 'furiagamingcommunity_games'); ?></p>

			<label for="group-game-type"><?php _e('Group Type', 'furiagamingcommunity_games');?></label>
			<select name="group-game-type" id="group-game-type" aria-required="true" <?php disabled(empty($types), true); ?> >
				<option value="" default><?php _e('None', 'furiagamingcommunity_games'); ?></option>
				<?php if(!empty($types)) : foreach($types as $type) : ?>
					<option value="<?php echo strtolower($type->slug); ?>" id="<?php echo 'game-type-' . $type->term_id; ?>" <?php selected($group_game_type, $type->slug); ?> ><?php echo $type->name; ?></option>
				<?php endforeach; endif; ?>
			</select>

			<h3><?php _e('Game rules', 'furiagamingcommunity_games'); ?></h3>
			<p><?php _e('Use the following text area to write the set of rules, if any, for the current game group. You can be as thorough as you want but mind to be clear and succinct to make it easier for your readers.', 'furiagamingcommunity_games'); ?></p>
			<?php
					// Load the rich text editor for this field.
			wp_editor($group_game_rules, 'group-game-rules', array( 'media_buttons' => false ));
			?>

			<?php
		}

		/**
		 * settings_screen_save() contains the catch-all logic for saving 
		 * settings from the edit, create, and Dashboard admin panels
		 */
		function settings_screen_save($group_id = NULL){
			
			if(empty($group_id))
				$group_id = bp_get_group_id();

			$setting['group-game'] = '';
			$setting['group-game-type'] = '';
			$setting['group-game-rules'] = '';

			if(!empty($_POST['group-game'])){
				// Store the group game.
				$setting['group-game'] = sanitize_text_field($_POST['group-game']);
				// Update the group name.
				groups_update_groupmeta($group_id, 'group-game', $setting['group-game']);
			}

			if(!empty($_POST['group-game-type'])){
				// Store the game group type.
				$setting['group-game-type'] = sanitize_text_field( $_POST['group-game-type']);

				// Get game group and game group type.
				$game = get_game_by_slug($_POST['group-game']);
				$type = get_term_type_by_slug($_POST['group-game-type']);

				// Attempt to add the game group type to the game object.
				if(!has_term($type, 'game-types', $game)){
					$term_taxonomy_ids = wp_set_object_terms($game->ID, $type->term_id, 'game-types', true);

					if(is_wp_error($term_taxonomy_ids)){
						if(is_admin()) add_action('admin_notices', 'admin_notices_missing_game_type');
						else bp_core_add_message(message_missing_game_type($type), 'error');
					} else {
						// Context message.
						bp_core_add_message(message_updated_game_group());
					}
				}

				// Update the group meta.
				groups_update_groupmeta($group_id, 'group-game-type', $setting['group-game-type']);
			}

			if(!empty($_POST['group-game-rules'])){
				// Load the editor for this field.
				$setting['group-game-rules'] = wp_kses_post($_POST['group-game-rules']);
				// Update the group rules.
				groups_update_groupmeta($group_id, 'group-game-rules', $setting['group-game-rules']);
			}

			if(!empty($setting['group-game'] || !empty($setting['group-game-type']) || !empty($setting['group-game-rules']))){
				// Success message.
				bp_core_add_message(message_updated_game_group());
			}
		}

	} // FuriaGamingCommunity_Games_BP_Group_Extension

	bp_register_group_extension('FuriaGamingCommunity_Games_BP_Group_Extension');

} else {
	if(is_admin() && !class_exists('BP_Group_Extension'))
		add_action('admin_notices', 'admin_notices_missing_bp_groups');
}
?>
