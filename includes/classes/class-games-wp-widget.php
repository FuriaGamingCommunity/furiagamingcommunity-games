<?php
/**
 * Plugin Widgets.
 *
 * @uses WP_Widget
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if(class_exists('WP_Widget') && !class_exists('FuriaGamingCommunity_Games_Widget')) :

	/**
	 * Registers the Played Games widget.
	 * Uses the "Games" custom post and taxonomies.
	 *
	 * @author Xavier Giménez
	 * @version 1.0.0
	 */
	class FuriaGamingCommunity_Games_Widget extends WP_Widget {

		public $error;

		/**
		 * Register widget with WordPress.
		 */
		function __construct(){
			parent::__construct(
				'FuriaGamingCommunity_Games_Widget', // Base ID
				__('Furia Gaming Community - Games', 'furiagamingcommunity_games'), // Name
				array('description' => __('Displays a list of the currently played community games.', 'furiagamingcommunity_games'),	) // Args
				);

			if(is_admin())
				add_action('init', array(&$this, 'init'));
		}

		/**
		 * Check for errors.
		 */
		public function init(){

			if(is_wp_error($this->error))
				furiagamingcommunity_games_notices($this->error->get_error_message(), 'warning is-dismissible');
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget($args, $instance){
			
			// Defaults and arguments.
			$defaults = array (
				'number'		=> get_option('posts_per_page')
				);			
			$args = wp_parse_args($args , $defaults);
			extract($args, EXTR_SKIP);

			if(!empty($instance['number'])) $number = $instance['number'];

			// Add custom widget class.
			if(strpos($before_widget, 'class') === false)
				$before_widget = str_replace('>', 'class="games">', $before_widget);
			else
				$before_widget = str_replace('class="', 'class="games ', $before_widget);
			echo $before_widget;

			// The Query
			$game_loop = new WP_Query(array(
				'post_type'			=> 'game',
				'posts_per_page'	=> $number,
				));
			
			// The Loop.
			if($game_loop->have_posts())
				while ($game_loop->have_posts())
					$game_loop->the_post();
				else
					if(is_admin())
						add_action('admin_notices', 'admin_notices_missing_games');

			// Reset post data.
					wp_reset_postdata();

					echo $after_widget;
				}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form($instance){
			
			$number = !empty($instance['number']) ? $instance['number'] : ''; ?>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of games to display:', 'furiagamingcommunity_games'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" min="1" max="9" value="<?php echo esc_attr($number); ?>">
				<span class="description"><?php _e('Set to <strong>-1</strong> to display all games.', 'furiagamingcommunity_games'); ?></span>
			</p>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update($new_instance, $old_instance){
			$instance = array();
			$instance['number'] = (! empty($new_instance['number'])) ? absint($new_instance['number']) : $old_instance['number'];

			return $instance;
		}

	} // class FuriaGamingCommunity_Games_Widget

	add_action('widgets_init', function(){

		// Register widget.
		register_widget('FuriaGamingCommunity_Games_Widget');
	});

else:
	// Class not found.
	if(is_admin() && !class_exists('WP_Widget')) 
		furiagamingcommunity_games_notices(__('WP_Widget class not found!', 'furiagamingcommunity_games'), 'warning');
	endif;
?>
