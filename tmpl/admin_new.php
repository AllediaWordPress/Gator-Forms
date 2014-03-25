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
<div class="pweb-version pweb-clearfix">
    <?php _e('Version'); ?> 
    <?php echo $this->_get_version(); ?>
</div>

<h2><?php _e('Perfect Ajax Popup Contact Form', 'pwebcontact'); ?></h2>

<?php $this->_display_messages(); ?>

<p><?php esc_html_e( 'TODO description', 'pwebcontact' ); ?></p>

<?php if ($this->can_edit) : ?>
<div class="theme-browser pweb-panels pweb-clearfix">
<div class="themes">
    <div class="theme add-new-theme pweb-panel-box">
        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=pwebcontact&task=new' ), 'new-form'); ?>">
            <div class="theme-screenshot">
                <span></span>
            </div>
            <h3 class="theme-name"><?php _e( 'Create your first form', 'pwebcontact' ); ?></h3>
        </a>
    </div>
</div>
</div>
<?php endif; ?>