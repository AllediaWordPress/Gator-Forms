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
    'type' => 'text',
    'name' => 'msg_success',
    'label' => 'Enter message which will appear after successful sending email by Contact Form',
    'class' => 'pweb-input-large'
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'msg_position',
    'label' => 'System message position',
    'default' => 'after',
    'options' => array(
        array(
            'value' => 'before',
            'name' => 'Before form'
        ),
        array(
            'value' => 'after',
            'name' => 'After form'
        ),
        array(
            'value' => 'button',
            'name' => 'Next to Send buton'
        ),
        array(
            'value' => 'popup',
            'name' => 'In popup',
            'is_parent' => true
        )
    )
)); ?>



<div class="pweb-advanced-options">
    <a href="#" class="pweb-advanced-options-toggler">
        <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
    </a>
    <div class="pweb-advanced-options-content">
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'msg_close_delay',
            'label' => 'Popup message close delay [s]',
            'default' => 10,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('msg_position_popup')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'color',
            'name' => 'msg_success_color',
            'label' => 'Success message color'
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'color',
            'name' => 'msg_error_color',
            'label' => 'Error message color'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'tooltips_validation',
            'label' => 'Show tooltips on validation error',
            'default' => 1,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No',
                    'is_parent' => true
                ),
                array(
                    'value' => 1,
                    'name' => 'Yes',
                    'is_parent' => true
                )
            )
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'reset_form',
            'label' => 'Reset form',
            'default' => 1,
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'After successfully sent'
                ),
                array(
                    'value' => 2,
                    'name' => 'After closing successfully sent form'
                ),
                array(
                    'value' => 3,
                    'name' => 'With reset button',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'button_reset',
            'label' => 'Reset button label',
            'parent' => array('reset_form_3')
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'redirect',
            'label' => 'Redirect after send',
            'default' => 0,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'Yes',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'redirect_url',
            'label' => 'Redirect URL',
            'class' => 'pweb-filter-url pweb-input-xlarge',
            'parent' => array('redirect_1')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'redirect_delay',
            'label' => 'Redirect delay [s]',
            'default' => 5,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('redirect_1')
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'text_button',
            'name' => 'adwords_url',
            'label' => 'Google AdWords Conversion Tracker - image URL',
            'button' => 'Paste',
            'class' => 'pweb-input-xlarge'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text_button',
            'name' => 'adcenter_url',
            'label' => 'Microsoft adCenter Conversion Tracker - MSTag iframe URL',
            'button' => 'Paste',
            'class' => 'pweb-input-xlarge'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'textarea',
            'name' => 'oncomplete',
            'label' => 'JavaScript on mail success event',
            'class' => 'pweb-filter-javascript widefat',
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'textarea',
            'name' => 'onerror',
            'label' => 'JavaScript on mail error event',
            'class' => 'pweb-filter-javascript widefat',
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
    </div>
</div>