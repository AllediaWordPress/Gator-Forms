<?php
/**
 * @version 1.0.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license Perfect Web License http://www.perfect-web.co/license
 * @author Piotr Moćko
 */

define( 'DOING_AJAX', true );

// Load WordPress
require_once( dirname(dirname(dirname( dirname( __FILE__ ) ))) . '/wp-load.php' );

// The content-type header.
PWebContact::setHeader('Content-Type', 'application/json; charset='.get_bloginfo('charset'));

// Expires in the past.
PWebContact::setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT');

// Always modified.
PWebContact::setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
PWebContact::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

// HTTP 1.0
$this->setHeader('Pragma', 'no-cache');

// Response
$response = new stdClass();
$response->success = false;
$response->message = null;
$response->data = null;

if (isset($_GET['action']) AND $_GET['action'])
{
    $action = preg_filter('/[^a-zA-Z0-9_]/', '', $_GET['action']);
    
    if (in_array($action, array('sendEmail', 'uploader', 'checkCaptcha', 'getToken')) 
            AND method_exists('PWebContact', $action.'Ajax'))
    {
        // Call action
        try
        {
            $response->data = call_user_func('PWebContact::' . $action . 'Ajax');
            $response->success = true;
        }
        catch (Exception $e)
        {
            $response->message = $e->getMessage();
        }
    }
    else 
    {
        // Method does not exist
        $response->message = sprintf('Action %s does not exist', $action);
    }
}
else 
{
    $response->message = 'Incorrect ajax call';
}

// Send headers
foreach (PWebContact::getHeaders() as $header => $value) {
    header($header . ($value ? ': '.$value : ''));
}

// Output response
die( json_encode($response) );