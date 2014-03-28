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


add_shortcode('pwebcontact', 'pwebcontact_shortcode');

function pwebcontact_shortcode($atts, $content = null, $tag) 
{
	extract( shortcode_atts( array (
		'id' => 0
	), $atts ) );
	
	$output = '';
	
	//TODO load contact form by $id
	
	return $output;
}