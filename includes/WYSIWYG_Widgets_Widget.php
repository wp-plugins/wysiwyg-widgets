<?php

class WYSIWYG_Widgets_Widget extends WP_Widget
{
	public function __construct() {
		parent::__construct(
	 		'wysiwyg_widgets_widget', // Base ID
			'WYSIWYG Widget', // Name
			array( 'description' => 'Select a rich-formatted widget block and show it in a widget area.' ) // Args
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
		$post = get_post($id);

		echo $before_widget;

		if(!empty($title)) { echo $before_title . $title . $after_title; }

		if($post && !empty($id)) {
			$content = $post->post_content;
			$content = do_shortcode($content);
			$content = "\n<!-- Widget by WYSIWYG Widgets v". WYWI_VERSION_NUMBER ." - http://wordpress.org/plugins/wysiwyg-widgets/ -->\n" . wpautop($content) . "\n<!-- / WYSIWYG Widgets -->\n";
			echo $content;		
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
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['wysiwyg-widget-id'] = $new_instance['wysiwyg-widget-id'];
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

		$title = isset($instance['title']) ? $instance['title'] : 'Just another WYSIWYG Widget';
		$selected_widget_id = (isset($instance['wysiwyg-widget-id'])) ? $instance['wysiwyg-widget-id'] : 0;

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'wysiwyg-widget-id' ); ?>"><?php _e( 'Widget Block:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('wysiwyg-widget-id'); ?>" name="<?php echo $this->get_field_name( 'wysiwyg-widget-id' ); ?>">
				<option value="0" disabled <?php selected($selected_widget_id, 0); ?>><?php if(empty($posts)) { ?>No widget blocks found.<?php } else { ?>Select a widget block..<?php } ?></option>
				<?php foreach($posts as $p) { ?>
					<option value="<?php echo $p->ID; ?>" <?php selected($selected_widget_id, $p->ID); ?>><?php echo $p->post_title; ?></option>
				<?php } ?>
			</select>
		</p>

		<p class="help"><a href="edit.php?post_type=wysiwyg-widget">Manage your widget blocks here</a></p>
		<p style="background:#222; color:#eee; padding:10px; ">If you like this plugin, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>       
		<?php
	}

}