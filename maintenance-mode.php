<?php
/*
 * Plugin Name: DP Maintenance Mode
 * Plugin URI: https://johngreenfield.dev/plugins/dp-maintenance-mode
 * Description: DP Maintenance Mode is a versatile WordPress plugin designed to simplify the process of enabling maintenance mode on your website.
 * Version: 1.0.0
 * Author: John Greenfield
 * Author URI: https://johngreenfield.dev
 * License: GPL3
 *
 * @package dp-maintenance-mode
 * @copyright Copyright (c) 2024, John Greenfield
 * @license GPL3+
 * 
 * Some code modified from https://github.com/lukasjuhas/lj-maintenance-mode
 * 
*/

// Define constants
define("DPMM_VERSION","1.0.0");
define('DPMM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DPMM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DPMM_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('DPMM_PLUGIN_DOMAIN', 'dp-maintenance-mode');
define('DPMM_VIEW_SITE_CAP', 'dpmm_view_site');
define('DPMM_PLUGIN_CAP', 'dpmm_control');

class dp_maintenance_mode {
    // Holds Social profiles. You can add more in __construct() function.
    public $social = array();
    
    // Holds the values to be used in the fields callbacks
    private $options;
    
    // Construct
    public function __construct() 
    {
        // Setup list of social networks
        $this->social = array(
            'bandcamp' => __('Bandcamp'),
            'bitbucket' => __('BitBucket'),
            'deviantart' => __('DeviantArt'),
            'etsy' => __('Etsy'),
            'facebook' => __('Facebook'),
            'free-code-camp' => __('Free Code Camp'),
            'github' => __('GitHub'),
            'instagram' => __('Instagram'),
            'lastfm' => __('LastFM'),
            'linkedin' => __('LinkedIn'),
            'medium' => __('Medium'),
            'pinterest' => __('Pinterest'),
            'reddit' => __('Reddit'),
            'soundcloud' => __('SoundCloud'),
            'stack-exchange' => __('Stack Exchange'),
            'steam' => __('Steam'),
            'tumblr' => __('Tumblr'),
            'twitch' => __('Twitch'),
            'twitter' => __('Twitter'),
            'vimeo' => __('Vimeo'),
            'wordpress' => __('WordPress'),
            'youtube' => __('YouTube'),
        );

        // Setup settings page
        add_action('admin_menu', array($this,'add_settings_page'));
        add_action('admin_init', array($this, 'settings'));
        add_action('admin_init', array($this, 'manage_capabilities'));
        // Hook into 'wp_loaded' and add maintenance mode
        add_action('wp_loaded', array($this, 'maintenance_mode'));
        // Add action links to plugins page
        add_filter('plugin_action_links_' . DPMM_PLUGIN_BASENAME, array($this, 'action_links'));
        // Add before maintenance mode
        add_action('dpmm_before_mm', array($this, 'before_maintenance_mode'));
        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_files'));
        // Add shortcode support
        add_filter('dpmm_content', 'do_shortcode', 11);
        // Add image upload support
        add_action('wp_ajax_dpmm_get_image', array($this, 'dpmm_get_image'));
    }

    // Display Settings page
    public function display_settings_page() 
    {
        if ( file_exists( DPMM_PLUGIN_DIR . 'views/settings.php' ) ) {
            require_once( DPMM_PLUGIN_DIR . 'views/settings.php' );
        }
    }

    // Add Submenu page for settings
    public function add_settings_page()
    {
        add_submenu_page('options-general.php', __('Maintenance Mode', DPMM_PLUGIN_DOMAIN), __('Maintenance Mode', DPMM_PLUGIN_DOMAIN), $this->get_relevant_cap(), 'dp-maintenance-mode', array($this, 'display_settings_page'));
    }

    // Register our settings and set the default content
    public function settings()
    {
        register_setting('dpmm', 'dpmm-enabled');
        register_setting('dpmm', 'dpmm-content');
        register_setting('dpmm', 'dpmm_code_snippet');
        register_setting('dpmm', 'dpmm-site-title');
        register_setting('dpmm', 'dpmm-roles');
        register_setting('dpmm', 'dpmm-mode');
        register_setting('dpmm', 'dpmm-image-id');
        register_setting('dpmm', 'dpmm-social-profiles', array($this, 'sanitize_profiles'));

        // set the default content
        $this->dpmm_set_content();
    }

    // Get warnings and other messages
    public function dpmm_get_messages($type) 
    {
        switch ($type) {
            case "maintenance_message":
                $message = __("<h1>Under Maintenance</h1><p>The website is currently undergoing scheduled maintenance, please check back soon.</p>",DPMM_PLUGIN_DOMAIN);
                break;
            case "construction_message":
                $message = __("<h1>Under Construction</h1><p>We're currently making something awesome, please check back soon.</p>",DPMM_PLUGIN_DOMAIN);
                break;
            case 'warning_wp_rocket':
                $message = __("Important: Don't forget to flush your cache using WP Rocket when enabling or disabling Maintenance Mode.", DPMM_PLUGIN_DOMAIN);
                break;
            case 'warning_wp_super_cache':
                $message = __("Important: Don't forget to flush your cache using WP Super Cache when enabling or disabling Maintenance Mode.", DPMM_PLUGIN_DOMAIN);
                break;
            case 'warning_w3_total_cache':
                $message = __("Important: Don't forget to flush your cache using W3 Total Cache when enabling or disabling Maintenance Mode.", DPMM_PLUGIN_DOMAIN);
                break;
            case 'warning_comet_cache':
                $message = __("Important: Don't forget to flush your cache using Comet Cache when enabling or disabling Maintenance Mode.", DPMM_PLUGIN_DOMAIN);
                break;
            case "dpmm_enabled":
                $message = __("Maintenance Mode is currently enabled.",DPMM_PLUGIN_DOMAIN);
                break;
            case "dpmm_disabled":
                $message = __("Maintenance Mode is currently disabled.",DPMM_PLUGIN_DOMAIN);
                break;
            default:
                $message = false;
        }

        return $message;
    }

    // Set the default content
    public function dpmm_set_content()
    {
        $content = get_option('dpmm-content');
        $mode = get_option('dpmm-mode');

        // If mode is not set, set the default mode.
        if (empty($mode)) {
            update_option('dpmm-mode', 'default');
        }

        // If content is not set, set the default content.
        if (empty($content)) {
            $content = $this->dpmm_get_messages('maintenance_message');
            update_option('dpmm-content', stripslashes($content));

            switch($mode):
                case 'con':
                    $content = $this->dpmm_get_messages('construction_message');
                    update_option('dpmm-content', stripslashes($content));
                    break;
                default:
                    $content = $this->dpmm_get_messages('maintenance_message');
                    update_option('dpmm-content', stripslashes($content));
            endswitch;
        }
    }

    // Enqueue scripts for WP Media Manager
    public function enqueue_files() {
        global $pagenow;

        if( $pagenow == 'options-general.php' ) {
            // Enqueue WordPress media scripts
            wp_enqueue_media();
            // Enqueue custom script that will interact with wp.media
            wp_enqueue_script('dpmm_admin_script', DPMM_PLUGIN_URL . 'assets/js/app.js', array('jquery'), DPMM_VERSION, false );
            // Enqueue admin css
            wp_enqueue_style('dpmm_admin_style', DPMM_PLUGIN_URL . 'assets/css/admin.css', array(), DPMM_VERSION, false);
        }
    }

    // Check if the current page is the login or registration page
    public function is_login_page() {
        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-signup.php'));
    }

    // Ajax action to refresh media image
    public function dpmm_get_image() {
        if(isset($_GET['id']) ){
            if($_GET['id'] > 0) {
                $image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'dpmm-preview-image' ) );
                $data = array(
                    'image'    => $image,
                );
            } else {
                $image = '<img id="dpmm-preview-image" src="' . DPMM_PLUGIN_URL . 'assets/images/placeholder.png" />';
                $data = array(
                    'image'    => $image,
                );
            }
            wp_send_json_success( $data );
        } else {
            wp_send_json_error();
        }
    }

    // Sanitize each setting field as needed.
    public function sanitize_profiles( $input ) {
        $new_input = array();
        // Sanitize Social Profiles values.
        foreach ( (array) $input as $name => $element ) {
            foreach ( $element as $index => $value ) {
                if ( ! empty( $value ) ) {
                    $new_input[ $name ][ $index ] = esc_url( $value );
                }
            }
        }
        return $new_input;
    }

    // Add action links to listing on Plugins page
    public function action_links($links)
    {
        $links[] = '<a href="' . get_admin_url(null, 'options-general.php?page=dp-maintenance-mode') . '">' . _x('Settings', 'Plugin Settings link', DPMM_PLUGIN_DOMAIN) . '</a>';

        return $links;
    }

    // Default site title for maintenance mode
    public function site_title()
    {
        return apply_filters('dpmm_site_title', get_bloginfo('name') . ' | ' . __('Under Maintenance', DPMM_PLUGIN_DOMAIN));
    }

    // Manage capabilities
    public function manage_capabilities()
    {
        $wp_roles = get_editable_roles();
        $all_roles = get_option('dpmm-roles');

        // extra checks
        if ($wp_roles && is_array($wp_roles)) {
            foreach ($wp_roles as $role => $role_details) {
                $get_role = get_role($role);

                if (is_array($all_roles) && array_key_exists($role, $all_roles)) {
                    $get_role->add_cap(DPMM_VIEW_SITE_CAP);
                } else {
                    $get_role->remove_cap(DPMM_VIEW_SITE_CAP);
                }
            }
        }

        // administrator by default
        $admin_role = get_role('administrator');
        $admin_role->add_cap(DPMM_VIEW_SITE_CAP);
        $admin_role->add_cap(DPMM_PLUGIN_CAP);
    }

    // Get mode
    public function get_mode()
    {
        $mode = get_option('dpmm-mode');
        if ($mode == 'con') {
            // construction mode
            return 200;
        }

        // maintenance mode
        return 503;
    }

    // Get content and custom stylesheet
    public function get_content()
    {
        $get_content = get_option('dpmm-content');
        $content = (!empty($get_content)) ? $get_content : $this->dpmm_get_messages('maintenance_message');
        $content = apply_filters('wptexturize', $content);
        $content = apply_filters('wpautop', $content);
        $content = apply_filters('shortcode_unautop', $content);
        $content = apply_filters('prepend_attachment', $content);
        $content = apply_filters('wp_make_content_images_responsive', $content);
        $content = apply_filters('convert_smilies', $content);
        $content = apply_filters('dpmm_content', $content);

        // add custom stylesheet
        $stylesheet = $this->custom_stylesheet();

        return $stylesheet .$content;
    }

    // Get code snippet
    public function get_snippet() 
    {
        // Get custom code snippets
        $code = get_option('dpmm_code_snippet');

        return $code;
    }

    // Get the site title
    public function get_title()
    {
        $site_title = get_option('dpmm-site-title');
        return $site_title ? $site_title : $this->site_title();
    }

    // Get CSS filename
    public function get_css_filename()
    {
        return apply_filters('dpmm_css_filename', 'maintenance.css');
    }

    // Check for custom stylesheet
    public function get_custom_stylesheet_url()
    {
        $stylesheet_url = false;

        $url_filename = $this->get_css_filename();

        if (!validate_file($url_filename)) {
            $url = apply_filters('dpmm_css_url', get_stylesheet_directory() . '/' . $url_filename);

            if (file_exists($url)) {
                $stylesheet_url = $url;
            }
        }

        return $stylesheet_url;
    }

    // Set Custom stylsheet
    public function custom_stylesheet()
    {
        $stylesheet = '';
        $url = $this->get_custom_stylesheet_url();

        if ($url) {
            $stylesheet = '<style type="text/css">' . file_get_contents($url) . '</style>';
        }

        return $stylesheet;
    }

    // Editor content
    public function editor_content()
    {
        $content = get_option('dpmm-content');
        $editor_id = 'dpmm-content';
        $settings  = array( 'media_buttons' => true );
        wp_editor($content, $editor_id, $settings);
    }

    // Before maintenance mode
    public function before_maintenance_mode()
    {
        // remove jetpack sharing
        remove_filter('the_content', 'sharing_display', 19);
    }

    // Is maintenance mode enabled?
    public function enabled()
    {
        // enabled
        if (get_option('dpmm-enabled') || isset($_GET['dpmm']) && $_GET['dpmm'] == 'preview') {
            return true;
        }

        // disabled
        return false;
    }

    // Maintenance Mode
    public function maintenance_mode()
    {
        if (!$this->enabled()) {
            return false;
        }

        do_action('dpmm_before_mm');

        if (!$this->is_login_page() && (!(current_user_can(DPMM_VIEW_SITE_CAP) || current_user_can('manage_options')) || (isset($_GET['dpmm']) && $_GET['dpmm'] == 'preview'))) {
            if (get_option('dpmm-mode') == 'con') {
                if ( file_exists( DPMM_PLUGIN_DIR . 'views/maintenance.php' ) ) {
                    require_once( DPMM_PLUGIN_DIR . 'views/maintenance.php' );
                }
                die();
            } else {
                wp_die($this->get_content() . $this->get_snippet(), $this->get_title(), ['response' => $this->get_mode()]);
            }
            
        }
    }

    // Get relevant capability
    public function get_relevant_cap()
    {
        return is_super_admin() ? 'delete_plugins' : DPMM_PLUGIN_CAP;
    }

    // Notify if cache plugin detected
    public function notify()
    {
        $cache_plugin_enabled = $this->cache_plugin();
        if (!empty($cache_plugin_enabled)) {
            $class = 'error';
            $message = $this->cache_plugin();
            if (isset($_GET['settings-updated'])) {
                echo '<div class="' . $class . '"><p>' . $message . '</p></div>';
            }
        }
    }

    // Detect caching plugins
    public function cache_plugin()
    {
        $message = '';
        // add wp rocket support
        if (in_array('wp-rocket/wp-rocket.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $message = $this->dpmm_get_messages('warning_wp_rocket');
        }

        // add wp super cache support
        if (in_array('wp-super-cache/wp-cache.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $message = $this->dpmm_get_messages('warning_wp_super_cache');
        }

        // add w3 total cache support
        if (in_array('w3-total-cache/w3-total-cache.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $message = $this->dpmm_get_messages('warning_w3_total_cache');
        }

        // add comet cache support
        if (in_array('comet-cache/comet-cache.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $message = $this->dpmm_get_messages('warning_comet_cache');
        }

        return $message;
    }
}

$dpMaintenanceMode = new dp_maintenance_mode();