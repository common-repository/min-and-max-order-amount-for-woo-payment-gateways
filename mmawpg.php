<?php
/*
Plugin Name: Min and Max Order Amount for Woo Payment Gateways
Plugin URI: https://mmawpg.wdraihan.com/
Description: Add minimum or maximum order amount to restricting/disable checkout/place order button based on WooCommerce payment gateways.
Version: 2.0.0
Author: Raihan
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: mmawpg
Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) )
     exit;

class MMAWPG_Lite_Min_Max_Amount {
    
    public function __construct(){
        
        define('MMAWPG_DIR', plugin_dir_url( __FILE__ ));
        define('MMAWPG_DIR_PATH', plugin_dir_path( __FILE__ ));
        define('MMAWPG_ASSETS', MMAWPG_DIR . 'assets/');
        
        //Register textdomain
        add_action( 'plugins_loaded', array($this, 'mmawpg_load_textdomain') );
        
        //mmawpg admin options
        require_once MMAWPG_DIR_PATH . 'includes/class-mmawpg-admin-options.php';
        
        if(get_option('mmawpg_enable_setting') == 'yes'){
            
            add_action('wp_enqueue_scripts', array($this, 'mmawpg_enqueue_all_scripts') );
            
            require_once MMAWPG_DIR_PATH . 'includes/class-mmawpg-functions.php';
        }
    }
    
    // Load plugin textdomain
    public function mmawpg_load_textdomain() {
      load_plugin_textdomain( 'mmawpg', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
    }
    
    //Enqueue scripts
    public function mmawpg_enqueue_all_scripts(){
        if(function_exists('is_checkout')){
            if(is_checkout()){
                wp_enqueue_script('mmawpg-script', MMAWPG_ASSETS.'js/mmawpg-scripts.js', array('jquery'), null, true);
                wp_localize_script( 'mmawpg-script', 'mmawpg_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
            }
        }
    }
}

/**
 * Check if WooCommerce is activated
 */
function mmawpg_lite_plugin_loaded(){
    
    if(function_exists('WC')){
        if( !class_exists('MMAWPG_Min_Max_Amount') ){
            new MMAWPG_Lite_Min_Max_Amount();
        }
    }else{
        
        function mmawpg_lite_woo_active_notice(){
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html__( 'Min and max order amount for WooCommerce payment gateways requires WooCommerce to be installed and active. You can download ', 'mmawpg' ); ?><a href="https://woocommerce.com/" target="_blank">WooCommerce</a> <?php echo esc_html__('here.','mmawpg'); ?></p>
            </div>
            <?php
        }
        if( !function_exists('mmawpg_woo_active_notice') ){
            add_action('admin_notices', 'mmawpg_lite_woo_active_notice');
        }
    }
}
add_action( 'plugins_loaded', 'mmawpg_lite_plugin_loaded' );