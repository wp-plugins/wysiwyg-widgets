<?php
if (!class_exists('WYSIWYG_Widgets_Widget')) {

    class WYSIWYG_Widgets_Widget extends WP_Widget {

        function __construct() {
            $widget_ops = array('classname' => 'wysiwyg_widget widget_text', 'description' => __('A widget with a WYSIWYG / Rich Text editor - supports media uploading'));
            $control_ops = array('width' => 560, 'height' => 400);

            parent::__construct('wysiwyg_widgets_widget', 'WYSIWYG Widget', $widget_ops, $control_ops);
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            $text = apply_filters('widget_text', $instance['text'], $instance);
            echo $before_widget;
            
            if (!empty($title)) {
                echo $before_title . $title . $after_title;
            }
            
            ?>

            <div class="textwidget"><?php echo wpautop($text); ?></div>
            
            <?php
            echo $after_widget;
        }

        function update($new_instance, $old_instance) {
            $instance = $old_instance;

            $instance['title'] = strip_tags($new_instance['title']);
            $instance['type'] = strip_tags($new_instance['type']);
            
            if (current_user_can('unfiltered_html'))
                $instance['text'] = $new_instance['text'];
            else
                $instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text'])));

            return $instance;
        }

        function form($instance) {
            $instance = wp_parse_args((array) $instance, array('title' => '', 'text' => '', 'type' => 'visual'));
            
            $title = strip_tags($instance['title']);
            $text = esc_textarea($instance['text']);
            $type = esc_textarea($instance['type']);
            
            ?>
            
            <input id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" type="hidden" value="<?php echo esc_attr($type); ?>" />
           
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
            
            <div class="editor_toolbar">
                <div class="editor_toggle_buttons">
                    <a id="widget-<?php echo $this->id_base; ?>-<?php echo $this->number; ?>-html"<?php if ($type == 'html') { ?> class="active"<?php } ?>><?php _e('HTML'); ?></a>
                    <a id="widget-<?php echo $this->id_base; ?>-<?php echo $this->number; ?>-visual"<?php if ($type == 'visual') { ?> class="active"<?php } ?>><?php _e('Visual'); ?></a>
                </div>

                <div class="editor_media_buttons">
                    <?php do_action('media_buttons'); ?>
                </div>
            </div>
            <div class="editor_container">
                <textarea class="widefat" rows="16" cols="40" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
            </div>
            
            <?php
        }

    }

}