<?php
/**
 * @version 2.3.0
 * @package Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;

?>

<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'dlid',
    'group' => 'settings',
    /*** FREE START ***/
    'readonly' => true,
    'desc' => sprintf(__('Enter Download ID which you can find at %s website, to get automatical updates if you have active PRO subscription. You have to first reinstall plugin to PRO version by yourself.', 'pwebcontact'), '<a href="https://gatorforms.com/wp-login.php" target="_blank">Gator Forms</a>'),
    /*** FREE END ***/
    /*** PRO START ***/
    'desc' => sprintf(__('Enter Download ID which you can find at %s website, to get automatical updates if you have active PRO subscription.', 'pwebcontact'), '<a href="https://gatorforms.com/wp-login.php" target="_blank">Gator Forms</a>'),
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

<?php
/*** PRO START ***/
$client   = PWebContact_GoogleApi::getInstance();
$url      = $client->createAccessCodeUrl('https://www.googleapis.com/auth/spreadsheets');
$hasToken = $client->hasToken();
echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'googleapi_accesscode',
    'group' => 'settings',
    'desc' => sprintf(__('%sClick here%s to grant an access to your Google Spreadsheets to allow saving contact form entries. Copy the generated code and paste it here only once. After saving settings this code will be removed as it will be no longer needed.', 'pwebcontact'), '<a href="'.$url.'" target="_blank">', '</a>'),
    'label' => 'Google API Access Code',
    'html_after' => ' <span class="pweb-text-' . ($hasToken ? 'success' : 'danger') . '">'
                    . '<i class="glyphicon glyphicon-' . ($hasToken ? 'ok' : 'remove') . '"></i> '
                    . __(($hasToken ? 'Has access' : 'No access'), 'pwebcontact')
                    . '</span>'
));
/*** PRO END ***/
?>
