<?php

if(!class_exists('WYSIWYG_Widget')) {

	class WYSIWYG_Widget extends WP_Widget {
		
		var $options;
		
		function __construct() {
			parent::__construct(false, $name = 'WYSIWYG Widget');
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
			$defaults = array( 'title' => 'Sign up for our newsletter!', 'email_label' => 'Email Address', 'name_label' => 'Name', 'text_after_signup' => 'Thanks for signing up to our newsletter!', 'text_before_form' => '', 'load_widget_styles' => 1);
			$instance = wp_parse_args( (array) $instance, $defaults );		
			
			$baseurl = includes_url('js/tinymce');
			
			extract($instance);

			?>
			 <p>
			  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:'); ?></label> 
			<div id="wysiwyg-editor-wrap">
				<textarea class="wysiwyg-overlay-toggle widefat" rows="16" cols="20" name="<?php echo $this->get_field_name('text'); ?>" id="<?php echo $this->get_field_id('text'); ?>"><?php if(isset($text)) echo $text; ?></textarea>
			</div>
			
			<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
		
			
			<input class="widget-width" type="hidden" value="400" name="widget-width">
			<input class="widget-height" type="hidden" value="350" name="widget-height">
			
			
			<?php 
		}

	}
}