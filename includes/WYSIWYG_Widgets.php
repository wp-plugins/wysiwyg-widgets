<?php

class WYSIWYG_Widgets
{

	public function __construct()
	{
		add_action('init', array($this, 'on_init_action'));
		add_action( 'widgets_init', array($this, 'register_widget'));
		add_action( 'add_meta_boxes', array($this, 'add_meta_box' ) );
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
			'show_in_menu' => 'themes.php',
			'rewrite' => false
		);
   		register_post_type( 'wysiwyg-widget', $args );

	}

	public function add_meta_box()
	{
		add_meta_box( 
        'wysiwyg-widget-donate-box',
	        'Donate a token of your appreciation',
	        array($this, 'meta_donate_box'),
	        'wysiwyg-widget',
	        'side',
            'low'
	    );
	}

	public function register_widget()
	{
		register_widget('WYSIWYG_Widgets_Widget');  
	}

	public function meta_donate_box($post)
	{
		?>
			<div style=" background: #222; color:#eee; padding:20px; ">
				<p>I spent countless hours developing and supporting this plugin.</p>
				<p>If you like it, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>
			</div>
		<?php
	}
}