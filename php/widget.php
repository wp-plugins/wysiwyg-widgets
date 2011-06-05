<?php

if(!class_exists('WYSIWYG_Widget')) {

	class WYSIWYG_Widget extends WP_Widget {
		
		var $options;
		
		function __construct() {
			$widget_ops = array('classname' => 'wysiwyg_widget', 'description' => __('A widget with a WYSIWYG / Rich Text editor - supports media uploading'));
			$control_ops = array('width' => 400, 'height' => 350);
		
			parent::__construct(false, 'WYSIWYG Widget', $widget_ops, $control_ops);
		}

		function widget($args, $instance) {	
			
			extract( $args );
			extract($instance);

			?>
				  <?php echo $before_widget; ?>
					  <?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
						<div class="wysiwyg-widget-content"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
				  <?php echo $after_widget; ?>
			<?php
		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			if ( current_user_can('unfiltered_html') )
				$instance['text'] =  $new_instance['text'];
			else
				$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
			$instance['filter'] = isset($new_instance['filter']);
			
			return $instance;
		}

		function form($instance) {	
						
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
			$title = strip_tags($instance['title']);
			$text = $instance['text'];

			?>
			 <p>
			  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:'); ?></label> 
			<textarea class="wysiwyg-overlay-toggle widefat" rows="16" cols="20" name="<?php echo $this->get_field_name('text'); ?>" id="<?php echo $this->get_field_id('text'); ?>"><?php if(isset($text)) echo $text; ?></textarea>
			
			<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
			
			
			<?php 
		}

	}
}