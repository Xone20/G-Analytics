<?php

/*
Plugin Name: G-Analytics
URI: http://www.xcode.com/
Version: 1.0
Author: Luigi Xone
Author URI: http://www.xcode.com/
Description: Simple Google Analytics Tracking Code for your WordPress site.
*/


// Security
if(!defined('ABSPATH')) exit;
if(defined('WP_INSTALLING') && WP_INSTALLING) {return;}


// Add css file
add_action('admin_init','style');
function style() {
   wp_register_style('style', plugins_url('css/style.css',__FILE__ ));
   wp_enqueue_style('style');
}


// We'll key on the slug for the settings page so set it here so it can be used in various places
define('MY_PLUGIN_SLUG', 'g_analytics-option');


// Register a callback for our specific plugin's actions
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'g_analytics_action_links');
function g_analytics_action_links( $links ) {
   $links[] = '<a href="'. menu_page_url(MY_PLUGIN_SLUG, false) .'">Settings</a>';
   return $links;
}


// Create a normal admin menu
if (is_admin() ){
  add_action('admin_menu', 'register_settings');
  add_action('admin_init', 'g_analytics_register_settings' );
}

// Register Settings
function register_settings() {
   add_options_page('G-Analytics Settings', 'G-Analytics', 'manage_options', MY_PLUGIN_SLUG, 'g_analytics_settings_page');

    global $submenu;
    if( array_key_exists('g-analytics-option' , $submenu))
    {
        foreach($submenu['g-analytics-option'] as $k => $v)
        {
            if( MY_PLUGIN_SLUG === $v[2] )
            {
                unset($submenu['g-analytics-option'][$k]);
            }
        }
    }
}


// FUNCTION REGISTER SETTINGS
function g_analytics_register_settings() {
  register_setting('g_analytics_options_group', 'g_analytics_option_code');
  register_setting('g_analytics_options_group', 'g_analytics_option_anonymize');
}


// This is our plugins settings page
function g_analytics_settings_page() {
?>
  <div>
  <?php screen_icon(); ?>
  <h1>G-Analytics - General Settings</h1>
  <br />
  <form method="post" action="<?php echo esc_url( ('options.php') ); ?>" class="g-form">
  <?php settings_fields('g_analytics_options_group'); ?>
  <div class="form-check">
  <label class="form-check-label">GA Track Code:</label>
    <input class="form-check-input" type="text" name="g_analytics_option_code" value="<?php echo esc_attr( get_option('g_analytics_option_code') ); ?>">
  </div>

  <?php submit_button(); ?>
  </form>
  </div>

<?php
}

// Main pubblic plugin function
function g_analytics_code() {	
?>
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', '<?php echo esc_attr( get_option('g_analytics_option_code') ); ?>', 'auto');		
            ga('set', 'anonymizeIp', true);		
			ga('send', 'pageview');
    </script>
<?php
}
add_action('wp_head', 'g_analytics_code');
?>