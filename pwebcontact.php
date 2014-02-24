<?php 
/**
 * Plugin Name: Perfect Ajax Popup Contact Form
 * Plugin URI: http://www.perfect-web.co/wordpress/ajax-contact-form-popup
 * Description: 
 * Version: 1.0.0
 * Author: Piotr Moćko
 * Author URI: http://www.perfect-web.co
 * License: GPLv3
 */

// No direct access
function_exists('add_action') or die;


if ( is_admin() ) {
    
    require_once dirname( __FILE__ ) . '/install.php';
    require_once dirname( __FILE__ ) . '/admin.php';
    
    register_activation_hook( __FILE__, 'pwebcontact_install' );
    register_uninstall_hook( __FILE__, 'pwebcontact_uninstall' );
} 
else {
    
    require_once dirname( __FILE__ ) . '/shortcode.php';
}

require_once dirname( __FILE__ ) . '/widget.php';