<?php 
/*
Plugin Name: LNB MA Integrations
Plugin URI: http://www.leadsnearby.com
Description: Plugin for enabling Marketing Automation Integration Functionality
Version: 1.0.0
Author: Brian West
Author URI: http://www.leadsnearby.com
License: GPLv3
*/


/****************************************************************************
 * 
This plugin creates the conversion website functionality for the LeadsNearby 
Marketing Automation Program. Please define the endpoints in the Wordpress 
Dasboard to begin sending the webhooks on Phone Number Click and Form Fill

****************************************************************************/


// Begin Main Class
class LNB_MA_Integrations {
    
    public function __construct() {
        
        add_action( 'gform_after_submission', array($this , 'after_submission'), 10, 2 );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_the_scripts' ) );
        add_action( 'admin_menu', array($this , 'add_menupage') );
        add_action( 'wp_ajax_number_click', array( $this , 'prefix_ajax_number_click' ) );
        
    }
    
    function register_the_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'phone-click-conversion', plugins_url( '/js/phone-click-conversion.js',  __FILE__  ));
        wp_localize_script( 'phone-click-conversion', 'phone_num_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
         
    }
    
    function add_menupage() {
        add_options_page ( 
            'LNB MA Integrations', 
            'LNB MA Integrations', 
            'manage_options', 
            'ma_menu_page', 
            array($this, 'menu_page')
        );
    }
    
    public function menu_page() {
        global $wpdb;
        
        if (isset($_POST["submit"])) {
            update_option( 'lnb-ma-apikey', $_POST['lnb-ma-apikey'] );
            update_option( 'lnb-ma-form-endpoint', $_POST['lnb-ma-form-endpoint'] );
            update_option( 'lnb-ma-phone-endpoint', $_POST['lnb-ma-phone-endpoint'] );
        }
        
            ?> 
            
            <div class="lbt-blog-form">
                <br /><br />
                <img class="plugin_logo" style="width: 500px" src="https://media-exp2.licdn.com/media/AAEAAQAAAAAAAAt5AAAAJDZmZjBjODAwLTFlNGItNDRlNy04NmFmLWYxNjhmZmNjNjdmMA.png">
                <h1>LNB Marketing Automation Integrations Settings</h1>
                <br />
                <form id="ma-integrations-form" method="post"  >
                    <h4>API Key</h4> <input type="password"style="width: 400px;" name="lnb-ma-apikey" value="<?php echo get_option("lnb-ma-apikey"); ?>" /><br /> <br />
                    <h4>Form Conversion Endpoint</h4> <input type="text" style="width: 600px;" name="lnb-ma-form-endpoint" value="<?php echo get_option("lnb-ma-form-endpoint"); ?>"  /><br /> <br />
                    <h4>Phone Click Conversion Endpoint</h4> <input type="text" style="width: 600px;" name="lnb-ma-phone-endpoint" value="<?php echo get_option("lnb-ma-phone-endpoint"); ?>"  /><br /> <br />
                    <br />
                    <input type="submit" name="submit" value="Save Options"  />
                </form>
            </div>
            
        <?php

    }
    
    // Send Webhook on Form Submit 
    function after_submission($entry, $form) {
	    $cookie = $_COOKIE;
	    $post_url = get_option('lnb-ma-form-endpoint') . "?apikey=" . get_option('lnb-ma-apikey');
	    $body = array(
	        rgar( $entry, '1' ),
            rgar( $entry, '2' ),
            rgar( $entry, '3' ),
		    rgar( $entry, '4' ),
		    rgar( $entry, '5' ),
		    rgar( $entry, '6' ),
		    rgar( $entry, '7' ),
		    rgar( $entry, '8' ),
		    $cookie
		    );
	
	
    $response = wp_remote_post( $post_url, array( 'body' => $body ) );
	// GFCommon::log_debug( 'gform_after_submission: response => ' . print_r( $response, true ) );
	
    } 
    
    // Receive Phone Number Click AJAX Post and send webhook.
    function prefix_ajax_number_click() {
        $cookies = $_COOKIE;
        
        if (isset($cookies["utm_medium"])) {
            $url = get_option('lnb-ma-phone-endpoint');
            $apikey = get_option("lnb-ma-apikey");
            $full_request_url = $url . '?apikey=' . $apikey;
            $post_data = $_POST['data'];
            
            $response = wp_remote_post( $full_request_url, array(
                'method' => 'POST',
                'timeout' => 45,
	            'headers' => array(),
	            'body' => $post_data,
                )
                );

            wp_die("Success");
            
        } else {
            print_r($cookies);
            wp_die("Cookie Not Set");
        }
    }
    
    
}


$ma_integration = new LNB_MA_Integrations();

?>