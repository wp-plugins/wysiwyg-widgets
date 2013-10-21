<?php

class WYSIWYG_Widgets
{

	public function __construct()
	{
		add_action('init', array($this, 'on_init_action'));
		add_action( 'widgets_init', array($this, 'register_widget'));
		add_action( 'add_meta_boxes', array($this, 'add_meta_box'), 20 );
	}

	public function on_init_action()
	{
		$labels = array(
		    'name' => 'Widget Blocks',
		    'singular_name' => 'Widget Block',
		    'add_new' => 'Add New',
		    'add_new_item' => 'Add New Widget Block',
		    'edit_item' => 'Edit Widget Block',
		    'new_item' => 'New Widget Block',
		    'all_items' => 'Widget Blocks',
		    'view_item' => 'View Widget Block',
		    'search_items' => 'Search Widget Blocks',
		    'not_found' =>  'No widget blocks found',
		    'not_found_in_trash' => 'No widget blocks found in Trash', 
		    'parent_item_colon' => '',
		    'menu_name' => 'Widget Blocks'
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
	        'More..',
	        array($this, 'meta_donate_box'),
	        'wysiwyg-widget',
	        'side',
            'low'
	    );
	    remove_meta_box('wpseo_meta', 'wysiwyg-widget', 'normal');
	}

	public function register_widget()
	{
		register_widget('WYSIWYG_Widgets_Widget');  
	}

	public function meta_donate_box($post)
	{
		?>
			<div style=" background: #222; color:#eee; padding:20px; ">
				<h4 style="margin-top:0;">Donate a token of your appreciation</h4>
				<p>I spent many hours developing and supporting this plugin.</p>
				<p>If you like it, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>
			</div>
			<div>
				<h4>Other ways to show your appreciation</h4>
				<ul class="ul-square">
					<li><a href="http://wordpress.org/support/view/plugin-reviews/wysiwyg-widgets?rate=5#postform" target="_blank">Leave a &#9733;&#9733;&#9733;&#9733;&#9733; review on WordPress.org</a></li>
                	<li><a href="http://twitter.com/?status=I%20use%20the%20WYSIWYG%20Widgets%20plugin%20by%20%40DannyvanKooten%20on%20my%20%23WordPress%20site%20to%20show%20beautiful%20widgets%20-%20love%20it!%20http%3A%2F%2Fwordpress.org%2Fplugins%2Fwysiwyg-widgets%2F" target="_blank">Tweet about WYSIWYG Widgets</a></li>
            		<li><a href="http://wordpress.org/plugins/wysiwyg-widgets/#compatibility">Vote "works" on the WordPress.org plugin page</a></li>
				</ul>
			</div>
			<div>
				<h4>Other useful plugins</h4>
				<ul class="ul-square">
					<li><a href="http://wordpress.org/plugins/mailchimp-for-wp/">MailChimp for Wordpress</a></li>
					<li><a href="http://wordpress.org/plugins/recent-facebook-posts/">Recent Facebook Posts</a></li>
				</ul>
			</div>
		<?php
	}
}