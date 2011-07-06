<?php
if(!class_exists('WYSIWYG_Widgets_Admin')) {

	require_once('dvk-plugin-admin.php');

	class WYSIWYG_Widgets_Admin extends DvK_Plugin_Admin{
		
		var $hook 		= 'wysiwyg-widgets';
		var $longname	= 'WYSIWYG Widgets Configuration';
		var $shortname	= 'WYSIWYG Widgets';
		var $plugin_url = 'http://dannyvankooten.com/wordpress-plugins/wysiwyg-widgets/';
		var $optionname = 'wysiwyg_widgets_options';
		var $filename	= 'wysiwyg-widgets/wysiwyg-widgets.php';	

		function __construct()
		{
			parent::__construct();
			global $pagenow;
			
			// Only load stuff on widgets page
			if($pagenow == 'widgets.php') {
				$this->add_hooks();
				$this->check_usage_time();
			} 
		}
		
		function add_hooks()
		{
			add_action("admin_head",array(&$this,"load_tiny_mce"));
			add_action('admin_init',array(&$this,"load_scripts"));
			add_action('admin_footer',array(&$this,'add_overlay'));
			
			if((int) get_bloginfo('version') >= 3.2) {
			// wp_tiny_mce_preload_dialogs has been replaced by wp_preload_dialogs
			// the_editor containts wp_quicktags which calls wp_preload_dialogs to load the link dialog
			// so it's no longer necessary to manually add this function to the footer.
				add_action('admin_footer', 'wp_tiny_mce_preload_dialogs');
			}
		}
		
		/* Load scripts and styles for plugin usage */
		function load_scripts()
		{
			// scripts
			wp_enqueue_script(array(
				'jquery',
				'editor',
				'thickbox',
				'media-upload'
			)); 
			wp_enqueue_script('wysiwyg-widgets', plugins_url('/js/wysiwyg-widgets.js',dirname(__FILE__)));
			
			// styles
			wp_enqueue_style('thickbox');
			wp_enqueue_style('wysiwyg-widgets', plugins_url('/css/wysiwyg-widgets-backend.css',dirname(__FILE__)));

		}
		
		/* Load the necessary tinymce and thickbox scripts */
		function load_tiny_mce()
		{
			if(function_exists('wp_tiny_mce')) {		
				remove_all_filters('mce_external_plugins');
				wp_tiny_mce( false );
			}
		}
		
		/**
		* Add the overlay that holds the tinymce editor, this goes hooked to admin_footer
		*/
		function add_overlay()
		{
			?>
			<div id="wysiwyg-widgets-overlay-bg"></div>
				<div id="wysiwyg-widgets-window">
					<div id="wysiwyg-widgets-title">
						<div class="title">WYSIWYG Widgets - Editor</div>
						<div class="close">
							<img src="<?php bloginfo('wpurl'); ?>/wp-includes/js/thickbox/tb-close.png" alt="X" title="Close overlay, discards changes"/>
						</div>
					</div>
					<div id="wysiwyg-widgets-content">
						<?php the_editor('','wysiwyg-textarea'); ?>
						<p>
							<input id="wysiwyg-send-to-widget" class="button-primary alignright" type="submit" value="<?php _e('Send to widget'); ?>" />
							<br style="clear:both;" />
						</p>
					</div>
				</div>
			<?php 	if(isset($this->actions['show_donate_box']) && $this->actions['show_donate_box']) { $this->donate_popup(); } 
		}
		
		/* Override the donate box that is shown after 30 days of usage */
		function donate_popup()
		{
			?>
			<div id="dvk-donate-box">
					<div id="dvk-donate-box-content">
						<img width="16" height="16" class="dvk-close" src="<?php echo plugins_url('/img/close.png',dirname(__FILE__)); ?>" alt="X">
						<h3>Support me</h3>
						<p>I noticed you've been using <?php echo $this->shortname; ?> for at least 30 days, would you like to show me a token of your appreciation by buying me a beer or tweet about <?php echo $this->shortname; ?>?</p>
						
						<table>
							<tr>
								<td>
								<form id="dvk_donate" target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
									<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/nl_NL/i/scr/pixel.gif" width="1" height="1">
								</form>
								</td>
								<td>
									<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $this->plugin_url; ?>" data-text="Showing my appreciation to @DannyvanKooten for his awesome #WordPress plugin: <?php echo $this->shortname; ?>" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
								</td>
							</tr>
						</table>
						<a class="dvk-dontshow" href="widgets.php?dontshowpopup=1">(do not show me this pop-up again)</a>
					</div>
				</div>
				<?php
		}
		
		function add_option_page(){}
		function add_admin_styles(){}
		function add_admin_scripts(){}
		function add_settings_link($links) { 
			$settings_link = '<a href="http://dannyvankooten.com">DannyvanKooten.com</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}

	}
	
	
	
	
	
}