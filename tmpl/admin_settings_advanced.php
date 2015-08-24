<?php
/**
 * @version 2.1.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2015 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;

?>

<?php echo $this->_get_field(array(
	'type' => 'radio',
	'name' => 'feed',
	'group' => 'settings',
	'label' => 'Updates feed',
	'header' => 'Updates',
	'tooltip' => 'Display news and special offers from Perfect-Web.co website in administration panel of this extension.',
	'default' => 1,
	'class' => 'pweb-radio-group',
	'options' => array(
		array(
			'value' => 0,
			'name' => 'No'
		),
		array(
			'value' => 1,
			'name' => 'Yes'
		)
	)
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'dlid',
    'group' => 'settings',
    /*** FREE START ***/
    'readonly' => true,
    'desc' => sprintf(__('Enter Download ID which you can find at %s website, to get automatical updates if you have active PRO subscription. You have to first reinstall plugin to PRO version by yourself.', 'pwebcontact'), '<a href="https://www.perfect-web.co/login" target="_blank">Perfect-Web.co</a>'),
    /*** FREE END ***/
    /*** PRO START ***/
    'desc' => sprintf(__('Enter Download ID which you can find at %s website, to get automatical updates if you have active PRO subscription.', 'pwebcontact'), '<a href="https://www.perfect-web.co/login" target="_blank">Perfect-Web.co</a>'),
    /*** PRO END ***/
    'label' => 'Download ID'
)); ?>


<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'force_init',
    'group' => 'settings',
	'header' => 'Advanced settings',
    'label' => 'Force to load CSS and JS at all pages',
    'tooltip' => 'Enable this option only if you are displaying contact form inside content by some AJAX plugin',
    'default' => 0,
    'class' => 'pweb-radio-group',
    'options' => array(
        array(
            'value' => 0,
            'name' => 'No'
        ),
        array(
            'value' => 1,
            'name' => 'Yes'
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'googledocs_accesscode',
    'group' => 'settings',
    'desc' => sprintf(__('Click <a href="%s" target="_blank">here</a> to get your access code and allow us to save form data into a spreadsheet,', 'pwebcontact'), 'https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=300313169789-tjp5sq43n8shqra9okb1j06ovlsrh0b6.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&response_type=code&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F'),
    'label' => 'Google Access Code'
)); ?>
