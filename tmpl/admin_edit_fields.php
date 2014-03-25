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
<div id="pweb_fields" class="pweb-clearfix">
	
	<?php echo $this->_get_field_control(array(
        'type' => 'hidden',
        'name' => 'fields',
        'value' => json_encode( $this->_get_param('fields', array()) )
    )); ?>
    
    <?php echo $this->_get_field(array(
        'type' => 'select',
        'group' => 'load',
        'name' => 'fields',
        'label' => 'Choose predefined fields',
        'options' => array(
            array(
                'name' => '- Select -',
                'value' => ''
            ),
            array(
                'name' => 'Contact form',
                'value' => '{"1":{"type":"row"},"2":{"type":"name","label":"Name","tooltip":"","required":"0","alias":"name","values":"","validation":""},"3":{"type":"row"},"4":{"type":"email","label":"Email","tooltip":"","required":"1","alias":"email","values":""},"5":{"type":"row"},"6":{"type":"phone","label":"Phone","tooltip":"","required":"0","alias":"phone","values":"","validation":"\/[\\\\d-+() ]+\/"},"7":{"type":"row"},"8":{"type":"textarea","label":"Message","tooltip":"","required":"1","alias":"message","values":"","rows":"","limit":""},"9":{"type":"row"},"10":{"type":"email_copy"},"11":{"type":"row"},"12":{"type":"button_send"}}'
            )
        )
    )); ?>
    
    
    <div class="pweb-fields-container">
        <div class="pweb-fields-rows pweb-clearfix" id="pweb_fields_rows">
            
        </div>
        <div class="pweb-fields-add-row pweb-clearfix" id="pweb_fields_add_row">
            <i class="icomoon-plus"></i> <?php _e('Add row', 'pwebcontact'); ?>
        </div>
    </div>
    
    
    
    <div class="pweb-fields-types" id="pweb_fields_types">
        
        <h3><?php _e('Types of fields', 'pwebcontact'); ?></h3>
        
        
        
        <?php $field_type = 'email'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Email', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Email', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Email field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 1,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'name'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Name', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Name', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Name field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'validation',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Validation regular expression'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'phone'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Phone', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Phone', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Phone field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'validation',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Validation regular expression',
                                'default' => '/[\d\-\+() ]+/'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'subject'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Subject', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Subject', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Subject field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'validation',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Validation regular expression'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'text'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Text input', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Text input', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Text input field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'validation',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Validation regular expression'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'textarea'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Multi-line textarea input', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Multi-line textarea input', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Multi-line textarea input field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'rows',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Rows number'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'limit',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Characters limit'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'date'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Date picker', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Date picker', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Date picker field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'format',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Date format',
                                'default' => '%d-%m-%Y'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'radio'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Radio group', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Radio group', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Radio group field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'textarea',
                        'name' => 'values',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Options'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'cols',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Display options in columns'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'checkboxes'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Checkboxes group', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Checkboxes group', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Checkboxes group field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'textarea',
                        'name' => 'values',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Options'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'cols',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Display options in columns'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'checkbox'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Single checkbox', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Single checkbox', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Single checkbox field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'url',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Agree to Terms & Conditions URL'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'radio',
                                'name' => 'target',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Open Terms & Conditions in',
                                'class' => 'pweb-radio-group',
                                'default' => 0,
                                'options' => array(
                                    array('value' => 0, 'name' => 'New window'),
                                    array('value' => 1, 'name' => 'Lightbox window')
                                )
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'select'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Select list', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Select list', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Select list field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'textarea',
                        'name' => 'values',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Options'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'default',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default option'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'multiple'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Multiple select list', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Multiple select list', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Multiple select list field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'textarea',
                        'name' => 'values',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Options'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'rows',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Visible rows',
                                'default' => '4'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'password'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Password', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Password', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Password field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'class' => 'pweb-radio-group',
                        'default' => 0,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'class' => 'pweb-custom-field-alias',
                                'label' => 'Alias for email template'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'disabled' => true,
                                'type' => 'text',
                                'name' => 'validation',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Validation regular expression'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'separator_text'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Custom text/html', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Custom text/html', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span><?php _e('Text/HTML', 'pwebcontact'); ?></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Custom text/html field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'textarea',
                        'name' => 'values',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Text/HTML'
                    )); ?>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'separator_header'; ?>
        <div class="pweb-custom-fields-type" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Header', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Header', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span><?php _e('Header', 'pwebcontact'); ?></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Header field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'textarea',
                        'name' => 'values',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Text/HTML'
                    )); ?>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'upload'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Upload', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Upload', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Upload field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'mailto_list'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('List of recipients', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('List of recipients', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('List of recipients field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'email_copy'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Send copy to yourself', 'pwebcontact'); ?>
            <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Send copy to yourself', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                    <?php if (in_array($field_type, self::$pro['fields'])) : ?><span class="pweb-pro">Pro</span><?php endif; ?>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span><?php _e('Send copy to yourself', 'pwebcontact'); ?></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Send copy to yourself field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                </div>
            </div>
        </div>
        
        
        <?php $field_type = 'button_send'; ?>
        <div class="pweb-custom-fields-type pweb-custom-fields-single" id="pweb_field_type_<?php echo $field_type; ?>">
            <?php _e('Send button', 'pwebcontact'); ?>
            
            <div data-type="<?php echo $field_type; ?>" class="pweb-custom-field-container pweb-custom-fields-single pweb-custom-field-type-<?php echo $field_type; ?>">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Button type', 'pwebcontact'); ?> <span><?php _e('Send', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Button label', 'pwebcontact'); ?> <span><?php _e('Send', 'pwebcontact'); ?></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Send button options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'disabled' => true,
                        'type' => 'hidden',
                        'name' => 'type',
                        'index' => 'X',
                        'group' => 'fields',
                        'value' => $field_type
                    )); ?>
                </div>
            </div>
        </div>
        
        
        <div class="pweb-advanced-options pweb-clear">
            <a href="#" class="pweb-advanced-options-toggler">
                <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
            </a>
            <div class="pweb-advanced-options-content">

            </div>
        </div>
    </div>
    
    
    
    <div class="pweb-fields-options" id="pweb_fields_options">
        <a href="#" id="pweb_fields_options_close" class="button">&times;</a>
        <div id="pweb_fields_options_content"></div>
        
        
        <div id="pweb_fields_options_content_upload" class="pweb-fields-options-content">
            
            <?php echo $this->_get_field(array(
                'type' => 'text',
                'name' => 'upload_label',
                'label' => 'Label',
            )); ?>

            <?php echo $this->_get_field(array(
                'type' => 'radio',
                'name' => 'upload_required',
                'label' => 'Required',
                'class' => 'pweb-radio-group',
                'default' => 1,
                'options' => array(
                    array('value' => 0, 'name' => 'No'),
                    array('value' => 1, 'name' => 'Yes')
                )
            )); ?>
            
            <?php $max_size = $this->_convert_size( ini_get('post_max_size') );
            echo $this->_get_field(array(
                'type' => 'custom',
                'name' => 'upload_max_size',
                'label' => 'Server max upload size',
                'content' => '<span class="badge badge-info">'.$max_size.' MB</span><script type="text/javascript">var pwebUploadMaxSize = '.$max_size.';</script>'
            )); ?>
            
            <?php echo $this->_get_field(array(
                'type' => 'text',
                'name' => 'upload_size_limit',
                'label' => 'File size limit [MB]',
                'default' => '1',
                'class' => 'pweb-filter-upload-max-size'
            )); ?>
            
            <?php echo $this->_get_field(array(
                'type' => 'text',
                'name' => 'upload_files_limit',
                'label' => 'Files limit',
                'default' => '5',
                'class' => 'pweb-filter-float'
            )); ?>

            <?php echo $this->_get_field(array(
                'type' => 'text',
                'name' => 'upload_allowed_ext',
                'label' => 'Allowed files extensions',
                'default' => 'gif|jpe?g|png|doc?x|odt|txt|pdf|zip',
                'class' => 'pweb-filter-ext'
            )); ?>

            <?php echo $this->_get_field(array(
                'type' => 'radio',
                'name' => 'upload_show_limits',
                'label' => 'Show limits in tooltip',
                'class' => 'pweb-radio-group',
                'default' => 1,
                'options' => array(
                    array('value' => 0, 'name' => 'No'),
                    array('value' => 1, 'name' => 'Yes')
                )
            )); ?>

            <?php echo $this->_get_field(array(
                'type' => 'text',
                'name' => 'tooltip',
                'label' => 'Tooltip'
            )); ?>

            <div class="pweb-advanced-options">
                <a href="#" class="pweb-advanced-options-toggler">
                    <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                </a>
                <div class="pweb-advanced-options-content">

                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'attachment_type',
                        'label' => 'Attachment type in email',
                        'class' => 'pweb-radio-group',
                        'default' => 1,
                        'options' => array(
                            array('value' => 1, 'name' => 'Files', 'is_parent' => true),
                            array('value' => 2, 'name' => 'Links to files')
                        )
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'attachment_delete',
                        'label' => 'Delete files after sending',
                        'class' => 'pweb-radio-group',
                        'default' => 1,
                        'parent' => 'attachment_type_1',
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'upload_autostart',
                        'label' => 'Auto-start upload',
                        'class' => 'pweb-radio-group',
                        'default' => 1,
                        'options' => array(
                            array('value' => 0, 'name' => 'No'),
                            array('value' => 1, 'name' => 'Yes')
                        )
                    )); ?>
                    
                    <?php $upload_dir = wp_upload_dir();
                    echo $this->_get_field(array(
                        'type' => 'custom',
                        'name' => 'upload_path',
                        'label' => 'Upload path',
                        'content' => $upload_dir['basedir'].'/pwebcontact/'.$this->id
                    )); ?>

                </div>
            </div>
        </div>
        
        
        <div id="pweb_fields_options_content_mailto_list" class="pweb-fields-options-content">
            
            <?php echo $this->_get_field(array(
                'type' => 'textarea',
                'name' => 'email_to_list',
                'label' => 'Recipients',
                'class' => 'pweb-filter-emailRecipients'
            )); ?>
            
        </div>
        
        
        <div id="pweb_fields_options_content_button_send" class="pweb-fields-options-content">
            
            <?php echo $this->_get_field(array(
                'type' => 'text',
                'name' => 'button_send',
                'label' => 'Send button label',
                'default' => __('Send', 'pwebcontact')
            )); ?>
            
        </div>
        
        
    </div>
    
</div>