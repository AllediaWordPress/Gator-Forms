<?php 
/**
 * @version 1.0.0
 * @package Perfect Ajax Popup Contact Form
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;


function pwebcontact_install() {
    
	pwebcontact_install_db();
}

function pwebcontact_uninstall() {
    
	pwebcontact_uninstall_db();
}

// create database table for contact forms settings
function pwebcontact_install_db() {
    
	global $wpdb;
	global $charset_collate;
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$sql = 
	"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}pwebcontact_forms` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `title` varchar(100) NOT NULL,
      `publish` tinyint(1) NOT NULL DEFAULT '1',
      `position` varchar(20) NOT NULL DEFAULT 'footer',
      `layout` varchar(20) NOT NULL DEFAULT 'slidebox',
      `modify_date` datetime NOT NULL,
      `params` text,
	  PRIMARY KEY (`id`)
	) $charset_collate AUTO_INCREMENT=1;";
    
	dbDelta( $sql );
}


function pwebcontact_uninstall_db() {
    
	global $wpdb;
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}pwebcontact_forms`;";
    
	dbDelta( $sql );
}

function pwebcontact_check_db() {
    
	global $wpdb;
    
    $show = $wpdb->hide_errors();
    
    try {
        if (false === $wpdb->query( 'SELECT `id` FROM `'.$wpdb->prefix.'pwebcontact_forms` LIMIT 1' )) {
            pwebcontact_install_db();
        }
    } catch (Exception $e) {
        
    }
    
    $wpdb->show_errors($show);
}