<?php

if(!class_exists('WYSIWYG_Widgets')) {

	class WYSIWYG_Widgets {
			
		public function __construct()
		{
			$this->add_hooks();
		}

		/**
		* Add WP filters and actions according where necessary
		*/
		function add_hooks()
		{
			add_action('widgets_init',array(&$this,'register_widget'));
		}
		
		/** Register the WYSIWYG Widgets Widget */
		function register_widget()
		{
			return register_widget('WYSIWYG_Widget');
		}
		
	}

}