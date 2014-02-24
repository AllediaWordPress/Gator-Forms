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

$pwebcontact_admin = new PWebContact_Admin;

class PWebContact_Admin {
    
    protected $id = null;
    protected $view = null;
    protected $can_edit = false;
    protected $data = null;
    
    protected $notifications = array();
    protected $warnings = array();
    protected $errors = array();
    
    protected $documentation_url = 'http://www.perfect-web.co/wordpress/ajax-contact-form-popup/documentation';
    protected $buy_pro_url = 'https://www.perfect-web.co/order/subscriptions/22';
    protected $buy_support_url = 'https://www.perfect-web.co/order/subscriptions/21,22';
    
    protected static $pro = array(
        'options' => array(
            
        ),
        'params' => array(
            
        )
    );
    
    
    function __construct() {
        
        // initialize admin view
        add_action( 'admin_init', array($this, 'init') );
        
        // Configuration link in menu
        add_action( 'admin_menu', array($this, 'menu') );
        
        // Configuration link on plugins list
        add_filter( 'plugin_action_links', array($this, 'action_links'), 10, 2 );
    }
    
    
    function init() {
        
        if (!isset($_GET['page']) OR $_GET['page'] !== 'pwebcontact') {
            return;
        }
        
        $this->can_edit = current_user_can('manage_options');
        
        $task = isset($_GET['task']) ? $_GET['task'] : 'list';
        
        if ( $task == 'new' ) {
            
            if (!$this->can_edit) {
                // redirect to list view
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('You do not have sufficient permissions to create form!', 'pwebcontact')));
            }
            
            check_admin_referer( 'new-form' );
            
            // create new instance of form
            if ($this->_create_form()) {
                // redirect to edit view
                $this->_redirect('admin.php?page=pwebcontact&task=edit&id='.(int)$this->id);
            } 
            else {
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('Failed creating a new form!', 'pwebcontact')));
            }
        }
        elseif ( $task == 'copy' AND isset($_GET['id'])) {
            
            $this->id = (int)$_GET['id'];
            $this->view = 'edit';
            
            if (!$this->can_edit OR !$this->id) {
                // redirect to list view
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('You do not have sufficient permissions to copy form!', 'pwebcontact')));
            }
            else {
                check_admin_referer( 'copy-form_'.$this->id );
                
                $result = $this->_copy_form();
                $message = __($result ? 'Contact form has been successfully copied.' : 'Failed copying contact form!', 'pwebcontact');
                
                if ($result) {
                    $this->_redirect('admin.php?page=pwebcontact&task=edit&id='.$this->id.'&notification='.urlencode($message));
                }
                else {
                    $this->_redirect('admin.php?page=pwebcontact&error='.urlencode($message));
                }
            }
        }
        elseif ( $task == 'edit' AND isset($_GET['id'])) {
            
            $this->id = (int)$_GET['id'];
            $this->view = 'edit';
            
            if (!$this->can_edit OR !$this->id) {
                // redirect to list view
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('You do not have sufficient permissions to edit form!', 'pwebcontact')));
            }
            else {
                $this->_load_form();
                
                // load JS files
                wp_enqueue_script('pwebcontact_admin_script', plugins_url('media/js/jquery.admin-edit.js', __FILE__), 
                        array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-tooltip', 'jquery-ui-tabs'));
            }
        }
        elseif ( $task == 'save' AND isset($_POST['id'])) {
            
            $this->id = (int)$_POST['id'];
            $this->view = 'edit';
            
            if (!$this->can_edit OR !$this->id) {
                // redirect to list view
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('You do not have sufficient permissions to edit form!', 'pwebcontact')));
            }
            else {
                
                if (isset($_GET['ajax'])) {
                    check_ajax_referer( 'save-form_'.$this->id );
                    //wp_verify_nonce( $_POST['_wp_nonce'], 'save-form_'.$this->id );
                }
                else {
                    check_admin_referer( 'save-form_'.$this->id );
                }
                
                $result = $this->_save_form();
                $message = __($result ? 'Contact form has been successfully saved.' : 'Failed saving contact form!', 'pwebcontact');
                
                if (isset($_GET['ajax'])) {
                    header('Content-type: application/json');
                    die(json_encode(array(
                        'success' => $result,
                        'message' => $message
                    )));
                }
                else {
                    $this->_redirect('admin.php?page=pwebcontact&task=edit&id='.$this->id.
                            '&'.($result ? 'notification' : 'error').'='.urlencode($message));
                }
            }
        }
        elseif ( $task == 'delete' AND isset($_GET['id'])) {
            
            $this->id = (int)$_GET['id'];
            $this->view = 'list';
            
            if (!$this->can_edit OR !$this->id) {
                // redirect to list view
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('You do not have sufficient permissions to delete form!', 'pwebcontact')));
            }
            else {
                
                if (isset($_GET['ajax'])) {
                    check_ajax_referer( 'delete-form_'.$this->id );
                    //wp_verify_nonce( $_POST['_wp_nonce'], 'delete-form_'.$this->id );
                }
                else {
                    check_admin_referer( 'delete-form_'.$this->id );
                }
                
                $result = $this->_delete_form();
                $message = __($result ? 'Contact form has been successfully deleted.' : 'Failed deleting contact form!', 'pwebcontact');
                
                if (isset($_GET['ajax'])) {
                    header('Content-type: application/json');
                    die(json_encode(array(
                        'success' => $result,
                        'message' => $message
                    )));
                }
                else {
                    $this->_redirect('admin.php?page=pwebcontact'.
                            '&'.($result ? 'notification' : 'error').'='.urlencode($message));
                }
            }
        }
        elseif ( $task == 'edit_state' AND isset($_GET['id']) AND isset($_GET['state'])) {
            
            $this->id = (int)$_GET['id'];
            $this->view = 'list';
            $state = (int)$_GET['state'];
            
            if (!$this->can_edit OR !$this->id) {
                // redirect to list view
                $this->_redirect('admin.php?page=pwebcontact&error='.
                        urlencode(__('You do not have sufficient permissions to edit form state!', 'pwebcontact')));
            }
            else {
                
                if (isset($_GET['ajax'])) {
                    check_ajax_referer( 'edit-form-state_'.$this->id );
                    //wp_verify_nonce( $_POST['_wp_nonce'], 'edit-form-state_'.$this->id );
                }
                else {
                    check_admin_referer( 'edit-form-state_'.$this->id );
                }
                
                $result = $this->_save_form_state($state);
                $message = __($result ? 'Contact form has been successfully '.($state ? 'published' : 'unpublished').'.' : 'Failed changing contact form state!', 'pwebcontact');
                
                if (isset($_GET['ajax'])) {
                    header('Content-type: application/json');
                    die(json_encode(array(
                        'success' => $result,
                        'message' => $message,
                        'state' => $state
                    )));
                }
                else {
                    $this->_redirect('admin.php?page=pwebcontact'.
                            '&'.($result ? 'notification' : 'error').'='.urlencode($message));
                }
            }
        }
        elseif ( $task == 'list' OR $task == '' ) {
            
            $this->view = 'list';
            
            if (!$this->can_edit AND !isset($_GET['error'])) {
                $this->errors[] = __( 'You do not have sufficient permissions to create form!', 'pwebcontact' );
            }
            
            $this->_check_requirements();
            $this->_load_forms();
            
            // load JS files
            wp_enqueue_script('pwebcontact_admin_script', plugins_url('media/js/jquery.admin-list.js', __FILE__), 
                    array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-dialog', 'jquery-ui-tooltip'));
            
            wp_localize_script('pwebcontact_admin_script', 'pwebcontact_l10n', array(
                'delete' => __( 'Delete' ),
                'cancel' => __( 'Cancel' )
            ));
            
            // load CSS
            wp_enqueue_style('wp-jquery-ui-dialog');
        }
        
        // load CSS
        //wp_enqueue_style('pwebcontact_jquery_ui_style', plugins_url('media/css/ui/jquery-ui-1.10.4.custom.css', __FILE__));
        wp_enqueue_style('pwebcontact_admin_style', plugins_url('media/css/admin.css', __FILE__));
        wp_enqueue_style('pwebcontact_admin_wp_style', plugins_url('media/css/admin_wp.css', __FILE__), array('dashicons'));
        wp_enqueue_style('pwebcontact_icomoon_style', plugins_url('media/css/icomoon.css', __FILE__));
        
        //add_action('admin_head', array($this, 'admin_head'));
    }
    
    
    function menu() {

        $title = __('Perfect Ajax Popup Contact Form', 'pwebcontact');
        
        if (isset($_GET['task']) AND $_GET['task'] == 'edit') {
            $title = __('Edit') .' &lsaquo; '. $title;
        }
        
        add_menu_page($title, __('Perfect Contact Forms', 'pwebcontact'), 
                'manage_options', 'pwebcontact', array($this, 'configuration'));
    }
    
    
    function action_links( $links, $file ) {

        if ( $file == plugin_basename(dirname(__FILE__).'/pwebcontact.php') ) {
            $links[] = '<a href="' . admin_url( 'admin.php?page=pwebcontact' ) . '">'.__( 'Forms list', 'pwebcontact' ).'</a>';
        }

        return $links;
    }
    
    
    function admin_head() {

        //TODO
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    
});
</script>
<?php
    }
    
    
    protected function _load_forms() {
        
        global $wpdb;
        
        if ($this->data === null) {
        
            $sql =  'SELECT `id`, `title`, `publish`, `position`, `layout` '.
                    'FROM `'.$wpdb->prefix.'pwebcontact_forms` ';
            $this->data = $wpdb->get_results($sql);
            
            if ($this->data === null) {
                $this->data = array();
            }
        }
    }
    
    
    protected function _load_form() {
        
        global $wpdb;
        
        if ($this->data === null AND $this->id) {
        
            $sql =  $wpdb->prepare('SELECT `title`, `publish`, `position`, `layout`, `params` '.
                    'FROM `'.$wpdb->prefix.'pwebcontact_forms` '.
                    'WHERE `id` = %d', $this->id);
            $this->data = $wpdb->get_row($sql);
            
            if ($this->data === null) {
                $this->data = false;
            }
            else {
                $this->data->params = json_decode($this->data->params, true);
            }
        }
    }
    
    
    protected function _get_param($key = null, $default = null) {
        
        if (isset($this->data->params[$key]) AND $this->data->params[$key]) {
            return $this->data->params[$key];
        }
        return $default;
    }
    
    
    protected function _get_post($key = null, $default = null) {
        
        if (isset($_POST[$key]) AND $_POST[$key]) {
            return $_POST[$key];
        }
        return $default;
    }
    
    
    protected function _redirect($url = null)
    {
        $url = admin_url($url);
        if (wp_redirect($url)) {
            die();
        }
        else {
            die('<script>document.location.href="'.$url.'";</script>');
        }
    }
    
    
    protected function _check_requirements() {
        
        global $wp_version;
        
        $result = true;
        
        //TODO get result from session
        //TODO if result == true then return it
        
        if (version_compare( $wp_version, '2.8', '<' )) {
            $result = false;
            $this->errors[] = __('This plugin is compatible with WordPress 2.8 or higher.', 'pwebcontact' );
        }
        
        //TODO save result in session
        
        return $result;
    }
    
    
    protected function _create_form() {
        
        global $wpdb;
        
        $data = array(
            'title' => 'Contact form',
            'publish' => 1,
            'position' => 'footer',
            'layout' => 'slidebox',
            'params' => '{}'
        );
        
        if ($wpdb->insert($wpdb->prefix.'pwebcontact_forms', $data)) {
            $this->id = (int)$wpdb->insert_id;
            return true;
        }
        return false;
    }
    
    
    protected function _copy_form() {
        
        global $wpdb;
        
        $sql =  $wpdb->prepare('SELECT `title`, `position`, `layout`, `params` '.
                    'FROM `'.$wpdb->prefix.'pwebcontact_forms` '.
                    'WHERE `id` = %d', $this->id);
        $data = $wpdb->get_row($sql, ARRAY_A);
        
        if (!$data) return false;
        
        $data['title'] .= __( ' (Copy)', 'pwebcontact' );
        $data['publish'] = 0;
        
        if ($wpdb->insert($wpdb->prefix.'pwebcontact_forms', $data)) {
            $this->id = (int)$wpdb->insert_id;
            return true;
        }
        return false;
    }
    
    
    protected function _save_form() {
        
        global $wpdb;
        
        // Get params from request
        $this->data = new stdClass();
        $this->data->params = $this->_get_post('params');
        
        // Validate params
        $params = array();
        $params['debug'] = (int)$this->_get_param('debug', 0);

        // Update data
        return false !== $wpdb->update($wpdb->prefix.'pwebcontact_forms', array(
                    'title' => $this->_get_post('title'),
                    'publish' => $this->_get_post('publish', 1),
                    'position' => $this->_get_post('position'),
                    'layout' => $this->_get_post('layout'),
                    'params' => json_encode($params)
                ), array('id' => $this->id), array('%s', '%d', '%s', '%s', '%s'));
    }
    
    
    protected function _save_form_state($state = 1) {
        
        global $wpdb;
        
        // Update data
        return false !== $wpdb->update($wpdb->prefix.'pwebcontact_forms', array('publish' => (int)$state), array('id' => $this->id));
    }
    
    
    protected function _delete_form() {
        
        global $wpdb;
        
        return false !== $wpdb->delete($wpdb->prefix.'pwebcontact_forms', array('id' => $this->id), array('%d'));
    }
    
    
    function configuration() {

?>
<div class="wrap pweb-wrap pweb-view-<?php echo $this->view; ?>">
    
    <?php $this->_display_messages(); ?>
    
    <?php 
    if ($this->view == 'list') : 
        
        if (count($this->data)) : 
            $this->_display_forms_list();
        else : 
            $this->_display_create_form();
        endif;
        
    elseif ($this->view == 'edit') : 
        $this->_display_edit_form();
    
    endif; ?>
    
    <p class="pweb-copyrights">
		Copyright &copy; 2014 Perfect Web sp. z o.o., All rights reserved. Distributed under GPL by
		<a href="http://www.perfect-web.co/wordpress" target="_blank"><strong>Perfect-Web.co</strong></a>.<br>
		All other trademarks and copyrights are property of their respective owners.
	</p>
</div>
<?php 

    }
    
    
    protected function _display_messages() {
        
        if (isset($_GET['error']) AND $_GET['error']) {
            $this->errors[] = urldecode($_GET['error']);
        }
        
        if (count($this->errors)) {
?>
<div class="error pweb-clearfix"><p><strong><?php echo implode('<br>', $this->errors); ?></strong></p></div>
<?php
		}
        
        if (isset($_GET['notification']) AND $_GET['notification']) {
            $this->notifications[] = urldecode($_GET['notification']);
        }
		if (count($this->notifications)) {
?>
<div class="updated pweb-clearfix"><p><strong><?php echo implode('<br>', $this->notifications); ?></strong></p></div>
<?php
		}
    }
    
    
    protected function _display_create_form() {
        
?>
<div class="pweb-version pweb-clearfix">
    <?php _e('Version'); ?> 
    <?php echo $this->_get_version(); ?>
</div>

<h2><?php _e('Perfect Ajax Popup Contact Form', 'pwebcontact'); ?></h2>

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

<?php  
    }
    
    
    protected function _display_forms_list() {
        
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
<?php
    }
    
    
    protected function _display_edit_form() {
        
?>
<form name="edit" method="post" action="<?php echo esc_attr(admin_url( 'admin.php?page=pwebcontact&task=save' )); ?>">
    
    <div class="pweb-toolbar">
        <h2><?php _e( 'Edit' ); ?></h2>
        
        <input type="text" name="title" value="<?php echo esc_attr($this->data->title); ?>" placeholder="<?php esc_attr_e( 'Form name', 'pwebcontact' ); ?>">
        
        <button type="submit" class="button button-primary">
            <i class="icomoon-disk"></i> <?php _e( 'Save' ); ?>
        </button>
        <button type="button" class="button" onclick="document.location.href='<?php echo admin_url( 'admin.php?page=pwebcontact' ); ?>'">
            <i class="icomoon-close"></i> <?php _e( 'Close' ); ?>
        </button>
        
        <span class="pweb-save-status"></span>
        
        <a class="button button-primary right" href="<?php echo $this->buy_support_url; ?>" target="_blank">
            <i class="icomoon-cart"></i> <?php esc_html_e( 'Buy Pro & Get Support', 'pwebcontact' ); ?>
        </a>
        <a class="button button-primary right" href="<?php echo $this->documentation_url; ?>" target="_blank">
            <i class="icomoon-support"></i> <?php _e( 'Documentation' ); ?>
        </a>
    </div>
    
    <div id="pweb-tabs">
        
        <h2 class="nav-tab-wrapper">
            <a href="#pweb-tab-location" class="nav-tab nav-tab-active"><?php esc_html_e( 'Location & Effects', 'pwebcontact' ); ?></a>
            <a href="#pweb-tab-fields" class="nav-tab"><?php esc_html_e( 'Fields', 'pwebcontact' ); ?></a>
            <a href="#pweb-tab-layout" class="nav-tab"><?php esc_html_e( 'Layout', 'pwebcontact' ); ?></a>
            <a href="#pweb-tab-submitted" class="nav-tab"><?php esc_html_e( 'After submitting', 'pwebcontact' ); ?></a>
            <a href="#pweb-tab-email" class="nav-tab"><?php esc_html_e( 'Email settings', 'pwebcontact' ); ?></a>
        </h2>
        
        <div id="pweb-tab-location" class="nav-tab-content nav-tab-content-active pweb-clearfix">
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
                    <h3><?php _e( 'Movement effect', 'pwebcontact' ); ?></h3>
                    <div class="pweb-location-step-tab" id="pweb-location-effect">
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
                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'handler',
                        'label' => 'How do you want to display your form before it is opened?',
                        'class' => 'pweb-related',
                        'default' => 'tab',
                        'required' => true,
                        'is_parent' => true,
                        'options' => array(
                            array(
                                'value' => 'button',
                                'name' => 'Button',
                                'class' => 'pweb-layout-button pweb-related-accordion pweb-related-modal-button',
                                'is_parent' => true
                            ),
                            array(
                                'value' => 'tab',
                                'name' => 'Toggler Tab',
                                'class' => 'pweb-layout-tab pweb-related-slidebox pweb-related-modal',
                                'is_parent' => true
                            ),
                            array(
                                'value' => 'static',
                                'name' => 'Always opened inside page content',
                                'class' => 'pweb-layout-static pweb-related-static'
                            ),
                            array(
                                'value' => 'hidden',
                                'name' => 'Hidden',
                                'class' => 'pweb-layout-hidden pweb-related-modal pweb-related-accordion pweb-related-slidebox pweb-related-modal-button'
                            )
                        )
                    )); ?>

                    <?php echo $this->_get_field(array(
                        'type' => 'text',
                        'label' => 'Define text shown on Toggler Tab or Button',
                        'name' => 'toggler_name',
                        'parent' => array('handler_button', 'handler_tab')
                    )); ?>

                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'toggler_position',
                        'label' => 'Toggler Tab position',
                        'parent' => 'handler_tab',
                        'default' => 'left',
                        'is_parent' => true,
                        'options' => array(
                            array(
                                'value' => 'left',
                                'name' => 'Left'
                            ),
                            array(
                                'value' => 'right',
                                'name' => 'Right'
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
                                'label' => 'Position offset [px, %]',
                                'name' => 'offset',
                                'class' => 'pweb-unit',
                                'parent' => array('layout_slidebox', 'handler_tab')
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'type' => 'text',
                                'label' => 'Layer level (CSS z-index)',
                                'name' => 'zindex',
                                'class' => 'pweb-int',
                                'parent' => array('layout_slidebox', 'layout_modal')
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'type' => 'radio',
                                'name' => 'toggler_icon',
                                'label' => 'Toggler Tab icon',
                                'default' => 0,
                                'is_parent' => true,
                                'parent' => array('handler_tab', 'handler_button'),
                                'options' => array(
                                    array(
                                        'value' => 0,
                                        'name' => 'Disabled'
                                    ),
                                    array(
                                        'value' => 'icomoon',
                                        'name' => 'IcoMoon'
                                    ),
                                    array(
                                        'value' => 'gallery',
                                        'name' => 'Gallery'
                                    ),
                                    array(
                                        'value' => 'custom',
                                        'name' => 'Custom'
                                    )
                                )
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'type' => 'icomoon',
                                'name' => 'toggler_icomoon',
                                'label' => 'Toggler Tab IcoMoon',
                                'parent' => array('toggler_icon_icomoon')
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'type' => 'radio',
                                'name' => 'toggler_vertical',
                                'label' => 'Vertical Toggler Tab',
                                'default' => 0,
                                'is_parent' => true,
                                'parent' => array('toggler_position_left', 'toggler_position_right'),
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
                                'name' => 'toggler_rotate',
                                'label' => 'Rotate Toggler Tab text',
                                'default' => 1,
                                'parent' => array('toggler_vertical_1'),
                                'options' => array(
                                    array(
                                        'value' => -1,
                                        'name' => '-90&deg; (counter-clockwise)'
                                    ),
                                    array(
                                        'value' => 1,
                                        'name' => '90&deg; (clockwise)'
                                    )
                                )
                            )); ?>
                            
                            <?php echo $this->_get_field(array(
                                'type' => 'filelist',
                                'name' => 'toggler_font',
                                'label' => 'TTF font file',
                                'default' => 'NotoSans-Regular',
                                'filter' => '\.ttf$',
                                'directory' => 'media/fonts',
                                'parent' => array('toggler_vertical_1')
                            )); ?>
                        </div>
                    </div>
                </div>
                
                <div class="pweb-location-options" id="pweb-location-effect-options">
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
                </div>
                
                <div class="pweb-location-options" id="pweb-location-after-options">
                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'layout',
                        'label' => 'How do you want to display your form after opening?',
                        'class' => 'pweb-related',
                        'default' => 'slidebox',
                        'required' => true,
                        'is_parent' => true,
                        'options' => array(
                            array(
                                'value' => 'slidebox',
                                'name' => 'Box at page edge',
                                'class' => 'pweb-layout-slidebox pweb-related-slidebox'
                            ),
                            array(
                                'value' => 'modal',
                                'name' => 'Lightbox window',
                                'class' => 'pweb-layout-modal pweb-related-modal pweb-related-modal-button'
                            ),
                            array(
                                'value' => 'accordion',
                                'name' => 'Accordion inside page content',
                                'class' => 'pweb-layout-accordion pweb-related-accordion'
                            ),
                            array(
                                'value' => 'static',
                                'name' => 'Static form inside page content',
                                'class' => 'pweb-layout-static pweb-related-static'
                            )
                        )
                    )); ?>
                </div>
                
                <div class="pweb-location-options" id="pweb-location-place-options">
                    <?php echo $this->_get_field(array(
                        'type' => 'radio',
                        'name' => 'position',
                        'group' => 'options',
                        'label' => 'Where do you want to display your form?',
                        'class' => 'pweb-related',
                        'default' => 'footer',
                        'required' => true,
                        'options' => array(
                            array(
                                'value' => 'footer',
                                'name' => 'On all pages',
                                'class' => 'pweb-related-slidebox pweb-related-modal'
                            ),
                            array(
                                'value' => 'shortcode',
                                'name' => 'On selected pages with short code',
                                'class' => 'pweb-related-slidebox pweb-related-modal pweb-related-accordion pweb-related-static pweb-related-modal-button'
                            ),
                            array(
                                'value' => 'widget',
                                'name' => 'In widget',
                                'class' => 'pweb-related-accordion pweb-related-static pweb-related-modal-button'
                            )
                        )
                    )); ?>
                </div>
                
            </div>
        </div>
        
        <div id="pweb-tab-fields" class="nav-tab-content pweb-clearfix">
            tab 2
        </div>
        
        <div id="pweb-tab-layout" class="nav-tab-content pweb-clearfix">
            <?php echo $this->_get_field(array(
                'type' => 'color',
                'name' => 'toggler_bg',
                'label' => 'Toggler Tab color',
                'parent' => array('handler_tab', 'handler_button'),
            )); ?>
        </div>
        
        <div id="pweb-tab-submitted" class="nav-tab-content pweb-clearfix">
            tab 4
        </div>
        
        <div id="pweb-tab-email" class="nav-tab-content pweb-clearfix">
            tab 5
        </div>
        
    </div>
    

    <input type="hidden" name="id" value="<?php echo (int)$this->id; ?>">
    <?php wp_nonce_field( 'save-form_'.$this->id ); ?>
    
</form>
<?php
    }
    
    
    protected function _get_version() {
        
        $data = get_plugin_data(dirname(__FILE__).'/pwebcontact.php');
        return $data['Version'];
    }
    
    
    protected function _get_field( $opt = array() ) {
        
        $opt = array_merge(array(
            'id' => null,
            'name' => null,
            'group' => 'params',
            'label' => null,
            'desc' => null,
            'tooltip' => null,
            'parent' => null,
            'disabled' => false,
            'is_pro' => null
        ), $opt);
        
        extract( $opt );
        
        if (empty($id)) {
            $id = 'pweb_'. $group .'_'. $name;
        }
        if ($is_pro === null) {
            $is_pro = in_array($name, self::$pro[$group]);
        }
        if ($parent !== null) {
            $names = array();
            foreach((array)$parent as $parent_name) {
                $names[] = 'pweb_'. $group .'_'.$parent_name;
            }
            $parent = ' pweb-child '.implode(' ', $names);
        }
        
        return 
                '<div class="pweb-field'
                .($parent ? $parent : '')
                .($is_pro === true ? ' pweb-pro' : '')
                .($disabled === true ? ' pweb-disabled' : '')
                .'">'.
                    ($label ? $this->_get_label($opt) : '').
                    '<div class="pweb-field-control'. ($tooltip ? ' pweb-has-tooltip' : '') .'"'. 
                            ($tooltip ? ' title="'. esc_attr__($tooltip, 'pwebcontact') .'"' : '') .'>'.
                        $this->_get_field_control($opt).
                        ($desc ? '<div class="pweb-field-desc">'. esc_html__($desc, 'pwebcontact') .'</div>' : '').
                    '</div>'.
                '</div>';
    }
    
    
    protected function _get_label( $opt = array() ) {
        
        $opt = array_merge(array(
            'id' => null,
            'name' => null,
            'group' => 'params',
            'label' => null,
            'required' => false,
            'is_pro' => null
        ), $opt);
        
        extract( $opt );
        
        if (empty($id)) {
            $id = 'pweb_'. $group .'_'. $name;
        }
        if ($is_pro === null) {
            $is_pro = in_array($name, self::$pro[$group]);
        }
        
        return '<label for="'.esc_attr($id).'"'. ($required ? ' class="required"' : '') .'>' . 
                __($label, 'pwebcontact') . 
                ($required ? ' <span class="pweb-star">*</span>' : '') .
                ($is_pro === true ? ' <span class="pweb-pro">Pro</span>' : '') .
                '</label>';
    }
    
    
    protected function _get_field_control( $opt = array() ) {
        
        $opt = array_merge(array(
            'type' => 'text',
            'id' => null,
            'name' => null,
            'group' => 'params',
            'value' => null,
            'default' => null,
            'class' => null,
            'required' => false,
            'disabled' => false,
            'readonly' => false,
            'attributes' => array(),
            'options' => array(),
            'is_parent' => false,
            'is_pro' => null
        ), $opt);
        
        extract( $opt );
        
        $html = $html_after = '';
        
        
        if (empty($id)) {
            $id = 'pweb_'. $group .'_'. $name;
        }
        $attributes['id'] = $id;
        
        $field_name = esc_attr($group.'['.$name.']');
        
        if ($is_pro === null) {
            $is_pro = in_array($name, self::$pro[$group]);
        }
        
        
        if ($default !== null AND ($value === null OR $value === '')) {
            $value = $default;
        }
        
        if ($class) {
            $attributes['class'] .= ' '.$class;
        }
        if ($required) {
            $attributes['class'] .= ' required';
            $attributes['required'] = 'required';
        }
        if ($is_pro === true OR $disabled) {
            $disabled = true;
            $attributes['disabled'] = 'disabled';
        }
        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }
        if ($is_parent === true) {
            $attributes['class'] .= ' pweb-parent';
        }
        
        
        // extend HTML fields with custom types
        switch ($type) {
            
            case 'filelist' AND isset($directory):
                
                $type = 'select';
                
                if (!count($options)) {
                    $options = array(array(
                        'value' => '',
                        'name' => 'Select option'
                    ));
                }
                
                if (is_dir( dirname(__FILE__) .'/'. trim($directory, '/\\') )) {
                    $directory = dirname(__FILE__) .'/'. trim($directory, '/\\');
                }
                elseif (is_dir( ABSPATH .'/'. trim($directory, '/\\') )) {
                    $directory = ABSPATH .'/'. trim($directory, '/\\');
                }
                else {
                    $directory = null;
                }
                
                if ($directory) {
                    $dir = new DirectoryIterator($directory);
                    foreach( $dir as $item )
                    {
                        if ($item->isFile()) 
                        {
                            if (strpos($item->getFilename(), 'index.') === false AND preg_match('/'.$filter.'/i', $item->getFilename())) {
                                $pos = strrpos($item->getFilename(), '.', 3);
                                $file_name = substr($item->getFilename(), 0, $pos);
                                $options[] = array(
                                    'value' => $file_name,
                                    'name' => $file_name
                                );
                            }
                        }
                    }
                }
                break;
            
            
            case 'icomoon':

                $type = 'select';
                
                $css = file_get_contents( dirname(__FILE__).'/media/css/icomoon.css' );
                if (preg_match_all('/\.(icomoon-[^:]+):before\s*\{\s*content:\s*"\\\([^"]+)";\s*\}/i', $css, $matches, PREG_SET_ORDER))
                {
                    $attributes['class'] .= ' pweb-icomoon-list';
                    
                    foreach ($matches as $icon) {
                        $options[] = array(
                            'value' => $icon[2],
                            'name' => '&#x'.$icon[2].';'
                        );
                    }
                }
                break;
            
            
            case 'image':

                $type = 'text';
                break;
            
            
            case 'wp_user':

                $type = 'select';
                $blog_id = get_current_blog_id();
                
                if (!count($options)) {
                    $options = array(array(
                        'value' => '',
                        'name' => 'Select Administrator'
                    ));
                }
                
                $users = get_users('blog_id='.$blog_id.'&orderby=display_name&role=administrator');
                if ($users) {
                    foreach ($users as $user) {
                        $options[] = array(
                            'value' => $user->id,
                            'name' => $user->display_name .'<'. $user->user_email .'>'
                        );
                    }
                }
                break;
            
            
            case 'text_button':

                $type = 'text';
                $html_after .= '<button type="button" class="button">'. esc_html__($button, 'pwebcontact') .'</button>';
                break;
            
            
            case 'color':

                $type = 'text';
                wp_enqueue_script( 'wp-color-picker' );
                wp_enqueue_style( 'wp-color-picker' );
                $html_after .= '<script type="text/javascript">jQuery(document).ready(function($){$("#'.$id.'").wpColorPicker()})</script>';
                break;
        }
        
        
        // default HTML field types
        switch ($type) {
            
            case 'text':
                
                $html .= '<input type="text" name="'.$field_name.'" value="'. esc_attr($value) .'"'. $this->_attr_to_str($attributes) .'>';
                break;
                
                
            case 'textarea':
                
                $attributes['cols'] = isset($attributes['cols']) ? $attributes['cols'] : 30;
                $attributes['rows'] = isset($attributes['rows']) ? $attributes['rows'] : 5;
                
                $html .= '<textarea name="'.$field_name.'"'. $this->_attr_to_str($attributes) .'>'. esc_attr($value) .'</textarea>';
                break;
                
                
            case 'select':
                
                if (isset($attributes['multiple'])) {
                    $field_name .= '[]';
                    $attributes['multiple'] = 'multiple';
                    if (!isset($attributes['size']) OR empty($attributes['size'])) {
                        $attributes['size'] = 4;
                    }
                }
                $html .= '<select name="'.$field_name.'"'. $this->_attr_to_str($attributes) .'>';
                foreach ($options as $option) {
                    
                    if ($is_pro === false AND !isset($option['disabled']) AND in_array($name.':'.$option['value'], self::$pro[$group]) ) {
                        $option['disabled'] = true;
                    }
                    
                    $html .= '<option value="'.esc_attr($option['value']).'"'. selected($value, $option['value'], false) 
                            . (isset($attributes['disabled']) OR isset($option['disabled']) ? ' disabled="disabled"' : '') 
                            . '>'. esc_html__($option['name'], 'pwebcontact') .'</option>';
                }
                $html .= '</select>';
                break;
                
                
            case 'radio':
            case 'checkbox':
                
                $html .= '<fieldset'. $this->_attr_to_str($attributes) .'>';
                
                if ($type == 'checkbox' AND count($options) > 1) {
                    $field_name .= '[]';
                }
                
                foreach ($options as $option) {
                    
                    if ($is_pro === false AND !isset($option['disabled']) AND in_array($name.':'.$option['value'], self::$pro[$group]) ) {
                        $option['disabled'] = true;
                    }
                    if (isset($option['parent'])) {
                        $names = array();
                        foreach((array)$option['parent'] as $parent_name) {
                            $names[] = 'pweb_'. $group .'_'.$parent_name;
                        }
                        $option['class'] .= ' pweb-child '.implode(' ', $names);
                    }
                    if (isset($option['tooltip'])) {
                        $option['class'] .= ' pweb-has-tooltip';
                    }
                    
                    
                    $option_id = $id .'_'. preg_replace('/[^a-z0-9-_]/i', '', $option['value']);
                    
                    $html .= '<div class="pweb-field-option'
                            . (isset($option['class']) ? ' '.esc_attr($option['class']) : '').'"'
                            . (isset($option['tooltip']) ? ' title="'. esc_attr__($option['tooltip'], 'pwebcontact') .'"' : '')
                            . '>';
                    
                    $html .= '<input type="'.$type.'" name="'.$field_name.'" id="'.$option_id.'"'
                            . ' value="'.esc_attr($option['value']).'"'. checked($value, $option['value'], false) 
                            . ((isset($attributes['disabled']) OR isset($option['disabled'])) ? ' disabled="disabled"' : '') 
                            . (($is_parent === true OR isset($option['is_parent'])) ? ' class="pweb-parent"' : '')
                            . '>';
                    
                    $html .= '<label for="'.$option_id.'" id="'.$option_id.'-lbl"'
                            . '>'. esc_html__($option['name'], 'pwebcontact') .'</label>';
                    
                    $html .= '</div>';
                }
                $html .= '</fieldset>';
                break;
        }
        
        return $html . $html_after;
    }
    
    protected function _attr_to_str($attributes = array()) {
        
        $attr = '';
        foreach ($attributes as $name => $value) {
            $attr .= ' '.$name.'="'.esc_attr($value).'"';
        }
        return $attr;
    }
}