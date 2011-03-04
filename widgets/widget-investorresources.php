<?php
/**
 * ProGo Themes' InvestorResource Widget Class
 *
 * Creates the "InvestorResource" widget with form
 *
 * @since 1.0
 *
 * @package ProGo
 * @subpackage Testify
 */

class ProGo_Widget_InvestorResources extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.0
	 */
	function ProGo_Widget_InvestorResources() {
		$this->prefix = 'investors';
		$this->textdomain = 'investors';

		$widget_ops = array( 'classname' => 'investors', 'description' => __( 'Investor Resources form', $this->textdomain ) );
		$this->WP_Widget( "{$this->prefix}", __( 'ProGo : Investor Resources', $this->textdomain ), $widget_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 1.0
	 */
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? __('Investor Resources') : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		?>
        <div>	
		<h4 class="headline">If Life Insurance Had No Cost, How Much Would You Own?</h4><br />
        <p>You would buy as much as they would sell you.</p>
        <a href="#free" onclick="jQuery(this).parent().hide().next().show(); return false;" class="btn">Free Download</a></div>
        <div style="display:none"><form action="" method="post">
        <p><input type="text" class="txt" name="name" value="Name" onfocus="if(jQuery(this).val()=='Name') jQuery(this).val('');" onblur="if(jQuery(this).val()=='') jQuery(this).val('Name');" /><br />
        <input type="text" class="txt" name="email" value="Email" onfocus="if(jQuery(this).val()=='Email') jQuery(this).val('');" onblur="if(jQuery(this).val()=='') jQuery(this).val('Email');" /><br />
        <strong>Please select from the following</strong><br />
<label for="type[customer]"><input type="radio" name="type" value="customer" /> I'm a Customer</label> <label for="type[agent]"><input type="radio" name="type" value="agent" /> I'm an Insurance Agent</label><br />
<label for="newsletter"><input type="checkbox" name="newsletter" checked="checked" /> Yes I would like to receive email updates</label>
<a href="#sub" class="btn">Submit</a>
</p>
        </form>
        </div>
        <?php
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