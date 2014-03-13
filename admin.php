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
                        array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-tooltip', 'jquery-ui-tabs', 'jquery-ui-sortable'));
                wp_enqueue_script('pwebcontact_admin_fields_script', plugins_url('media/js/jquery.admin-fields.js', __FILE__));
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
    
    
    protected function _load_tmpl($name = '', $preffix = __FILE__) {
        
        $path = plugin_dir_path(__FILE__).'tmpl/'.basename($preffix, '.php').'_'.$name.'.php';
        if (is_file($path)) {
            include $path;
        }
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
        
        $this->_load_tmpl('new'); 
    }


    protected function _display_forms_list() {
        
        $this->_load_tmpl('list');
    }


    protected function _display_edit_form() {
        
        $this->_load_tmpl('edit');
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
                '<div class="pweb-field pweb-field-'.$type
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
        elseif (count($options)) {
            foreach ($options as $option) {
                if (isset($option['is_parent']) AND $option['is_parent'] === true) {
                    $attributes['class'] .= ' pweb-parent';
                    break;
                }
            }
        }
        
        
        // extend HTML fields with custom types
        switch ($type) {
            
            case 'filelist' AND isset($directory):
                
                $type = 'select';
                
                if (!count($options)) {
                    $options = array(array(
                        'value' => '',
                        'name' => '- Select option -'
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
                                if (isset($strip_ext) AND $strip_ext) {
                                    $pos = strrpos($item->getFilename(), '.', 3);
                                    $file_name = substr($item->getFilename(), 0, $pos);
                                }
                                else {
                                    $file_name = $item->getFilename();
                                }
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
                        'name' => '- Select Administrator -'
                    ));
                }
                
                $users = get_users('blog_id='.$blog_id.'&orderby=display_name&role=administrator');
                if ($users) {
                    foreach ($users as $user) {
                        $options[] = array(
                            'value' => $user->id,
                            'name' => $user->display_name .' <'. $user->user_email .'>'
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
                $html_after .= '<script type="text/javascript">'
                        . 'jQuery(document).ready(function($){'
                            . '$("#'.$id.'").wpColorPicker({'
                                . 'change:function(e,ui){'
                                    . '$(this).trigger("change")'
                                . '},'
                                . 'clear:function(e,ui){'
                                    . '$(this).trigger("change")'
                                . '}'
                            . '})'
                        . '})'
                    . '</script>';
                break;
        }
        
        
        // default HTML field types
        switch ($type) {
            
            case 'text':
            case 'email':
            case 'hidden':
                
                $html .= '<input type="'.$type.'" name="'.$field_name.'" value="'. esc_attr($value) .'"'. $this->_attr_to_str($attributes) .'>';
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
                    if (!isset($option['name'])) {
                        $option['name'] = (string)$option['value'];
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
                            . (($is_parent === true OR (isset($option['is_parent']) AND $option['is_parent'] === true)) ? ' class="pweb-parent"' : '')
                            . '>';
                    
                    $html .= '<label for="'.$option_id.'" id="'.$option_id.'-lbl"'
                            . '>'. __($option['name'], 'pwebcontact') .'</label>';
                    
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