<?php
/**
 * The Furia Gaming Community - Games Plugin.
 *
 * Furia Gaming Community - Games is a plugin made for http://furiaguild.com that adds Games as an option to BuddyPress Group Extension.
 *
 * @package  FuriaGamingCommunity
 * @subpackage FuriaGamingCommunity_Games
 * @author Xavier Giménez <xavier.gimenez.segovia@gmail.com>
 * @license GPL-2.0+
 **/

/**
 * Plugin Name:     Furia Gaming Community - Games
 * Plugin URI:      https://github.com/nottu2584/furiagamingcommunity-games
 * Description:     Sets a new post type named slides and adds a custom widget to display them into slideshows.
 * Author:          Xavier Giménez
 * Author URI:      https://es.linkedin.com/pub/javier-gimenez-segovia/
 * Author Email:    xavier.gimenez.segovia@gmail.com
 * Version:         1.2.0
 * Depends:         BuddyPress
 * Text Domain:     furiagamingcommunity_games
 * License:         GPLv2 or later (LICENSE)
**/

// Exit if accessed directly
defined('ABSPATH') || exit;

if(!class_exists('FuriaGamingCommunity_Games')) :

/**
 * Inits the plugin dependencies.
 *
 * @author Xavier Giménez
 * @version 1.2.0
 */
class FuriaGamingCommunity_Games {
	
	public static function instance(){

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if(null === $instance){
			
			// Setup plugin object.
			$instance = new FuriaGamingCommunity_Games;
			
			// Setup plugin dependencies
			$instance->constants();
			$instance->setup_globals();
			$instance->includes();
			$instance->classes();
			$instance->actions();
		}

		// Always return the instance
		return $instance;
	}

	/**
	 * A dummy constructor to prevent FuriaGamingCommunity_Games from being loaded more than once.
	 * @since 1.0.0
	 */
	private function __construct(){ /* Do nothing here */ }

	/**
	 * Setup actions.
	 *
	 * @since 1.1.0
	 */
	private function actions(){

	}

	/**
	 * Setup classes.
	 *
	 * @since 1.0.0
	 */
	private function classes(){

		$this->games = new Games();
	}

	/**
	 * Bootstrap constants.
	 *
	 * @since 1.0.0
	 *
	 * @uses plugin_dir_path()
	 * @uses plugin_dir_url()
	 */
	private function constants(){

		// Path
		if(! defined('FGC_G_PLUGIN_DIR'))
			define('FGC_G_PLUGIN_DIR', plugin_dir_path(__FILE__));
		// URL
		if(! defined('FGC_G_PLUGIN_URL'))
			define('FGC_G_PLUGIN_URL', plugin_dir_url(__FILE__));
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 */
	private function includes(){

		require($this->plugin_dir . 'includes/functions.php');
		require($this->plugin_dir . 'includes/messages.php');
		require($this->plugin_dir . 'includes/classes/class-games.php');
		require($this->plugin_dir . 'includes/classes/class-games-bp-group-extension.php');
		require($this->plugin_dir . 'includes/classes/class-games-wp-widget.php');
	}

	/**
	 * Declare class constants.
	 *
	 * @since 1.0.0
	 */
	private function setup_globals(){

		$this->file           = constant('FGC_G_PLUGIN_DIR') . __FILE__;
		$this->basename       = basename(constant('FGC_G_PLUGIN_DIR')) . __FILE__;

		$this->plugin_dir     = trailingslashit(constant('FGC_G_PLUGIN_DIR'));
		$this->plugin_url     = trailingslashit(constant('FGC_G_PLUGIN_URL'));
	}

} // class FuriaGamingCommunity_Games


/**
 * Launch a single instance of the plugin
 * @since 1.0.0
 * 
 * @return FuriaGamingCommunity_Games The plugin instance
 */
function furiagamingcommunity_games(){
	return FuriaGamingCommunity_Games::instance();
}
add_action('bp_include', 'furiagamingcommunity_games');

/**
 * Register the text domain
 * @since 1.0.0
 */
function furiagamingcommunity_games_load_textdomain(){
	load_plugin_textdomain('furiagamingcommunity_games', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'furiagamingcommunity_games_load_textdomain');

endif;
?>
