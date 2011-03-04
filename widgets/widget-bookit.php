<?php
/**
 * ProGo Themes' BookIt Widget Class
 *
 * This widget is for controlling the "ProGo : BookIt" block
 * modelled after Hybrid theme's widget definitions
 *
 * @since 1.0
 *
 * @package ProGo
 * @subpackage Core
 */

class ProGo_Widget_BookIt extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.0
	 */
	function ProGo_Widget_BookIt() {
		$this->prefix = 'progo';
		$this->textdomain = 'progo';

		$widget_ops = array( 'classname' => 'bookit', 'description' => __( 'Contact call-to-action', $this->textdomain ) );
		$this->WP_Widget( "{$this->prefix}-bookit", __( 'ProGo : BookIt', $this->textdomain ), $widget_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 1.0
	 */
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$url = strip_tags($instance['url']);
		$text = strip_tags($instance['text']);
		
		echo $before_widget;
		echo $before_title .'<a href="'. esc_url($url) .'" class="book button">'. $title .'</a>'. $after_title;
		
		?>
		<p><span class="lq">&ldquo;</span><?php echo nl2br(wp_kses($text,array('em'=>array(),'strong'=>array()))); ?><span class="rq">&rdquo;</span></p><?php
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 1.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'url' => '', 'text' => '') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['text'] = strip_tags($new_instance['text']);

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 1.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'url' => '', 'text' => 'Possible statment about what bookng david means here. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt  Lorem ipsum dolor sit amet, consectet') );
		$title = strip_tags($instance['title']);
		$url = strip_tags($instance['url']);
		$text = strip_tags($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL to link to'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_url($url); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text paragraph'); ?></label><br /><textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" cols="40" rows="5"><?php echo esc_attr($ft); ?></textarea></p>
<?php
	}
}

?>