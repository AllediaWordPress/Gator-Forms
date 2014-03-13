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
    
    <div class="pweb-fields-types">
        <ul>
            <li><?php _e('Type 1', 'pwebcontact'); ?></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    
    <div class="pweb-fields-options">
        options
        <div class="pweb-advanced-options">
            <a href="#" class="pweb-advanced-options-toggler">
                <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
            </a>
            <div class="pweb-advanced-options-content">
                advanced options
            </div>
        </div>
    </div>
</div>

<div class="pweb-advanced-options">
    <a href="#" class="pweb-advanced-options-toggler">
        <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
    </a>
    <div class="pweb-advanced-options-content">
        
    </div>
</div>