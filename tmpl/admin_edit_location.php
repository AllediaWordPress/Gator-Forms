<?php
/**
 * @version 1.0.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license Perfect Web License http://www.perfect-web.co/license
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;

?>

<h3 class="pweb-steps">
    <?php printf(__('Step %d of %d', 'pwebcontact'), 1, 4); ?>
    -
    <?php _e('Decide how and where your form should be displayed', 'pwebcontact'); ?>
    <button class="button button-primary pweb-next-tab-button" type="button">
        <?php _e( 'Next', 'pwebcontact' ); ?> <i class="glyphicon glyphicon-chevron-right"></i>
    </button>
</h3>

<?php if (!defined('PWEBCONTACT_PRO')) : ?>
<div id="pweb_layout_type_warning" class="pweb-alert pweb-alert-info" style="display:none">
    <?php _e('You need to go PRO :)', 'pwebcontact'); ?>
    <?php _e('You have chosen PRO layout. You can still save your form, but to display it buy PRO Version', 'pwebcontact'); ?>
    <button class="button button-primary pweb-buy">
        <i class="glyphicon glyphicon-shopping-cart"></i> <?php _e( 'Buy', 'pwebcontact' ); ?>
    </button>
</div>
<?php endif; ?>

<div class="pweb-width-40" id="pweb-location-steps">

    <div class="pweb-location-step">
        <h3><?php _e( 'Form before opening', 'pwebcontact' ); ?></h3>
        <div class="pweb-location-step-tab pweb-tab-active" id="pweb-location-before">
            <div class="pweb-step-option"></div>
            <div class="pweb-step-arrow-right"></div>
            <div class="pweb-step-arrow-down"></div>
        </div>
    </div>

    <div class="pweb-location-step">
        <h3><?php _e( 'Form after opening', 'pwebcontact' ); ?></h3>
        <div class="pweb-location-step-tab" id="pweb-location-after">
            <div class="pweb-step-option"></div>
            <div class="pweb-step-arrow-right"></div>
            <div class="pweb-step-arrow-down"></div>
        </div>
    </div>

    <div class="pweb-location-step">
        <h3><?php _e( 'Form position', 'pwebcontact' ); ?></h3>
        <div class="pweb-location-step-tab" id="pweb-location-place">
            <div class="pweb-step-option"></div>
            <div class="pweb-step-arrow-right"></div>
        </div>
    </div>

</div>
<div class="pweb-width-60" id="pweb-location-options">

    <div class="pweb-location-options pweb-options-active" id="pweb-location-before-options">
        <?php $this->_load_tmpl('before', __FILE__); ?>
    </div>

    <div class="pweb-location-options" id="pweb-location-after-options">
        <?php $this->_load_tmpl('after', __FILE__); ?>
    </div>

    <div class="pweb-location-options" id="pweb-location-place-options">
        <?php $this->_load_tmpl('place', __FILE__); ?>
    </div>

</div>
