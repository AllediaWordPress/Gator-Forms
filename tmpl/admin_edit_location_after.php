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
    'name' => 'layout',
    'label' => 'How do you want to display your form after opening?',
    'class' => 'pweb-related',
    'default' => 'slidebox',
    'required' => true,
    'options' => array(
        array(
            'value' => 'slidebox',
            'name' => 'Box at page edge',
            'class' => 'pweb-layout-slidebox pweb-related-slidebox',
            'is_parent' => true
        ),
        array(
            'value' => 'modal',
            'name' => 'Lightbox window',
            'class' => 'pweb-layout-modal pweb-related-modal pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'accordion',
            'name' => 'Accordion inside page content',
            'class' => 'pweb-layout-accordion pweb-related-accordion',
            'is_parent' => true
        ),
        array(
            'value' => 'static',
            'name' => 'Static form inside page content',
            'class' => 'pweb-layout-static pweb-related-static'
        )
    )
)); ?>

<div class="pweb-advanced-options">
    <a href="#" class="pweb-advanced-options-toggler pweb-advanced-options-active">
        <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-up"></i>
    </a>
    <div class="pweb-advanced-options-content" style="display:block">
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'accordion_boxed',
            'label' => 'Show accordion box with arrow',
            'default' => 1,
            'parent' => array('layout_accordion'),
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
    </div>
</div>