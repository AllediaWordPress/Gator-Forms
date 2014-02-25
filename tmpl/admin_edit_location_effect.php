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
    'name' => 'effect',
    'label' => 'Which animation do you want to use?',
    'class' => 'pweb-related',
    'default' => 'slide_in',
    'required' => true,
    'options' => array(
        array(
            'value' => 'slide_in',
            'name' => 'Slide-in from page edge',
            'class' => 'pweb-effect-slide-in pweb-related-slidebox',
            'is_parent' => true
        ),
        array(
            'value' => 'modal_fade',
            'name' => 'Fade-in lightbox',
            'class' => 'pweb-effect-modal-fade pweb-related-modal pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'modal_drop',
            'name' => 'Drop and fade-in lightbox from top',
            'class' => 'pweb-effect-modal-drop pweb-related-modal pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'modal_rotate',
            'name' => 'Move from toggler, rotate, enlarge and fade-in lightbox',
            'class' => 'pweb-effect-modal-rotate pweb-related-modal pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'modal_square',
            'name' => 'Move from toggler, enlarge and fade-in lightbox',
            'class' => 'pweb-effect-modal-square pweb-related-modal pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'modal_smooth',
            'name' => 'Move from toggler, change height and fade-in lightbox',
            'class' => 'pweb-effect-modal-smooth pweb-related-modal pweb-related-modal-button',
            'is_parent' => true
        ),
        array(
            'value' => 'slide_down',
            'name' => 'Slide-down - accordation',
            'class' => 'pweb-effect-slide-down pweb-related-accordion',
            'is_parent' => true
        ),
        array(
            'value' => 'none',
            'name' => 'No animation',
            'class' => 'pweb-effect-none pweb-related-static'
        )
    )
)); ?>

<div class="pweb-advanced-options">
    <a href="#" class="pweb-advanced-options-toggler">
        <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
    </a>
    <div class="pweb-advanced-options-content">
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'toggler_slide',
            'label' => 'Slide Toggler Tab with box',
            'default' => 0,
            'parent' => array('layout_slidebox'),
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
            'type' => 'select',
            'name' => 'slide_transition',
            'label' => 'Slide effect transition',
            'default' => -1,
            'parent' => array('layout_slidebox'),
            'options' => array(
                array('value' => -1, 'name' => '- Use default -'),
                array('value' => 'linear'),
                array('value' => 'swing'),
                array('value' => -2, 'name' => '- jQuery UI effects -', 'disabled' => true),
                array('value' => 'easeInQuad'),
                array('value' => 'easeOutQuad'),
                array('value' => 'easeInOutQuad'),
                array('value' => 'easeInCubic'),
                array('value' => 'easeOutCubic'),
                array('value' => 'easeInOutCubic'),
                array('value' => 'easeInQuart'),
                array('value' => 'easeOutQuart'),
                array('value' => 'easeInOutQuart'),
                array('value' => 'easeInQuint'),
                array('value' => 'easeOutQuint'),
                array('value' => 'easeInOutQuint'),
                array('value' => 'easeInExpo'),
                array('value' => 'easeOutExpo'),
                array('value' => 'easeInOutExpo'),
                array('value' => 'easeInSine'),
                array('value' => 'easeOutSine'),
                array('value' => 'easeInOutSine'),
                array('value' => 'easeInCirc'),
                array('value' => 'easeOutCirc'),
                array('value' => 'easeInOutCirc'),
                array('value' => 'easeInElastic'),
                array('value' => 'easeOutElastic'),
                array('value' => 'easeInOutElastic'),
                array('value' => 'easeInBack'),
                array('value' => 'easeOutBack'),
                array('value' => 'easeInOutBack'),
                array('value' => 'easeInBounce'),
                array('value' => 'easeOutBounce'),
                array('value' => 'easeInOutBounce')
            )
        )); ?>



        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'effect_duration',
            'label' => 'Effect duration [ms]',
            'default' => 400,
            'class' => 'pweb-filter-int',
            'parent' => array('layout_slidebox', 'layout_modal', 'layout_accordion')
        )); ?>
        
    </div>
</div>