<?php
/**
 * The Furia Gaming Community - Games Plugin.
 *
 * Furia Gaming Community - Games is a plugin made for http://furiaguild.com that adds Games as an option to BuddyPress Group Extension.
 *
 * @package  FuriaGamingCommunity
 * @subpackage FuriaGamingCommunity_Games
 * @author Xavier Giménez Segovia <xavier.gimenez.segovia@gmail.com>
 * @license GPL-2.0+
 **/
 
/**
 * Plugin Name:     Furia Gaming Community - Games
 * Plugin URI:      https://github.com/nottu2584/furiagamingcommunity-games
 * Description:     Sets a new post type named slides and adds a custom widget to display them into slideshows.
 * Author:          Xavier Giménez Segovia
 * Author URI:      https://es.linkedin.com/pub/javier-gimenez-segovia/
 * Author Email:    xavier.gimenez.segovia@gmail.com
 * Version:         1.0.2
 * Depends:         BuddyPress
 * Text Domain:     furiagamingcommunity_games
 * License:         GPLv2 or later (LICENSE)
**/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Inits the plugin dependencies.
 *
 * @author Xavier Giménez Segovia
 * @version 1.0.1
 */
class FuriaGamingCommunity_Games {

    /**
     * Set all dependencies.
     * @since 1.0.1
     */
    public function __construct() {

        $this->constants();
        $this->setup_globals();
        $this->includes();
    }

    /**
     * Bootstrap constants.
     *
     * @since 1.0.1
     *
     * @uses plugin_dir_path()
     * @uses plugin_dir_url()
     */
    private function constants() {

        // Path
        if ( ! defined( 'FGC_G_PLUGIN_DIR' ) )
            define( 'FGC_G_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        // URL
        if ( ! defined( 'FGC_G_PLUGIN_URL' ) )
            define( 'FGC_G_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Declare class constants.
     *
     * @since 1.0.1
     */
    private function setup_globals() {

        $this->file           = constant( 'FGC_G_PLUGIN_DIR' ) . __FILE__;
        $this->basename       = basename( constant( 'FGC_G_PLUGIN_DIR' ) ) . __FILE__;
        $this->plugin_dir     = trailingslashit( constant( 'FGC_G_PLUGIN_DIR' ) );
        $this->plugin_url     = trailingslashit( constant( 'FGC_G_PLUGIN_URL' ) );
    }

    /**
     * Include required files.
     *
     * @since 1.0.1
     */
    private function includes() {
        require( $this->plugin_dir . 'class/class-games.php' );
        require( $this->plugin_dir . 'class/class-games-bp-group-extension.php' );
    }
    
} // class FuriaGamingCommunity_Games
    
/**
 * Helper functions to set context on game pages
 * @since 1.0.1
 */
function furiagamingcommunity_games_init() {
    register_activation_hook( basename( __FILE__ ), new FuriaGamingCommunity_Games );
}
add_action( 'bp_include', 'furiagamingcommunity_games_init' );

/**
 * Display admin notices
 * @since 1.0.1
 *
 * @param str $message Text message to display at the notice.
 * @param str $type [error|info|success|warning] Type of message.
 */
function furiagamingcommunity_games_notices( $message, $type = 'warning' ) {
    
    if ( $message ) :
    
    ?>
    <div class="notice notice-<?php echo $type ?>">
        <p><?php echo $message; ?></p>
    </div>
    <?php

    else :
        return false;

    endif;
}

/**
 * Register the text domain
 * @since 1.0.0
 */
function furiagamingcommunity_games_load_textdomain() {
    load_plugin_textdomain( 'furiagamingcommunity_games', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'furiagamingcommunity_games_load_textdomain');


/** 
 * Plugin functions.
 *
 * General plugin functions and helpers for the game post type.
 */

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
 * Helper function to set context on game class
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
 * Get all games
 * @since 1.0.0
 *
 * @return array|bool Retrieves a list of posts matching the game type. Returns false if empty.
 */
function get_games() {
    
    $args = array(
        'post_type'         => 'game',
        'post_status'       => 'publish'
        );

    $posts = get_posts( $args );

    if ( empty( $posts ) )
        return false;
    else
        return $posts;
}

/**
 * Get a single game by the slug
 * @since 1.0.0
 *
 * @return array|bool Retrieves a list of posts matching the game type selected by the game slug. Returns false in case the slug was not provided.
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
 *
 * @return array List of post terms under game races.
 */
function get_game_races( $post_id ) {
    return wp_get_post_terms( $post_id, 'game-races' );
}

/**
 * Get game classes
 * @since 1.0.0
 *
 * @return array List of post terms under game classes.
 */
function get_game_classes( $post_id ) {
    return wp_get_post_terms( $post_id, 'game-classes' );
}

/**
 * Get game roles
 * @since 1.0.0
 *
 * @return array List of post terms under game roles.
 */
function get_game_roles( $post_id ) {
    return wp_get_post_terms( $post_id, 'game-roles' );
}
?>
