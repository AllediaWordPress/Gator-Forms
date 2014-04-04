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

?>

<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'debug',
    'label' => 'Debug (testing mode)',
    'tooltip' => 'Enable this option if contact form is not showing or sending emails. Then reload front-end page and try to send email again. It would display more informations.',
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
    'type' => 'radio',
    'name' => 'bootstrap_version',
    'label' => 'Bootstrap version',
    'tooltip' => 'Select version of Bootstrap to work with.',
    'default' => 2,
    'class' => 'pweb-radio-group',
    'options' => array(
        array(
            'value' => 2,
            'name' => '2.3.2'
        ),
        array(
            'value' => 3,
            'name' => '3.x'
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'load_bootstrap',
    'label' => 'Load Bootstrap JS',
    'tooltip' => 'Disable this option only if you have already loaded Bootstrap JavaScript with other extension. Required.',
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
    'type' => 'radio',
    'name' => 'load_bootstrap_css',
    'label' => 'Load Bootstrap CSS',
    'tooltip' => 'Disable this option only if you have already loaded Bootstrap CSS with other extension. If your template breaks because of loaded Bootstrap then set legacy option to load only required styles which would work only for contact form. Required.',
    'default' => 2,
    'class' => 'pweb-radio-group',
    'options' => array(
        array(
            'value' => 0,
            'name' => 'No'
        ),
        array(
            'value' => 1,
            'name' => 'Yes'
        ),
        array(
            'value' => 2,
            'name' => 'Only required styles'
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'boostrap_glyphicons',
    'label' => 'Load Bootstrap Glyphicons',
    'tooltip' => 'Disable this option only if you can see two icons over themselves. Do not disable this option if you have selected Glyphicons in Layout tab of configuration.',
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
    'type' => 'radio',
    'name' => 'load_jquery',
    'label' => 'Load WordPress jQuery',
    'tooltip' => 'Disable this option only if you have already loaded jQuery with other extension. Required.',
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
    'type' => 'radio',
    'name' => 'load_jquery_ui',
    'label' => 'Load WordPress jQuery UI',
    'tooltip' => 'It is being loaded only if needed. Disable this option only if you have already loaded jQuery UI with other extension. Required for transitions effects and for Uploader.',
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
    'type' => 'radio',
    'name' => 'load_jquery_validate',
    'label' => 'Load jQuery Validate',
    'tooltip' => 'Disable this option only if you have already loaded jQuery Validate Plugin with other extension. Required.',
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
    'type' => 'radio',
    'name' => 'load_jquery_fileupload',
    'label' => 'Load jQuery File Upload',
    'tooltip' => 'It is being loaded only if needed. Disable this option only if you have already loaded jQuery File Upload Plugin with other extension. Required for files uploader.',
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
    'type' => 'radio',
    'name' => 'load_jquery_cookie',
    'label' => 'Load jQuery Cookie',
    'tooltip' => 'It is being loaded only if needed. Disable this option only if you have already loaded jQuery Cookie Plugin with other extension. Required for auto-popup counter.',
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
    'type' => 'radio',
    'name' => 'load_icomoon',
    'label' => 'Load IcoMoon',
    'tooltip' => 'It is being loaded only if needed. Disable this option only if you have already loaded IcoMoon with other extension. Required for toggler tab with IcoMoon and other icons.',
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
    'type' => 'radio',
    'name' => 'feed',
    'label' => 'Updates feed',
    'tooltip' => 'Display news and special offers from Perfect-Web.co website in administration panel of this extension only for Administrator.',
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