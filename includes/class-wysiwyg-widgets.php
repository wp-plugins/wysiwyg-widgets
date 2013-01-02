<?php

class WYSIWYG_Widgets
{

	public function init()
	{
		add_action('init', array($this, 'on_init_action'));
		add_action( 'widgets_init', array($this, 'register_widget'));
	}

	public function on_init_action()
	{
		$labels = array(
		    'name' => 'WYSIWYG Widget',
		    'singular_name' => 'WYSIWYG Widget',
		    'add_new' => 'Add New',
		    'add_new_item' => 'Add New WYSIWYG Widget',
		    'edit_item' => 'Edit Widget',
		    'new_item' => 'New Widget',
		    'all_items' => 'All Widgets',
		    'view_item' => 'View  Widget',
		    'search_items' => 'Search Widgets',
		    'not_found' =>  'No widgets found',
		    'not_found_in_trash' => 'No widgets found in Trash', 
		    'parent_item_colon' => '',
		    'menu_name' => ' WYSIWYG Widgets'
		  );
		$args = array(
			'public' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'labels' => $labels,
			'has_archive' => false,
			'supports' => array('title', 'editor')
		);
   		register_post_type( 'wysiwyg-widget', $args );
	}

	public function register_widget()
	{
		register_widget('WYSIWYG_Widgets_Widget');  
	}
}