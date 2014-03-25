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

<h2>
    <?php _e('Perfect Ajax Popup Contact Form', 'pwebcontact'); ?>
    <?php if ($this->can_edit) : ?>
        <a class="add-new-h2" href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=pwebcontact&task=new' ), 'new-form'); ?>">
            <i class="icomoon-plus"></i> <?php echo esc_html_x('Add New', 'link'); ?></a>
    <?php endif; ?>
    <a class="add-new-h2" href="<?php echo $this->documentation_url; ?>" target="_blank">
        <i class="icomoon-support"></i> <?php _e( 'Documentation' ); ?></a>
</h2>

<?php $this->_display_messages(); ?>

<div class="theme-browser pweb-panels pweb-clearfix">
<div class="themes">

<?php foreach ($this->data as $form) : ?>
    <div class="theme pweb-panel-box">
        <div class="theme-screenshot">
			
		</div>
        <h3 class="theme-name">
            <?php echo $form->title ? esc_html($form->title) : '&nbsp;'; ?>
        </h3>
        <div class="theme-name pweb-position">
            <?php if ($form->position == 'shortcode') : ?>
                <input type="text" class="pweb-shortcode pweb-has-tooltip" readonly="readonly"
                       title="<?php esc_attr_e( 'Copy shortcode and paste into blog post or page.', 'pwebcontact' ); ?>" 
                       value="[pwebcontact id=<?php echo (int)$form->id; ?>]">
                <!--<button type="button" class="button">
                    <i class="icomoon-copy"></i>
                </button>-->
                <?php _e( 'shortcode', 'pwebcontact' ); ?>
            <?php elseif ($form->position == 'widget') : ?>
                TODO
            <?php elseif ($form->position == 'footer') : ?>
                TODO
            <?php endif; ?>
        </div>
        <div class="theme-name pweb-actions">
            <button type="button"<?php if (!$this->can_edit) echo ' disabled="disabled"'; ?> class="button button-primary" 
                    onclick="document.location.href='<?php echo admin_url( 'admin.php?page=pwebcontact&task=edit&id='.(int)$form->id ); ?>'">
                <i class="icomoon-pencil2"></i> <?php _e( 'Edit' ); ?>
            </button>
            <button type="button"<?php if (!$this->can_edit) echo ' disabled="disabled"'; ?> 
                    class="button pweb-action-toggle-state pweb-text-<?php echo $form->publish ? 'success' : 'danger'; ?> pweb-has-tooltip" 
                    title="<?php esc_attr_e( 'Toggle form publish state', 'pwebcontact' ); ?>" 
                    data-action="<?php echo admin_url( 'admin.php?page=pwebcontact&task=edit_state&id='.(int)$form->id.'&ajax=1&_wpnonce='. wp_create_nonce('edit-form-state_'.$form->id).'&state=' ); ?>"
                    data-state="<?php echo $form->publish; ?>">
                <i class="icomoon-<?php echo $form->publish ? 'checkmark-circle' : 'cancel-circle'; ?>"></i> 
            </button>
            <button type="button"<?php if (!$this->can_edit) echo ' disabled="disabled"'; ?> 
                    class="button pweb-has-tooltip" title="<?php esc_attr_e( 'Copy' ); ?>" 
                    onclick="document.location.href='<?php echo admin_url( 'admin.php?page=pwebcontact&task=copy&id='.(int)$form->id.'&_wpnonce='. wp_create_nonce('copy-form_'.$form->id) ); ?>'">
                <i class="icomoon-copy"></i> 
            </button>
            <button type="button"<?php if (!$this->can_edit) echo ' disabled="disabled"'; ?> 
                    class="button pweb-action-delete pweb-has-tooltip" title="<?php esc_attr_e( 'Delete' ); ?>" 
                    data-form-title="<?php echo esc_attr($form->title); ?>" 
                    data-action="<?php echo admin_url( 'admin.php?page=pwebcontact&task=delete&id='.(int)$form->id.'&ajax=1&_wpnonce='. wp_create_nonce('delete-form_'.$form->id) ); ?>">
                <i class="icomoon-remove2"></i> 
            </button>
        </div>
    </div>
<?php endforeach; ?>

    <div class="theme active pweb-panel-box pweb-panel-pro">
        <div class="theme-screenshot">
			
		</div>
        <h3 class="theme-name">
            <a class="button button-primary right" href="<?php echo $this->buy_pro_url; ?>" target="_blank">
                <i class="icomoon-cart"></i> <?php _e( 'Buy', 'pwebcontact' ); ?>
            </a>
            <?php _e( 'Pro', 'pwebcontact' ); ?>
        </h3>
    </div>
    
    <div class="theme active pweb-panel-box pweb-panel-support">
        <div class="theme-screenshot">
			
		</div>
        <h3 class="theme-name">
            <a class="button button-primary right" href="<?php echo $this->buy_support_url; ?>" target="_blank">
                <i class="icomoon-cart"></i> <?php _e( 'Buy', 'pwebcontact' ); ?>
            </a>
            <?php _e( 'Support', 'pwebcontact' ); ?>
        </h3>
    </div>
        
</div>
</div>

<div id="pweb-dialog-delete" title="<?php esc_attr_e( 'Confirm deletion', 'pwebcontact' ); ?>" style="display:none">
    <?php _e( 'Are you sure you want to delete form:', 'pwebcontact' ); ?> 
    <span class="pweb-dialog-form-title"></span>?
</div>