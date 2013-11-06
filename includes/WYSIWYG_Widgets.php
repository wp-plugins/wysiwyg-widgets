<?php

class WYSIWYG_Widgets
{

	public function __construct()
	{
		add_action('init', array($this, 'register_post_type'));
		add_action('plugins_loaded', array($this, 'load_textdomain'));
		add_action( 'widgets_init', array($this, 'register_widget'));
		add_action( 'add_meta_boxes', array($this, 'add_meta_box'), 20 );
		add_action('do_meta_boxes', array($this, 'remove_meta_boxes'));
	}

	public function load_textdomain() {
  		load_plugin_textdomain( 'wysiwyg-widgets', false, 'wysiwyg-widgets/languages/' );
	}

	public function register_post_type()
	{
		$labels = array(
		    'name' => __('Widget Blocks', 'wysiwyg-widgets'),
		    'singular_name' => __('Widget Block', 'wysiwyg-widgets'),
		    'add_new' => __('New Widget Block', 'wysiwyg-widgets'),
		    'add_new_item' => __('Add New Widget Block', 'wysiwyg-widgets'),
		    'edit_item' => __('Edit Widget Block', 'wysiwyg-widgets'),
		    'new_item' => __('New Widget Block', 'wysiwyg-widgets'),
		    'all_items' => __('All Widget Blocks', 'wysiwyg-widgets'),
		    'view_item' => __('View Widget Block', 'wysiwyg-widgets'),
		    'search_items' => __('Search Widget Blocks', 'wysiwyg-widgets'),
		    'not_found' =>  __('No widget blocks found', 'wysiwyg-widgets'),
		    'not_found_in_trash' => __('No widget blocks found in Trash', 'wysiwyg-widgets'), 
		    'menu_name' => __('Widget Blocks', 'wysiwyg-widgets')
		  );
		$args = array(
			'public' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'labels' => $labels,
			'has_archive' => false,
			'supports' => array('title', 'editor'),
			'rewrite' => false,
			'map_meta_cap' => true
		);

   		register_post_type( 'wysiwyg-widget', $args );

	}

	public function add_meta_box()
	{
		add_meta_box( 
        'wysiwyg-widget-donate-box',
	        __('More..', 'wysiwyg-widgets'),
	        array($this, 'meta_donate_box'),
	        'wysiwyg-widget',
	        'side',
            'low'
	    );
	}

	
	/**
	* Remove all metaboxes except "submitdiv".
	* Also removes all metaboxes added by other plugins..
	*/
	public function remove_meta_boxes() {
		global $wp_meta_boxes;

		if ( isset( $wp_meta_boxes["wysiwyg-widget"] ) && is_array( $wp_meta_boxes["wysiwyg-widget"] ) ) {
			$meta_boxes = $wp_meta_boxes["wysiwyg-widget"];
			$allowed_meta_boxes = array( 'submitdiv' );

			foreach ( $meta_boxes as $context => $context_boxes ) {
				if ( ! is_array( $context_boxes ) ) { continue; }

				foreach ( $context_boxes as $priority => $priority_boxes ) {
					if ( !is_array( $priority_boxes ) ) { continue; }

					foreach ( $priority_boxes as $meta_box_id => $meta_box_args ) {
						if ( stristr( $meta_box_id, 'wysiwyg-widget' ) === false && !in_array( strtolower($meta_box_id), $allowed_meta_boxes ) ) {

							remove_meta_box($meta_box_id, 'wysiwyg-widget', $priority);
							
							//unset( $wp_meta_boxes["wysiwyg-widget"][$context][$priority][$meta_box_id] );
						}
					}
				}
			}
		}
	}

	public function register_widget()
	{
		register_widget('WYSIWYG_Widgets_Widget');  
	}

	public function meta_donate_box($post)
	{
		?>
			<div>
				<h4><?php _e('And now?', 'wysiwyg-widgets'); ?></h4>
				<p><?php printf(__('Show this widget block by going to your %swidgets page%s and then dragging the WYSIWYG Widget to one of your widget areas.', 'wysiwyg-widgets'), '<a href="'. admin_url('widgets.php') .'">', '</a>'); ?></p>
			</div>
			<div style="margin:1.33em 0; background: #222; color:#eee; padding:20px; ">
				<h4 style="margin:0;">Donate a token of your appreciation</h4>
				<p>If you like this plugin, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>
			</div>
			<div>
				<h4><?php _e('Show your appreciation', 'wysiwyg-widgets'); ?></h4>
				<ul class="ul-square">
					<li><a href="http://wordpress.org/support/view/plugin-reviews/wysiwyg-widgets?rate=5#postform" target="_blank"><?php _e('Leave a &#9733;&#9733;&#9733;&#9733;&#9733; review on WordPress.org', 'wysiwyg-widgets'); ?></a></li>
                	<li><a href="http://twitter.com/?status=I%20use%20the%20WYSIWYG%20Widgets%20plugin%20by%20%40DannyvanKooten%20on%20my%20%23WordPress%20site%20to%20show%20beautiful%20widgets%20-%20love%20it!%20http%3A%2F%2Fwordpress.org%2Fplugins%2Fwysiwyg-widgets%2F" target="_blank"><?php _e('Tweet about WYSIWYG Widgets', 'wysiwyg-widgets'); ?></a></li>
            		<li><a href="http://wordpress.org/plugins/wysiwyg-widgets/#compatibility"><?php _e('Vote "works" on the WordPress.org plugin page', 'wysiwyg-widgets'); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php _e('Other useful plugins', 'wysiwyg-widgets'); ?></h4>
				<ul class="ul-square">
					<li><a href="http://wordpress.org/plugins/mailchimp-for-wp/">MailChimp for Wordpress</a></li>
					<li><a href="http://wordpress.org/plugins/recent-facebook-posts/">Recent Facebook Posts</a></li>
				</ul>
			</div>
		<?php
	}
}