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
    'name' => 'handler',
    'label' => 'How do you want to display your form before it is opened?',
    'class' => 'pweb-related',
    'default' => 'tab',
    'required' => true,
    'options' => array(
        array(
            'value' => 'button',
            'name' => 'Toggler Button',
            'class' => 'pweb-layout_type-button pweb-related-accordion pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'tab',
            'name' => 'Toggler Tab',
            'class' => 'pweb-layout_type-tab pweb-related-slidebox pweb-related-modal',
            'is_parent' => true
        ),
        array(
            'value' => 'static',
            'name' => 'Always opened inside page content',
            'class' => 'pweb-layout_type-static pweb-related-static'
        ),
        array(
            'value' => 'hidden',
            'name' => 'Hidden',
            'class' => 'pweb-layout_type-hidden pweb-related-modal pweb-related-accordion pweb-related-slidebox pweb-related-modal-button'
        )
    )
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'toggler_name',
    'label' => 'Define text shown on Toggler Tab or Button',
    'parent' => array('handler_button', 'handler_tab')
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'position',
    'label' => 'Toggler Tab position',
    'parent' => 'handler_tab',
    'default' => 'left',
    'options' => array(
        array(
            'value' => 'left',
            'name' => 'Left',
            'is_parent' => true
        ),
        array(
            'value' => 'right',
            'name' => 'Right',
            'is_parent' => true
        ),
        array(
            'value' => 'top:left',
            'name' => 'Top left'
        ),
        array(
            'value' => 'top:right',
            'name' => 'Top right'
        ),
        array(
            'value' => 'bottom:left',
            'name' => 'Bottom left'
        ),
        array(
            'value' => 'bottom:right',
            'name' => 'Bottom right'
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
            'name' => 'offset',
            'label' => 'Position offset [px, %]',
            'class' => 'pweb-filter-unit pweb-input-mini',
            'parent' => array('layout_slidebox', 'handler_tab')
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'zindex',
            'label' => 'Layer level (CSS z-index)',
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('layout_slidebox', 'layout_modal')
        )); ?>



        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'toggler_icon',
            'label' => 'Toggler icon',
            'default' => 0,
            'parent' => array('handler_tab', 'handler_button'),
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'Disabled'
                ),
                array(
                    'value' => 'icomoon',
                    'name' => 'IcoMoon',
                    'is_parent' => true
                ),
                array(
                    'value' => 'gallery',
                    'name' => 'Gallery',
                    'is_parent' => true
                ),
                array(
                    'value' => 'custom',
                    'name' => 'Custom image',
                    'is_parent' => true
                )
            )
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'icomoon',
            'name' => 'toggler_icomoon',
            'label' => 'IcoMoon',
            'parent' => array('toggler_icon_icomoon')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'filelist',
            'name' => 'toggler_icon_gallery_image', //TODO change param name in front
            'label' => 'Gallery icon',
            'filter' => '\.(jpg|png|gif)$',
            'directory' => 'media/images/icons',
            'parent' => array('toggler_icon_gallery')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'image',
            'name' => 'toggler_icon_custom_image', //TODO change param name in front
            'label' => 'Custom icon',
            'parent' => array('toggler_icon_custom'),
            'class' => 'pweb-input-xlarge'
        )); ?>



        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'toggler_vertical',
            'label' => 'Vertical Toggler Tab',
            'default' => 0,
            'parent' => array('position_left', 'position_right'),
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
            'name' => 'toggler_rotate',
            'label' => 'Rotate Toggler Tab text',
            'default' => 1,
            'parent' => array('toggler_vertical_1'),
            'options' => array(
                array(
                    'value' => -1,
                    'name' => '-90&deg; <i class="icomoon-undo2"></i> (counter-clockwise)'
                ),
                array(
                    'value' => 1,
                    'name' => ' 90&deg; <i class="icomoon-redo2"></i> (clockwise)'
                )
            )
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'filelist',
            'name' => 'toggler_font',
            'label' => 'TTF font for vertical Toggler Tab text',
            'default' => 'NotoSans-Regular',
            'filter' => '^((?!icomoon).)+\.ttf$',
            'directory' => 'media/fonts',
            'strip_ext' => true,
            'parent' => array('toggler_vertical_1')
        )); ?>



        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'open_toggler',
            'label' => 'Auto-open',
            'default' => 0,
            'parent' => array('layout_slidebox', 'layout_modal', 'layout_accordion'),
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'On load',
                    'is_parent' => true
                ),
                array(
                    'value' => 2,
                    'name' => 'On scroll',
                    'is_parent' => true
                ),
                array(
                    'value' => 3,
                    'name' => 'On exit',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'open_delay',
            'label' => 'Auto-open delay [ms]',
            'default' => 1000,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('open_toggler_1', 'open_toggler_2', 'open_toggler_3')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'open_count',
            'label' => 'Auto-open count',
            'default' => 1,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('open_toggler_1', 'open_toggler_2', 'open_toggler_3')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'cookie_lifetime',
            'label' => 'Cookie lifetime [days]',
            'default' => 30,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('open_toggler_1', 'open_toggler_2', 'open_toggler_3')
        )); ?>



        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'close_toggler',
            'label' => 'Auto-close',
            'default' => 0,
            'parent' => array('layout_slidebox', 'layout_modal', 'layout_accordion'),
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'On mail success',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'close_delay',
            'label' => 'Auto-close delay [ms]',
            'default' => 0,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('close_toggler_1')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'close_other',
            'label' => 'Close other Perfect Contact Forms and Boxes',
            'default' => 0,
            'parent' => array('layout_slidebox', 'layout_accordion'),
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
            'name' => 'modal_disable_close',
            'label' => 'Disable manual closing of Lightbox',
            'default' => 0,
            'parent' => array('layout_modal'),
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
            'type' => 'textarea',
            'name' => 'onload',
            'label' => 'JavaScript on load event',
            'class' => 'pweb-filter-javascript widefat',
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'textarea',
            'name' => 'onopen',
            'label' => 'JavaScript on open event',
            'class' => 'pweb-filter-javascript widefat',
            'parent' => array('layout_slidebox', 'layout_modal', 'layout_accordion'),
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'textarea',
            'name' => 'onclose',
            'label' => 'JavaScript on close event',
            'class' => 'pweb-filter-javascript widefat',
            'parent' => array('layout_slidebox', 'layout_modal', 'layout_accordion'),
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
    </div>
</div>