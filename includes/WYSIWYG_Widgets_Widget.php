<?php

class WYSIWYG_Widgets_Widget extends WP_Widget
{
	public function __construct() {
		parent::__construct(
	 		'wysiwyg_widgets_widget', // Base ID
			'WYSIWYG Widget', // Name
			array( 'description' => 'Displays one of your Widget Blocks.' ) // Args
		);
	}

 	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$id = $instance['wysiwyg-widget-id'];

		$title = apply_filters( 'widget_title', $instance['title'] );
		$show_title = (isset($instance['show_title'])) ? $instance['show_title'] : 1;
		$post = get_post($id);

		echo $before_widget;

		if($show_title) { echo $before_title . $title . $after_title; }

		if($post && !empty($id)) {
			$content = $post->post_content;
			$content = do_shortcode($content);
			$content = "\n<!-- Widget by WYSIWYG Widgets v". WYWI_VERSION_NUMBER ." - http://wordpress.org/plugins/wysiwyg-widgets/ -->\n" . wpautop($content) . "\n<!-- / WYSIWYG Widgets -->\n";
			echo apply_filters('ww_content', $content, $id);		
		} elseif(current_user_can('manage_options')) { ?>
				<p>
					<?php if(empty($id)) { ?>
						Please select a widget block to show in this area.
					<?php } else { ?>
						No widget block found with ID <?php echo $id; ?>, please select an existing widget block in the widget settings.
					<?php } ?>
				</p>
		<?php 
		}

		echo $after_widget;
		
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
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wysiwyg-widget-id'] = $new_instance['wysiwyg-widget-id'];
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_title'] = (isset($new_instance['show_title']) && $new_instance['show_title'] == 1) ? 1 : 0;

		// grab title from widget block
		if($instance['wysiwyg-widget-id']) {
			$post = get_post($instance['wysiwyg-widget-id']);

			if($post) {
				$instance['title'] = $post->post_title;
			}
		}		

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		$posts = (array) get_posts(array(
			'post_type' => 'wysiwyg-widget',
			'numberposts' => -1
		));

		$title = isset($instance['title']) ? $instance['title'] : '';
		$show_title = (isset($instance['show_title'])) ? $instance['show_title'] : 1;
		$selected_widget_id = (isset($instance['wysiwyg-widget-id'])) ? $instance['wysiwyg-widget-id'] : 0;
		?>

		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="hidden" value="<?php echo esc_attr( $title ); ?>" />

		<p>	
			<label for="<?php echo $this->get_field_id( 'wysiwyg-widget-id' ); ?>"><?php _e( 'Widget Block to show:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('wysiwyg-widget-id'); ?>" name="<?php echo $this->get_field_name( 'wysiwyg-widget-id' ); ?>">
				<option value="0" disabled <?php selected($selected_widget_id, 0); ?>><?php if(empty($posts)) { ?>No widget blocks found.<?php } else { ?>Select a widget block..<?php } ?></option>
				<?php foreach($posts as $p) { ?>
					<option value="<?php echo $p->ID; ?>" <?php selected($selected_widget_id, $p->ID); ?>><?php echo $p->post_title; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label><input type="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" value="1" <?php checked($show_title, 1); ?> /> <?php _e("Show title?", "wysiwyg-widgets"); ?></label>
		</p>

		<p class="help">Manage your widget blocks <a href="edit.php?post_type=wysiwyg-widget">here</a></p>
		<?php
	}

}