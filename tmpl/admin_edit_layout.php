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
    'type' => 'filelist',
    'name' => 'style_toggler',
    'label' => 'Toggler style',
    'default' => 'blue',
    'filter' => '\.css$',
    'directory' => 'media/css/toggler',
    'strip_ext' => true,
    'parent' => array('handler_tab', 'handler_button'),
    'options' => array(
        array(
            'value' => -1,
            'name' => '- Do not use -'
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'color',
    'name' => 'toggler_bg',
    'label' => 'Custom color of Toggler',
    'parent' => array('handler_tab', 'handler_button')
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'color',
    'name' => 'toggler_color',
    'label' => 'Custom color of Toggler text',
    'parent' => array('handler_tab', 'handler_button')
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'filelist',
    'name' => 'style_form',
    'label' => 'Buttons and fields style',
    'default' => 'blue',
    'filter' => '\.css$',
    'directory' => 'media/css/form',
    'strip_ext' => true,
    'options' => array(
        array(
            'value' => -1,
            'name' => '- Do not use -'
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'color',
    'name' => 'buttons_fields_color',
    'label' => 'Custom color of buttons, fields and links'
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'color',
    'name' => 'buttons_text_color',
    'label' => 'Custom color of buttons text'
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'filelist',
    'name' => 'style_bg',
    'label' => 'Background style',
    'default' => 'white',
    'filter' => '\.css$',
    'directory' => 'media/css/background',
    'strip_ext' => true,
    'options' => array(
        array(
            'value' => -1,
            'name' => '- Do not use -'
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'color',
    'name' => 'bg_color',
    'label' => 'Custom color of form background',
    'is_parent' => true
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'select',
    'name' => 'bg_opacity',
    'label' => 'Opacity of custom color of form background',
    'default' => 0.9,
    'parent' => array('bg_color'),
    'options' => array(
        array('value' => 0.1),
        array('value' => 0.2),
        array('value' => 0.3),
        array('value' => 0.4),
        array('value' => 0.5),
        array('value' => 0.6),
        array('value' => 0.7),
        array('value' => 0.8),
        array('value' => 0.9),
        array('value' => 1.0)
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'color',
    'name' => 'text_color',
    'label' => 'Custom color of form text'
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'rounded',
    'label' => 'Rounded corners',
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
    'name' => 'shadow',
    'label' => 'Shadow',
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
    'name' => 'labels_position',
    'label' => 'Labels position',
    'default' => 'inline',
    'class' => 'pweb-radio-group',
    'options' => array(
        array(
            'value' => 'inline',
            'name' => 'Inline with field'
        ),
        array(
            'value' => 'above',
            'name' => 'Above field'
        ),
        array(
            'value' => 'over',
            'name' => 'Inside field as placeholder'
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
            'name' => 'form_width',
            'label' => 'Form width [px, %]',
            'class' => 'pweb-filter-unit'
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'labels_width',
            'label' => 'Labels width [%]',
            'class' => 'pweb-filter-int'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'image',
            'name' => 'bg_image',
            'label' => 'Background image'
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'select',
            'name' => 'bg_position',
            'label' => 'Background image alignment',
            'options' => array(
                array('value' => '', 'name' => '- Use default -'),
                array('value' => 'left top'),
                array('value' => 'left center'),
                array('value' => 'left bottom'),
                array('value' => 'right top'),
                array('value' => 'right center'),
                array('value' => 'right bottom'),
                array('value' => 'center top'),
                array('value' => 'center center'),
                array('value' => 'center bottom')
            )
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'select',
            'name' => 'bg_padding_position',
            'label' => 'Form padding',
            'options' => array(
                array(
                    'value' => '',
                    'name' => '- Disabled -'
                ),
                array(
                    'value' => 'left',
                    'name' => 'Left'
                ),
                array(
                    'value' => 'right',
                    'name' => 'Right'
                ),
                array(
                    'value' => 'top',
                    'name' => 'Top'
                ),
                array(
                    'value' => 'bottom',
                    'name' => 'Bottom'
                )
            )
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'bg_padding',
            'label' => 'Padding value [px, %]',
            'class' => 'pweb-filter-unit'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'color',
            'name' => 'modal_bg',
            'label' => 'Lightbox backdrop color',
            'parent' => array('layout_modal')
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'select',
            'name' => 'modal_opacity',
            'label' => 'Lightbox backdrop opacity',
            'default' => -1,
            'parent' => array('layout_modal'),
            'options' => array(
                array('value' => -1, 'name' => '- Use default -'),
                array('value' => 0, 'name' => 'Hide'),
                array('value' => 0.1),
                array('value' => 0.2),
                array('value' => 0.3),
                array('value' => 0.4),
                array('value' => 0.5),
                array('value' => 0.6),
                array('value' => 0.7),
                array('value' => 0.8),
                array('value' => 0.9),
                array('value' => 1.0)
            )
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'toggler_width',
            'label' => 'Toggler width [px]',
            'class' => 'pweb-filter-int',
            'parent' => array('handler_tab', 'handler_button')
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'toggler_height',
            'label' => 'Toggler height [px]',
            'class' => 'pweb-filter-int',
            'parent' => array('handler_tab', 'handler_button')
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'toggler_font_size',
            'label' => 'Toggler font size',
            'class' => 'pweb-filter-unit',
            'parent' => array('handler_tab', 'handler_button')
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'toggler_font_family',
            'label' => 'Toggler font family',
            'parent' => array('toggler_vertical_0')
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'form_font_size',
            'label' => 'Form font size',
            'class' => 'pweb-filter-unit'
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'form_font_family',
            'label' => 'Form font family'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'icons',
            'label' => 'Icons type',
            'default' => 'icomoon',
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 'icomoon',
                    'name' => 'IcoMoon'
                ),
                array(
                    'value' => 'glyphicons',
                    'name' => 'Bootstrap Glyphicons'
                )
            )
        )); ?>
    </div>
</div>