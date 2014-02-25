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
    'name' => 'load_bootstrap',
    'label' => 'Load Bootstrap JS',
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