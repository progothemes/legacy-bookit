<?php
/**
 * ProGo Themes' Testify plugin Random Widget Class
 *
 * Creates a "Testify : Random" widget to pull in a Testimonial at random
 *
 * @since 1.0
 *
 * @package ProGo
 * @subpackage Testify
 */

class ProGo_Widget_Family extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.0
	 */
	function ProGo_Widget_Family() {
		$this->prefix = 'family';
		$this->textdomain = 'family';

		$widget_ops = array( 'classname' => 'family', 'description' => __( 'Links to your Family bios', $this->textdomain ) );
		$this->WP_Widget( "{$this->prefix}", __( 'ProGo : Meet the Family', $this->textdomain ), $widget_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 1.0
	 */
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? __('Meet the Family') : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
			
		//echo "<p>random $num testimonial here...</p>";
		$args = array('post_type' => 'progo_family', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' );
		$fam = get_posts($args);
		foreach($fam as $t) {
			echo '<a href="'. get_bloginfo('url') .'/family/#'. $t->post_name .'" class="thm">'. get_the_post_thumbnail($t->ID) . $t->post_title .'</a>';
		}
		
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 1.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 1.0
	 */
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
}

?>