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
<form name="edit" method="post" action="<?php echo esc_attr(admin_url( 'admin.php?page=pwebcontact&task=save_settings' )); ?>" id="pweb_form">
    
    <div id="pweb-adminbar">
        
        <div class="pweb-toolbar pweb-clearfix">
            <h2><?php _e('Perfect Easy & Powerful Contact Form Settings', 'pwebcontact'); ?></h2>
            
            <?php $this->_display_messages(); ?>

            <button type="submit" class="button button-primary" id="pweb-save-button">
                <i class="icomoon-disk"></i> <span><?php _e( 'Save' ); ?></span>
            </button>
            <button type="button" class="button" id="pweb-close-button" onclick="document.location.href='<?php echo admin_url( 'admin.php?page=pwebcontact' ); ?>'">
                <i class="icomoon-close"></i> <span><?php _e( 'Close' ); ?></span>
            </button>

            <span id="pweb-save-status"></span>
            
            <a class="button button-primary right" id="pweb-docs-button" href="<?php echo $this->documentation_url; ?>" target="_blank">
                <i class="icomoon-support"></i> <span><?php _e( 'Documentation' ); ?></span>
            </a>
        </div>
    </div>
    
    <div id="pweb-settings-content">
        <?php $this->_load_tmpl('email', __FILE__); ?>
    </div>
    
    <?php wp_nonce_field( 'save-settings' ); ?>
    
</form>