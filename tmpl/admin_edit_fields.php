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
        'name' => 'fields'
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
        
        <div class="pweb-custom-fields-type pweb-custom-fields-type-email">
            <?php _e('Email', 'pwebcontact'); ?>
            
            <div data-type="email" class="pweb-custom-field-container">
                <div class="pweb-custom-field-type">
                    <a href="#" class="pweb-custom-field-show-options pweb-has-tooltip" title="<?php _e('Show options', 'pwebcontact'); ?>">
                        <?php _e('Field type', 'pwebcontact'); ?> <span><?php _e('Email', 'pwebcontact'); ?></span> <i class="dashicons dashicons-admin-generic"></i>
                    </a>
                </div>
                <div class="pweb-custom-field-label">
                    <?php _e('Field label', 'pwebcontact'); ?> <span></span>
                </div>
                
                <div class="pweb-custom-field-options">
                    <h3><?php _e('Field options', 'pwebcontact'); ?></h3>
                    <?php echo $this->_get_field(array(
                        'type' => 'text',
                        'name' => 'label',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Label',
                        'class' => 'pweb-custom-field-label-input'
                    )); ?>
                    <?php echo $this->_get_field(array(
                        'type' => 'text',
                        'name' => 'tooltip',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Tooltip'
                    )); ?>
                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'required',
                        'index' => 'X',
                        'group' => 'fields',
                        'label' => 'Required',
                        'default' => 0,
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
                    
                    <div class="pweb-advanced-options">
                        <a href="#" class="pweb-advanced-options-toggler">
                            <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
                        </a>
                        <div class="pweb-advanced-options-content">
                            
                            <?php echo $this->_get_field(array(
                                'type' => 'text',
                                'name' => 'alias',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Alias for email template'
                            )); ?>
                            <?php echo $this->_get_field(array(
                                'type' => 'text',
                                'name' => 'values',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Default value'
                            )); ?>
                            <?php echo $this->_get_field(array(
                                'type' => 'text',
                                'name' => 'validation',
                                'index' => 'X',
                                'group' => 'fields',
                                'label' => 'Validation regular expresion'
                            )); ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pweb-custom-fields-type" data-type="name" data-name="<?php _e('Name', 'pwebcontact'); ?>">
            <?php _e('Name', 'pwebcontact'); ?>
        </div>
        <div class="pweb-custom-fields-type" data-type="phone" data-name="<?php _e('Phone', 'pwebcontact'); ?>">
            <?php _e('Phone', 'pwebcontact'); ?>
        </div>
        <div class="pweb-custom-fields-type" data-type="text" data-name="<?php _e('Text', 'pwebcontact'); ?>">
            <?php _e('Text', 'pwebcontact'); ?>
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
    </div>
    
</div>