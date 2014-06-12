<?php
/**
 * @version 1.0.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;
?>

<h3>
    <?php _e('Check below if you can publish your form', 'pwebcontact'); ?>
</h3>


<div id="pweb-cog-check-success" class="pweb-alert pweb-alert-success" style="display:block">
    <?php _e('Congratulations! All options you had to choose to get your form working properly are chosen.', 'pwebcontact'); ?>
</div>

<?php if (!defined('PWEBCONTACT_PRO')) : ?>
<div id="pweb-cog-check-warning" class="pweb-alert pweb-alert-success" style="display:none">
    <?php _e('Congratulations your form is ready! But you have chosen some PRO options so you need to BUY Pro Version in order to publish your form', 'pwebcontact'); ?>
    
    <button class="button button-primary pweb-buy">
        <i class="icomoon-cart"></i> <?php _e( 'Buy', 'pwebcontact' ); ?>
    </button>
</div>
<?php endif; ?>

<div id="pweb-cog-check-error" class="pweb-alert pweb-alert-danger" style="display:none">
    <?php _e('There are still some options required to get your form working', 'pwebcontact'); ?>
</div>

<button type="button" class="button button-primary" id="pweb-cog-check-save">
    <i class="icomoon-disk"></i> <span><?php _e( 'Save' ); ?></span>
</button>

<div id="pweb-cog-check">
    
    <div class="pweb-alert pweb-alert-danger" id="pweb-email-to-warning" style="display:none">
        <i class="icomoon-warning"></i>
        <?php _e('Enter one or more emails to which message should be sent to in Email settings tab.', 'pwebcontact'); ?>
    </div>

    <?php if (($result = $this->_check_mailer()) !== true) : ?>
    <div class="pweb-alert pweb-alert-danger">
        <i class="icomoon-warning"></i> <?php echo $result; ?>
    </div>
    <?php endif; ?>


    <?php if (($result = $this->_check_cache_path()) !== true) : ?>
    <div class="pweb-alert pweb-alert-danger">
        <i class="icomoon-warning"></i> <?php echo $result; ?>
    </div>
    <?php endif; ?>


    <?php if (($result = $this->_check_upload_path()) !== true) : ?>
    <div class="pweb-alert pweb-alert-danger" id="pweb-upload-path-warning" style="display:none">
        <i class="icomoon-warning"></i> <?php echo $result; ?>
    </div>
    <?php endif; ?>


    <?php if (($result = $this->_check_image_text_creation()) !== true) : ?>
    <div class="pweb-alert pweb-alert-warning">
        <i class="icomoon-warning"></i> <?php echo $result; ?>
    </div>
    <?php endif; ?>

</div>