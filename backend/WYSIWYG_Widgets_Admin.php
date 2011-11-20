<?php
if (!class_exists('WYSIWYG_Widgets_Admin')) {

    class WYSIWYG_Widgets_Admin {

        var $hook = 'wysiwyg-widgets';
        var $longname = 'WYSIWYG Widgets';
        var $shortname = 'WYSIWYG Widgets';
        var $plugin_url = 'http://dannyvankooten.com/wordpress-plugins/wysiwyg-widgets/';
        var $filename = 'wysiwyg-widgets/wysiwyg-widgets.php';
        private $version = '1.1';

        function __construct() {
            // Add settings link to plugin page
            add_filter("plugin_action_links_{$this->filename}", array(&$this, 'add_settings_link'));
            
            // Add DvK.com dashboard widget
            add_action('wp_dashboard_setup', array(&$this, 'widget_setup'));
            
            // Remove options upon deactivation
            register_deactivation_hook($this->filename, array(&$this, 'remove_options'));

            global $pagenow;

            // Only do stuff on widgets page
            if ($pagenow == 'widgets.php') {
                $this->check_usage_time();
                $this->add_hooks();
            }
        }
        
        /**
         * This function is called on the admin widget page
         * Adds the necessary hooks
         */
        function add_hooks() {
            add_action("admin_head", array(&$this, "load_tiny_mce"));
            add_action('admin_print_scripts', array(&$this, "load_scripts"));
            add_action('admin_print_styles', array(&$this, 'load_styles'));
            add_filter('tiny_mce_before_init', array(&$this, 'initialize_editor'), 20);
            
            // WP 3.2 and below don't have the wp_preload_dialogs function
            if((float) get_bloginfo('version') < 3.2) {
		add_action('admin_footer', 'wp_tiny_mce_preload_dialogs');
            } else {
                add_action('admin_footer', array(&$this, 'tinymce_preload_dialogs'));
            }
            
            if (isset($this->actions['show_donate_box']) && $this->actions['show_donate_box']) {
                add_action('admin_footer', array(&$this, 'donate_popup'));
            }
        }
        
        /**
         * Alters some default TinyMCE Settings
         * Removes the wpfullscreen plugin so clicking the full-screen button activates the default TinyMCE Full-screen omde
         * Removes the more button, since it's useless for widgets.
         * @param array $settings The settings array
         * @return array The altered settings array
         */
        function initialize_editor($settings) {
            // Remove WP fullscreen mode and set the native tinyMCE fullscreen mode
            $plugins = explode(',', $settings['plugins']);

            if (isset($plugins['wpfullscreen'])) {
                unset($plugins['wpfullscreen']);
            }
            if (!isset($plugins['fullscreen'])) {
                $plugins[] = 'fullscreen';
            }
            $settings['plugins'] = join(',', $plugins);
            $settings['theme_advanced_buttons1'] = str_replace(',wp_more', '', $settings['theme_advanced_buttons1']);
            return $settings;
        }

      
        /**
         * Loads the necessary javascript files
         */
        function load_scripts() {
            add_thickbox();
            wp_enqueue_script('media-upload');
            wp_enqueue_script('wysiwyg-widgets', plugins_url('/backend/js/wysiwyg-widgets.js', dirname(__FILE__)), array('jquery', 'editor', 'thickbox', 'media-upload'), $this->version);
        }
        
        /**
         * Loads the necessary stylesheet files
         */
        function load_styles() {
            wp_enqueue_style('thickbox');
            wp_enqueue_style('wysiwyg-widgets', plugins_url('/backend/css/wysiwyg-widgets.css', dirname(__FILE__)));
        }

        /**
         * Load TinyMCE
         * Workaround for people using Dean's FCK Editor
         * @global object $current_user The current user
         */
        function load_tiny_mce() {
            $temp_changed_rich_editing = false;

            if (!function_exists('wp_tiny_mce'))
                include_once( ABSPATH . 'wp-admin/includes/post.php' );

            // Deans FCKeditor workaround
            if (is_plugin_active('fckeditor-for-wordpress-plugin/deans_fckeditor.php')) {

                global $current_user;
                $current_user = wp_get_current_user();

                $old_rich_editing_value = get_user_option('rich_editing', $current_user->id);

                // Change rich_editing option
                update_user_option($current_user->id, 'rich_editing', 'true', true);
                $temp_changed_rich_editing = true;
            }

            remove_all_filters('mce_external_plugins');
            wp_tiny_mce(false);

            // if rich_editing value has been changed, reset it.
            if ($temp_changed_rich_editing) {
                update_user_option($current_user->id, 'rich_editing', $old_rich_editing_value, true);
            }
        }

        /**
         * Preload some TinyMCE dialogs, to get them working.
         */
        function tinymce_preload_dialogs() {
            // wp_preload_dialogs does not exist in WP 3.1 and below
            if(function_exists('wp_preload_dialogs')) wp_preload_dialogs(array('plugins' => 'wpdialogs,wplink,wpfullscreen'));
        }

        /**
         * This is called when someone has been using the plugin for over 30 days
         * Renders a pop-up asking for a tweet or donation.
         */
        function donate_popup() {
            ?>
            <div id="dvk-donate-box">
                <div id="dvk-donate-box-content">
                    <img width="16" height="16" class="dvk-close" src="<?php echo plugins_url('/backend/img/close.png', dirname(__FILE__)); ?>" alt="X">
                    <h3>Like WYSIWYG Widgets?</h3>
                    <p>I noticed you've been using <?php echo $this->shortname; ?> for at least 30 days. This plugin cost me countless hours of work. If you use it, please donate a token of your appreciation!</p>

                    <form id="dvk_donate" target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
                        <input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/nl_NL/i/scr/pixel.gif" width="1" height="1">
                    </form>
                    <p>Alternatively, tweet about it so others find out about WYSIWYG Widgets.</p>

                    <div style="margin:10px 0; text-align:center;">
                        <a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $this->plugin_url; ?>" data-text="Showing my appreciation to @DannyvanKooten for his awesome #WordPress plugin: <?php echo $this->shortname; ?>" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                    </div>

                    <a class="dvk-dontshow" href="widgets.php?dontshowpopup=1">(do not show me this pop-up again)</a>
                </div>
            </div>
            <?php
        }
        
        /**
         * Add link to DannyvanKooten.com to plugins overview page
         * @param array $links
         * @return array 
         */
        function add_settings_link($links) {
            $settings_link = '<a href="http://dannyvankooten.com">DannyvanKooten.com</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        /**
         * Check how long plugin has been installed on this blog
         * If over 30 days, show a pop-up asking for a donation
         * @return null 
         */
        function check_usage_time() {
            $opts = get_option('ww_options');

            // First-time use? (option does not exist)
            if (!$opts) {
                $opts['date_installed'] = strtotime('now');
                update_option('ww_options', $opts);
                return;
            }

            // User clicked don't show pop-up link, update option.
            if (isset($_GET['dontshowpopup']) && $_GET['dontshowpopup'] == 1) {
                $opts['dontshowpopup'] = 1;
                update_option('ww_options', $opts);
                return;
            }

            // Over 30 days? Not set to don't show? Show the damn thing.
            if (!isset($opts['dontshowpopup']) && $opts['date_installed'] < strtotime('-30 days')) {
                // plugin has been installed for over 30 days
                $this->actions['show_donate_box'] = true;
                wp_enqueue_style('dvk_donate', plugins_url('/backend/css/donate.css', dirname(__FILE__)));
                wp_enqueue_script('dvk_donate', plugins_url('/backend/js/donate.js', dirname(__FILE__)));
            }
        }

        function remove_options() {
            delete_option('ww_options');
        }
        
        /**
         * Adds the DvK.com dashboard widget, if user didn't remove it before.
         * @return type 
         */
        function dashboard_widget() {
            $options = get_option('dvkdbwidget');
            if (isset($_POST['dvk_removedbwidget'])) {
                $options['dontshow'] = true;
                update_option('dvkdbwidget', $options);
            }

            if (isset($options['dontshow']) && $options['dontshow']) {
                echo "If you reload, this widget will be gone and never appear again, unless you decide to delete the database option 'dvkdbwidget'.";
                return;
            }

            require_once(ABSPATH . WPINC . '/rss.php');
            if ($rss = fetch_rss('http://feeds.feedburner.com/dannyvankooten')) {
                echo '<div class="rss-widget">';
                echo '<a href="http://dannyvankooten.com/" title="Go to DannyvanKooten.com"><img src="http://static.dannyvankooten.com/images/dvk-64x64.png" class="alignright" alt="DannyvanKooten.com"/></a>';
                echo '<ul>';
                $rss->items = array_slice($rss->items, 0, 3);
                foreach ((array) $rss->items as $item) {
                    echo '<li>';
                    echo '<a target="_blank" class="rsswidget" href="' . clean_url($item['link'], $protocolls = null, 'display') . '">' . $item['title'] . '</a> ';
                    echo '<span class="rss-date">' . date('F j, Y', strtotime($item['pubdate'])) . '</span>';
                    echo '<div class="rssSummary">' . $this->text_limit($item['summary'], 250) . '</div>';
                    echo '</li>';
                }
                echo '</ul>';
                echo '<div style="border-top: 1px solid #ddd; padding-top: 10px; text-align:center;">';
                echo '<a target="_blank" style="margin-right:10px;" href="http://feeds.feedburner.com/dannyvankooten"><img src="' . get_bloginfo('wpurl') . '/wp-includes/images/rss.png" alt=""/> Subscribe by RSS</a>';
                echo '<a target="_blank" href="http://dannyvankooten.com/newsletter/"><img src="http://static.dannyvankooten.com/images/email-icon.png" alt=""/> Subscribe by email</a>';
                echo '<form class="alignright" method="post"><input type="hidden" name="dvk_removedbwidget" value="true"/><input title="Remove this widget" type="submit" value=" X "/></form>';
                echo '</div>';
                echo '</div>';
            }
        }

        function widget_setup() {
            $options = get_option('dvkdbwidget');
            if (!$options['dontshow'])
                wp_add_dashboard_widget('dvk_db_widget', 'Latest posts on DannyvanKooten.com', array(&$this, 'dashboard_widget'));
        }
        
        /**
         * Helper function to format text in dashboard widget
         * @param string $text
         * @param int $limit
         * @param string $finish
         * @return string 
         */
        function text_limit($text, $limit, $finish = '...') {
            if (strlen($text) > $limit) {
                $text = substr($text, 0, $limit);
                $text = substr($text, 0, - ( strlen(strrchr($text, ' ')) ));
                $text .= $finish;
            }
            return $text;
        }

    }

}