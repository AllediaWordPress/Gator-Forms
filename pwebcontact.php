<?php 
/**
 * Plugin Name: Perfect Ajax Popup Contact Form
 * Plugin URI: http://www.perfect-web.co/wordpress/ajax-contact-form-popup
 * Description: 
 * Version: 1.0.0
 * Author: Piotr Moćko
 * Author URI: http://www.perfect-web.co
 * License: GPLv3
 */

// No direct access
function_exists('add_action') or die;


if ( is_admin() ) {
    
    require_once dirname( __FILE__ ) . '/install.php';
    require_once dirname( __FILE__ ) . '/admin.php';
    
    register_activation_hook( __FILE__, 'pwebcontact_install' );
    register_uninstall_hook( __FILE__, 'pwebcontact_uninstall' );
} 
else {
    
    add_action('init', array('PWebContact', 'init'));
    
    add_action('wp_footer', array('PWebContact', 'displayFormsInFooter'), 100);
    
    add_shortcode('pwebcontact', array('PWebContact', 'displayFormByShortcode'));
}

require_once dirname( __FILE__ ) . '/widget.php';


class PWebContact
{
	// current module ID
	protected static $form_id       = 0;
	// multiple instances
	protected static $params 		= array();
	protected static $fields 		= array();
    protected static $forms 		= array();
	// only one instance
	protected static $data 			= array();
	protected static $email_tmpls 	= array();
	protected static $email_vars 	= array();
	protected static $logs 			= array();
	
    protected static $headers       = array();
	protected static $sys_info 		= null;
	protected static $loaded 		= array(
										'init' 			=> false,
                                        'text' 			=> false,
										'uploader_text' => false,
										'debug_js' 		=> false,
										'ie_css' 		=> false
									);
    

    public static function init() 
	{
        if (!self::$loaded['init']) 
		{
			self::$loaded['init'] = true;
            
            $media_path = dirname(__FILE__) . '/media/';
            $media_url = plugins_url('media/', __FILE__);
            
            $debug = ((defined('WP_DEBUG') AND WP_DEBUG === true) OR $_GET['debug'] OR get_option('pwebcontact_debug', false));
            define('PWEBCONTACT_DEBUG', $debug);
            
            // Register scripts
            wp_register_script('pwebcontact-bootstrap', $media_url.'js/bootstrap'.($debug ? '' : '.min').'.js', array('jquery'), null, true);
            wp_register_script('pwebcontact-bootstrap-2', $media_url.'js/bootstrap-2.3.2'.($debug ? '' : '.min').'.js', array('jquery'), null, true);
            
            wp_register_script('pwebcontact-jquery-iframe-transport', $media_url.'js/jquery.iframe-transport.js', array('jquery'), null, true);
            wp_register_script('pwebcontact-jquery-fileupload-process', $media_url.'js/jquery.fileupload-process.js', array('jquery'), null, true);
            wp_register_script('pwebcontact-jquery-fileupload-validate', $media_url.'js/jquery.fileupload-validate.js', array('jquery'), null, true);
            wp_register_script('pwebcontact-jquery-fileupload-ui', $media_url.'js/jquery.fileupload-ui.js', array('jquery'), null, true);
            wp_register_script('pwebcontact-jquery-fileupload', $media_url.'js/jquery.fileupload'.($debug ? '' : '.min').'.js', array('jquery'), null, true);
            
            wp_register_script('pwebcontact-jquery-validate', $media_url.'js/jquery.validate'.($debug ? '' : '.min').'.js', array('jquery'), null, true);
            wp_register_script('pwebcontact-jquery-cookie', $media_url.'js/jquery.cookie'.($debug ? '' : '.min').'.js', array('jquery'), null, true);
            
            wp_register_script('pwebcontact', $media_url.'js/jquery.pwebcontact'.(file_exists($media_path.'js/jquery.pwebcontact.js') ? '' : '.min').'.js', array('jquery'), null, true);
            
            
            // Register styles
            wp_register_style('pwebcontact-bootstrap', $media_url.'css/bootstrap'.($debug ? '' : '.min').'.css');
            wp_register_style('pwebcontact-bootstrap-rtl', $media_url.'css/bootstrap-rtl'.($debug ? '' : '.min').'.css');
            wp_register_style('pwebcontact-bootstrap-2', $media_url.'css/bootstrap-2.3.2'.($debug ? '' : '.min').'.css');
            wp_register_style('pwebcontact-bootstrap-2-rtl', $media_url.'css/bootstrap-2.3.2-rtl'.($debug ? '' : '.min').'.css');
            wp_register_style('pwebcontact-bootstrap-custom', $media_url.'css/bootstrap-custom.css');
            wp_register_style('pwebcontact-bootstrap-custom-rtl', $media_url.'css/bootstrap-custom-rtl.css');

            wp_register_style('pwebcontact-icomoon', $media_url.'css/icomoon.css');

            wp_register_style('pwebcontact-layout', $media_url.'css/layout.css');
            wp_register_style('pwebcontact-layout-rtl', $media_url.'css/layout-rtl.css');
            wp_register_style('pwebcontact-animations', $media_url.'css/animations.css');
            
            wp_register_style('pwebcontact-uploader', $media_url.'css/uploader.css');
            wp_register_style('pwebcontact-uploader-rtl', $media_url.'css/uploader-rtl.css');

            wp_register_style('pwebcontact-custom', $media_url.'css/custom.css');
            
            // Register styles for Internet Explorer
            wp_register_style('pwebcontact-ie8', $media_url.'css/ie8.css');
            wp_style_add_data('pwebcontact-ie8', 'conditional', 'lt IE 9');
            
            
            // get forms published in footer and initialize them
            $forms_id = self::getFormsInFooter();
            foreach ($forms_id as $form_id) {
                self::initForm($form_id);
            }
            
            // check if there are any forms published and load IE CSS in header
            add_action('wp_head', array('PWebContact', 'displayHeader'));
            
            // TODO check if there is any form published not in footer with option to load CSS in header on all pages
        }
    }
    
    
    public static function getFormsInFooter()
    {
        global $wpdb;
        
        $sql =  $wpdb->prepare('SELECT `id` '.
                    'FROM `'.$wpdb->prefix.'pwebcontact_forms` '.
                    'WHERE `publish` = %d AND `position` = %s', 1, 'footer');

        return $wpdb->get_col($sql);
    }
    
    
    public static function displayFormsInFooter()
    {
        $forms_id = self::getFormsInFooter();
        foreach ($forms_id as $form_id) {
            self::displayForm($form_id);
        }
    }
    
    
    public static function displayFormByShortcode($atts, $content = null, $tag) {
        
        extract( shortcode_atts( array (
            'id' => 0
        ), $atts ) );
        
        $output = '';
        
        if ($id AND self::initForm($id, 'shortcode') === true) {
            ob_start();
            self::displayForm($id);
            $output = ob_get_clean();
        }
        
        return $output;
    }
    
    
    public static function displayForm($form_id = 0) 
	{
		if (!isset(self::$forms[$form_id]) OR self::$forms[$form_id] === false) {
            // form is not initialized or disabled
            return;
        }
        
        $params = self::getParams($form_id);
        
        // Get JavaScript init code
        $script = self::getScript($form_id);

        // Load fields
        $fields = self::getFields($form_id);
        
        $layout = $params->get('layout_type', 'slidebox');

        // Display HTML code
        require (dirname(__FILE__) .'/tmpl/'. $params->get('layout', 'default') .'.php'); //WP
                
        // Disable form to load it only once
        self::$forms[$form_id] = false;
    }

	public static function initForm($form_id = 0, $position = 'footer') 
	{
		if (isset(self::$forms[$form_id])) {
            // form already initialized
            return null;
        }
        
        $params = self::getParams($form_id); //WP
        
        if ($params->get('position', 'footer') !== $position) {
            // form is assigned to another position
            return false;
        }
        
        if (!$params->get('publish')) { //WP
            // form is disabled
            self::$forms[$form_id] = false;
            return false;
        }
        
        // Load form
        self::$forms[$form_id] = true;

        // Show or hide module on Mobile browser
        /*if ($filter_browsers = $params->get('filter_browsers'))
        {
            $browser = JBrowser::getInstance();
            $isMobile = $browser->isMobile();
            // Show only on mobile OR only on desktop
            if (($filter_browsers == 1 AND !$isMobile) OR ($filter_browsers == 2 AND $isMobile)) {
                if ($params->get('debug')) {
                    $app->enqueueMessage(__('MOD_PWEBCONTACT_FILTER_BROWSERS_WARNING', 'pwebcontact'), 'warning');
                }
                return;
            }
        }*/

        // Get layout name
        $layout = $params->get('layout_type', 'slidebox');
        // Position and offset
        $position = explode(':', $params->get('toggler_position', 'left:top'));
        $params->set('toggler_position', $position[0]);
        $params->def('toggler_offset_position', array_key_exists(1, $position) ? $position[1] : 'top');

        // Set static position for static and accordion layouts
        if (in_array($layout, array('static', 'accordion'))) {
            $params->set('toggler_position', 'static');
        }


        // Auto RTL
        if ($params->get('rtl', 2) == 2) {
            if (!is_rtl())
                $params->set('rtl', 0);
            else {
                switch ($params->get('toggler_position')) {
                    case 'left':
                        $params->set('toggler_position', 'right');
                        break;
                    case 'right':
                        $params->set('toggler_position', 'left');
                        break;
                    case 'top':
                    case 'bottom':
                        switch ($params->get('toggler_offset_position')) {
                            case 'left':
                                $params->set('toggler_offset_position', 'right');
                                break;
                            case 'right':
                                $params->set('toggler_offset_position', 'left');
                        }
                }
                $params->set('toggler_rotate', 0 - $params->get('toggler_rotate', 1));
            }
        }


        // Disable vertical toggler if position is not left or right
        if (!in_array($params->get('toggler_position'), array('left', 'right'))) {
            $params->set('toggler_vertical', 0);
        }
        // Disable sliding of toggler if it is not vertical and position is left or right
        elseif (!$params->get('toggler_vertical', 0)) {
            $params->set('toggler_slide', 0);
        }

        // Toggler position
        if ($layout == 'slidebox') {
            if ($params->get('handler', 'tab') == 'hidden') {
                $params->set('toggler_vertical', 0);
                $params->set('toggler_slide', 0);
            }
        }
        elseif ($layout == 'accordion') {
            if ($params->get('handler', 'tab') == 'button') {
                $params->def('toggler_offset_position', 'fixed');
            }
        }

        // Disable auto-open for static layout
        if ($layout == 'static') {
            $params->set('open_toggler', 0);
        }

        // Toggler tab name
        $toggler_name = explode('|', $params->get('toggler_name', 'Ask a question')); //WP
        $params->def('toggler_name_open', str_replace('"', '', __($toggler_name[0], 'pwebcontact'))); //WP
        $params->def('toggler_name_close', array_key_exists(1, $toggler_name) ? str_replace('"', '', __($toggler_name[1], 'pwebcontact')) : null); //WP


        // Set media path
        $media_path = dirname(__FILE__) . '/media/'; //WP
        $params->set('media_path', $media_path);
        $media_url = plugins_url('media/', __FILE__); //WP
        $params->set('media_url', $media_url);

        // Load CSS and JS files and JS translations
        self::initHeader();

        // Module CSS classes
        self::initCssClassess();
        
        return true;
	}
	

	public static function setLog($log) 
	{
		return self::$logs[] = $log;
	}
	

	public static function setParams(&$params) 
	{
		self::$form_id = (int)$params->get('id');
		self::$params[self::$form_id] = $params;
	}


	public static function getParams($form_id = 0)
	{
		global $wpdb;
        
        $form_id = $form_id ? $form_id : self::$form_id;
		if (!isset(self::$params[$form_id]))
		{
            $sql =  $wpdb->prepare('SELECT `params`, `publish`, `position`, `layout` '.
                    'FROM `'.$wpdb->prefix.'pwebcontact_forms` '.
                    'WHERE `id` = %d', $form_id);
            
            $data = $wpdb->get_row($sql);
			
			$params = new PWebContact_Params($data->params);
			$params->def('id', (int)$form_id);
            $params->def('publish', $data->publish);
            $params->def('position', $data->position);
            $params->def('layout_type', $data->layout);
				
			self::$params[$form_id] = $params;
			
			if (!self::$form_id AND $form_id) self::$form_id = $form_id;
		}
		return self::$params[$form_id];
	}


	public static function getFields($form_id = 0) 
	{
		$form_id = $form_id ? $form_id : self::$form_id;
		if (!isset(self::$fields[$form_id])) 
		{
			$params = self::getParams($form_id);
			self::$fields[$form_id] = $params->get('fields', array());
            if (!is_array(self::$fields[$form_id])) {
                self::$fields[$form_id] = json_decode($params->get('fields', '[]'), true);
            }
		}
		return self::$fields[$form_id];
	}


	public static function initCssClassess($form_id = 0)
	{
		$params = self::getParams($form_id);
		$layout = $params->get('layout_type', 'slidebox');
		
		$positionClasses = $togglerClasses = $boxClasses = array();
		$moduleClasses = array(
			'pweb-'.$layout,
			'pweb-labels-'.$params->get('labels_position', 'inline')
		);
		
		if (($class = $params->get('style_bg', 'white')) != -1) $moduleClasses[] = 'pweb-bg-'.$class;
		if (($class = $params->get('style_form', 'blue')) != -1) $moduleClasses[] = 'pweb-form-'.$class;
		
		if ($layout != 'static') 
		{
			if (in_array($layout, array('slidebox', 'modal')))
			{
				$positionClasses[] = 'pweb-'.$params->get('toggler_position', 'left');
				$positionClasses[] = 'pweb-offset-'.$params->get('toggler_offset_position', 'top');
				if ($params->get('toggler_vertical')) {
					$moduleClasses[] = 'pweb-vertical';
					if ($params->get('toggler_rotate', 1) == -1) $togglerClasses[] = 'pweb-rotate';
				} else {
					$moduleClasses[] = 'pweb-horizontal';
				}
				
				if ($layout == 'slidebox')
				{
					if ($params->get('handler', 'tab') == 'hidden' AND $params->get('toggler_offset_position') == 'fixed') $moduleClasses[] = 'pweb-toggler-hidden';
					if ($params->get('toggler_slide')) $moduleClasses[] = 'pweb-toggler-slide';
					if (!$params->get('debug')) $boxClasses[] = 'pweb-init';
				}
			}
			elseif ($layout == 'accordion') 
			{
				if ($params->get('accordion_boxed', 1)) $boxClasses[] = 'pweb-accordion-boxed';
				if (!$params->get('debug')) $boxClasses[] = 'pweb-init';
			}
			
			if (($class = $params->get('style_toggler', 'blue')) != -1) $togglerClasses[] = 'pweb-toggler-'.$class;
			if ($icon = $params->get('toggler_icon')) $togglerClasses[] = 'pweb-icon pweb-icon-'.$icon;
		}
		
		if ($icon = $params->get('icons', 'icomoon')) $moduleClasses[] = 'pweb-'.$icon;
		if ($params->get('rounded')) $moduleClasses[] = $togglerClasses[] = 'pweb-radius';
		if ($params->get('shadow')) $moduleClasses[] = $togglerClasses[] = 'pweb-shadow';
		if ($params->get('rtl', 0)) $moduleClasses[] = $togglerClasses[] = 'pweb-rtl';
		if ($params->get('user_data', 1) == 2) {
			$user = wp_get_current_user(); //WP
			if ($user->ID) $moduleClasses[] = 'pweb-hide-user';
		}
		if ($moduleclass_sfx = $params->get('moduleclass_sfx')) {
			$moduleclasses_sfx = explode(' ', $moduleclass_sfx);
			for ($i = 0; $i < count($moduleclasses_sfx); $i++) 
				if (strpos($moduleclasses_sfx[$i], 'icon-') !== false) 
					unset($moduleclasses_sfx[$i]);
			$moduleClasses[] = $togglerClasses[] = htmlspecialchars(implode(' ', $moduleclasses_sfx));
		}
		
		$params->def('positionClass', implode(' ', $positionClasses));
		$params->def('togglerClass', implode(' ', $togglerClasses));
		$params->def('moduleClass', implode(' ', $moduleClasses));
		$params->def('boxClass', implode(' ', $boxClasses));
	}


	public static function getCssDeclaration($form_id = 0)
	{
		$params 		= self::getParams($form_id);
		$form_id 		= (int)$params->get('id');
		$media_url 		= $params->get('media_url');
		$layout 		= $params->get('layout_type', 'slidebox');
		$css 			= null;
		$declarations 	= array();


		// Position offset
		if ($value = $params->get('offset'))
			$css .= '#pwebcontact'.$form_id.'{'.$params->get('toggler_offset_position', '').':'.$value.'}';
		
		
		// Layer level
		if ($value = (int)$params->get('zindex')) {
			// Slide box and Lightbox toggler
			$css .=  '#pwebcontact'.$form_id.'.pweb-left,'
					.'#pwebcontact'.$form_id.'.pweb-right,'
					.'#pwebcontact'.$form_id.'.pweb-top,'
					.'#pwebcontact'.$form_id.'.pweb-bottom'
					.'{z-index:'.$value.'}';
			// Lightbox window
			if ($layout == 'modal' AND $value > 1030) {
				$css .= '.pweb-modal-open .modal-backdrop{z-index:'.($value+10).'}';
				$css .= '.pwebcontact-modal.modal{z-index:'.($value+20).'}';
				$css .= '.ui-effects-transfer.pweb-genie{z-index:'.($value+19).'}';
			}
			// TODO Calendar
			if ($value+20 >= 10000) {
				self::$loaded['calendar_zindex'] = $value+30;
				$css .= 'body div.calendar{z-index:'.($value+30).'}';
			}
			
		}
		
		
		if ($layout == 'slidebox' OR (in_array($layout, array('accordion', 'modal')) AND in_array($params->get('handler', 'tab'), array('button', 'tab'))) )
		{
			// Toggler
			if ($value = $params->get('toggler_color'))
				$declarations[] = 'color:'.$value;
			if ($value = $params->get('toggler_bg')) {
				$declarations[] = 'background-image:none';
				$declarations[] = 'background-color:'.$value;
				$declarations[] = 'border-color:'.$value;
			}
			if ($value = $params->get('toggler_font_size'))
				$declarations[] = 'font-size:'.$value;
			if ($value = $params->get('toggler_font_family'))
				$declarations[] = 'font-family:'.$value;
			if ($value = $params->get('toggler_width'))
				$declarations[] = 'width:'.(int)$value.'px';
			if ($value = $params->get('toggler_height'))
				$declarations[] = 'height:'.(int)$value.'px';
			if (count($declarations)) {
				$css .= '#pwebcontact'.$form_id.'_toggler{'.implode(';', $declarations).'}';
				$declarations = array();
			}
			
			// Toggler icon
			if ($params->get('toggler_icon') == 'gallery') {
				if ($value = $params->get('toggler_icon_gallery'))
					$css .= '#pwebcontact'.$form_id.'_toggler .pweb-icon{background-image:url('.$media_url.'images/icons/'.urlencode($value).')}';
			}
			elseif ($params->get('toggler_icon') == 'custom') {
				if ($value = $params->get('toggler_icon_custom'))
                        //TODO parse and encode URL with JS
					$css .= '#pwebcontact'.$form_id.'_toggler .pweb-icon{background-image:url('.$value.')}'; //WP
			}
			elseif ($params->get('toggler_icon') == 'icomoon') {
				if ($value = $params->get('toggler_icomoon'))
					$css .= '#pwebcontact'.$form_id.'_toggler .pweb-icon:before{content:"\\'.$value.'"}';
			}
				
			// Toggler vertical text
			if ($params->get('toggler_vertical'))
			{
				$lang_code = get_bloginfo('language'); //WP
				$toggler_dir  = $params->get('media_path').'toggler/'; //WP
				$toggler_file = 'toggler-'.$form_id.'-'.$lang_code.'-'
					.md5(
						 (int)$params->get('toggler_width', 30)
						.(int)$params->get('toggler_height', 120)
						.(int)$params->get('toggler_font_size', 12)
						.(int)$params->get('toggler_rotate', 1)
						.$params->get('toggler_font', 'NotoSans-Regular')
						.$params->get('toggler_color')
						.$params->get('toggler_name')
						.$params->get('style_toggler', 'blue')
					)
					.'.png';
				
				if (!file_exists($toggler_dir.$toggler_file)) //WP
					self::createToggleImage($toggler_dir, $toggler_file); //WP
				
				//$css .= '#pwebcontact'.$form_id.'_toggler .pweb-text{background-image:url('.$params->get('media_url').'toggler/'.$toggler_file.')}';
				$css .= '#pwebcontact'.$form_id.'_toggler .pweb-text{background-image:url(data:image/png;base64,'
						.base64_encode(file_get_contents($toggler_dir.$toggler_file)) //WP
						.')}';
			}
		}
		
		
		// Form container
		if ($value = $params->get('form_font_size'))
			$declarations[] = 'font-size:'.$value;
		if ($value = $params->get('form_font_family'))
			$declarations[] = 'font-family:'.$value;
		if (count($declarations)) {
			$css .=  '#pwebcontact'.$form_id.'_box,'
					.'#pwebcontact'.$form_id.'_form label,'
					.'#pwebcontact'.$form_id.'_form input,'
					.'#pwebcontact'.$form_id.'_form textarea,'
					.'#pwebcontact'.$form_id.'_form select,'
					.'#pwebcontact'.$form_id.'_form button,'
					.'#pwebcontact'.$form_id.'_form .btn'
					.'{'.implode(';', $declarations).'}';
			$declarations = array();
		}
		
		if ($value = $params->get('text_color')) {
			$css .=  '#pwebcontact'.$form_id.'_form label,'
					.'#pwebcontact'.$form_id.'_form .pweb-separator-text,'
					.'#pwebcontact'.$form_id.'_form .pweb-msg,'
					.'#pwebcontact'.$form_id.'_form .pweb-chars-counter,'
					.'#pwebcontact'.$form_id.'_form .pweb-uploader,'
					.'#pwebcontact'.$form_id.'_box .pweb-dropzone'
					.'{color:'.$value.'}';
		}
		
		if ($value = $params->get('bg_color')) {
			if (($opacity = (float)$params->get('bg_opacity')) < 1) {
				$bg_color = self::parseToRgbColor($value);
				$value .= ';background-color:rgba('.$bg_color['r'].','.$bg_color['g'].','.$bg_color['b'].','.$opacity.')';
			}
			$container_bg = 'background-color:'.$value;
			$css .= '#pwebcontact'.$form_id.'_container{'.$container_bg.'}';
		}
		
		
		// Form width
		if ($value = $params->get('form_width')) {
			if ($layout != 'slidebox' OR strpos($value, 'px') !== false)
				$css .= '#pwebcontact'.$form_id.'_box{max-width:'.$value.'}';
		}
		// Labels width
		if ($params->get('labels_position', 'inline') == 'inline' AND ($value = (int)$params->get('labels_width'))) 
		{
			if ($value > 90) $value = 30;
			$css .= '#pwebcontact'.$form_id.'_box .pweb-label{width:'.$value.'%}';
			$css .= '#pwebcontact'.$form_id.'_box .pweb-field{width:'.(99.9-floatval($value)).'%}';	
		}
		
		// Message success and error
		if ($value = $params->get('msg_success_color'))
			$css .= '#pwebcontact'.$form_id.'_form .pweb-msg .pweb-success{color:'.$value.'}';
		if ($value = $params->get('msg_error_color'))
			$css .= '#pwebcontact'.$form_id.'_form .pweb-msg .pweb-error{color:'.$value.'}';
		
		
		// Buttons, fields, links
		if ($value = $params->get('buttons_fields_color')) {
			$declarations[] = 'background-image:none';
			$declarations[] = 'background-color:'.$value;
			$declarations[] = 'border-color:'.$value;
			
			$css .=  '#pwebcontact'.$form_id.'_container a,'
					.'#pwebcontact'.$form_id.'_container a:hover,'
					.'#pwebcontact'.$form_id.'_container .pweb-button-close'
					.'{color:'.$value.' !important}';
			
			$css .=  '#pwebcontact'.$form_id.'_form input.pweb-input,'
			 		.'#pwebcontact'.$form_id.'_form select,'
			 		.'#pwebcontact'.$form_id.'_form textarea{border-color:'.$value.'}';
			$css .=  '#pwebcontact'.$form_id.'_form input.pweb-input:focus,'
			 		.'#pwebcontact'.$form_id.'_form select:focus,'
			 		.'#pwebcontact'.$form_id.'_form textarea:focus'
			 		.'{border-color:'.$value.' !important}';
		}
		if ($value = $params->get('buttons_text_color'))
			$declarations[] = 'color:'.$value.' !important';
		if (count($declarations)) {
			$css .=  '#pwebcontact'.$form_id.'_form button,'
					.'#pwebcontact'.$form_id.'_form .btn'
					.'{'.implode(';', $declarations).'}';
			$declarations = array();
		}
		
		if ($layout == 'modal') 
		{
			// Modal backdrop
			if (($value = (float)$params->get('modal_opacity')) > 0) 
				$declarations[] = 'opacity:'.$value;
			if ($value = $params->get('modal_bg')) 
				$declarations[] = 'background-color:'.$value;
			if (count($declarations)) {
				$css .= '.pwebcontact'.$form_id.'_modal-open .modal-backdrop.fade.in{'.implode(';', $declarations).'}';
				$declarations = array();
			}
			
			// Modal transfer effect
			if (($value = (float)$params->get('modal_duration', 400)) !== 400) {
				$declarations[0] = 'animation-duration:'.$value.'ms';
				$declarations[] = '-o-'.$declarations[0];
				$declarations[] = '-ms-'.$declarations[0];
				$declarations[] = '-moz-'.$declarations[0];
				$declarations[] = '-webkit-'.$declarations[0];
			}
			if (isset($container_bg))
				$declarations[] = $container_bg;
			if (count($declarations)) {
				if (($class = $params->get('style_bg', 'white')) != -1) 
					$css .= '.pweb-bg-'.$class;
				$css .= '.ui-effects-transfer.pweb-genie.pwebcontact'.$form_id.'-genie{'.implode(';', $declarations).'}';
				$declarations = array();
			}
		}
		
		// Background image
		$declarations_mobile = array();
		if ($value = $params->get('bg_image')) {
			$declarations[] = 'background-image:url('.$value.')'; //WP
		}
		if ($value = $params->get('bg_position')) {
			if ($params->get('rtl') == 2) {
				if (strpos($value, 'left') !== false)
					$value = str_replace('left', 'right', $value);
				elseif (strpos($value, 'right') !== false)
					$value = str_replace('right', 'left', $value);
			}
			$declarations[] = 'background-position:'.$value;
		}
		if (($padding_position = $params->get('bg_padding_position')) AND ($padding = $params->get('bg_padding'))) {
			if ($params->get('rtl') == 2) {
				if ($padding_position == 'left')
					$padding_position = 'right';
				elseif ($padding_position == 'right')
					$padding_position = 'left';
			}
			$declarations[] = 'padding-'.$padding_position.':'.$padding;
			
			if (($padding_position == 'left' OR $padding_position == 'right')) {
				$padding_mobile = 10;
				if ($layout == 'slidebox' 
					AND ($params->get('toggler_position') == 'left' OR $params->get('toggler_position') == 'right') 
					AND $params->get('toggler_vertical') AND !$params->get('toggler_slide')) {
						$padding_mobile = 50;
				}
				if ($params->get('bg_image')) {
					$declarations_mobile[] = 'background-image:none';
				}
				$declarations_mobile[] = 'padding-'.$padding_position.':'.$padding_mobile.'px';
			}
		}
		if (count($declarations)) {
			$css .= '#pwebcontact'.$form_id.'_container{'.implode(';', $declarations).'}';
			if (count($declarations_mobile)) {
				$css .= '@media(max-width:480px){#pwebcontact'.$form_id.'_container{'.implode(';', $declarations_mobile).'}}';
			}
			$declarations = array();
		}


		// Accordion boxed with arrow
		if ($layout == 'accordion' AND $params->get('accordion_boxed', 1) AND $params->get('bg_color')) {
			$border_color = isset($bg_color) ? $bg_color : self::parseToRgbColor($params->get('bg_color'));
			foreach ($border_color as &$color) {
				$color -= 25; // 10% from 255
				if ($color < 0) $color = 0;
			}
			
			$declarations[0] = 'box-shadow:'.($params->get('shadow', 1) ? '0 0 4px rgba(0,0,0,0.5),' : '')
				.'inset 0 0 8px rgb('.$border_color['r'].','.$border_color['g'].','.$border_color['b'].')';
			$declarations[] = '-moz-'.$declarations[0];
			$declarations[] = '-webkit-'.$declarations[0];
			$declarations[] = 'border-color:rgb('.$border_color['r'].','.$border_color['g'].','.$border_color['b'].')';
			$css .= '#pwebcontact'.$form_id.'_container{'.implode(';', $declarations).'}';
			$css .= '#pwebcontact'.$form_id.'_box .pweb-arrow{border-bottom-color:rgb('.$border_color['r'].','.$border_color['g'].','.$border_color['b'].')}';
			$declarations = array();
		}


		// Disable Boostrap glyphicons
		if (!$params->get('boostrap_glyphicons', 1))
			$css .= '[class^="icon-"],[class*=" icon-"]{background-image:none !important}';


		return $css;
	}


	public static function initHeader($form_id = 0) 
	{
		$params 	= self::getParams($form_id);
		$media_url 	= $params->get('media_url');
		$layout 	= $params->get('layout_type', 'slidebox');
		$debug 		= $params->get('debug');

		// jQuery
		if ($params->get('load_jquery', 1)) {
            wp_enqueue_script('jquery');
		}
        // Bootstrap JS
		if ($params->get('load_bootstrap', 1)) {
            if ($params->get('bootstrap_version', 2) === 2) {
                wp_enqueue_script('pwebcontact-bootstrap-2');
            }
            elseif ($params->get('bootstrap_version', 2) === 3) {
                wp_enqueue_script('pwebcontact-bootstrap');
            }
		}
		
		// Bootstrap CSS
		if ($params->get('load_bootstrap_css', 2) == 1) {
            
			if ($params->get('bootstrap_version', 2) === 2) {
                wp_enqueue_style('pwebcontact-bootstrap-2');
                if ($params->get('rtl', 0)) 
                    wp_enqueue_style('pwebcontact-bootstrap-2-rtl');
            }
            elseif ($params->get('bootstrap_version', 2) === 3) {
                wp_enqueue_style('pwebcontact-bootstrap');
                if ($params->get('rtl', 0)) 
                    wp_enqueue_style('pwebcontact-bootstrap-rtl');
            }
		}
		elseif ($params->get('load_bootstrap_css', 2) == 2) {
            wp_enqueue_style('pwebcontact-bootstrap-custom');
            if ($params->get('rtl', 0)) 
                wp_enqueue_style('pwebcontact-bootstrap-custom-rtl');
		}


        // TODO load IcoMoon for calendar field
		// Toggler IcoMoon
		if (in_array($params->get('handler', 'tab'), array('button', 'tab')) AND $params->get('toggler_icon') == 'icomoon' AND $params->get('toggler_icomoon') AND $params->get('load_icomoon', 1))
			wp_enqueue_style('pwebcontact-icomoon');


		// CSS layout
		wp_enqueue_style('pwebcontact-layout');
		wp_enqueue_style('pwebcontact-animations');

		// CSS RTL layout
		if ($params->get('rtl', 0))
			wp_enqueue_style('pwebcontact-layout-rtl');


		
        
        if ($params->get('show_upload', 0)) 
		{
			// Load IcoMoon for uploader
            if ($params->get('icons', 'icomoon') == 'icomoon' AND $params->get('load_icomoon', 1))
				wp_enqueue_style('pwebcontact-icomoon');
			
			wp_enqueue_style('pwebcontact-uploader');
			if ($params->get('rtl', 0)) 
				wp_enqueue_style('pwebcontact-uploader-rtl');
			
			if ($params->get('load_jquery_fileupload', 1)) {
				
				if ($params->get('load_jquery_ui', 1)) {
					wp_enqueue_script('jquery-ui-widget', array('jquery-ui-core'));
				}
				
				if ($debug) {
					wp_enqueue_script('pwebcontact-jquery-iframe-transport');
					wp_enqueue_script('pwebcontact-jquery-fileupload');
					wp_enqueue_script('pwebcontact-jquery-fileupload-process');
					wp_enqueue_script('pwebcontact-jquery-fileupload-validate');
					wp_enqueue_script('pwebcontact-jquery-fileupload-ui');
				} else {
					wp_enqueue_script('pwebcontact-jquery-fileupload');
				}
			}
			
			if (!self::$loaded['uploader_text']) 
			{
				self::$loaded['uploader_text'] = true;
				
                wp_localize_script('pwebcontact-jquery-fileupload', 'pwebcontact_l10n = pwebcontact_l10n || {}; pwebcontact_l10n.upload', array(
                    'UPLOADING' => __('Uploading...', 'pwebcontact'),
                    'ERR'       => __('Upload error', 'pwebcontact'),
                    'BYTES_ERR' => __('Uploaded bytes exceed file size', 'pwebcontact'),
                    'LIMIT_ERR' => __('Maximum number of files exceeded', 'pwebcontact'),
                    'TYPE_ERR'  => __('File type not allowed', 'pwebcontact'),
                    'SIZE_ERR'  => __('File is too large', 'pwebcontact')
                ));
			}
		}

        if ($layout == 'slidebox') 
		{
			if (($effect = $params->get('effect_transition')) > 0 AND strpos($effect, 'ease') !== false AND $params->get('load_jquery_ui', 1)) {
				wp_enqueue_script('jquery-ui-core');
			}
		}
		elseif ($layout == 'accordion' OR ($layout == 'modal' AND $params->get('modal_effect','square') != 'default'))
		{
			if ($params->get('load_jquery_ui_effects', 1)) {
				wp_enqueue_script('jquery-ui-effects', array('jquery-ui-core'));
			}
			if ($layout == 'modal') {
				wp_enqueue_style('pwebcontact-animations');
			}
		}
        
        // Load jQuery Cookie for auto-open count
        if ($params->get('open_toggler') AND $params->get('open_count') AND $params->get('load_jquery_cookie', 1)) {
			wp_enqueue_script('pwebcontact-jquery-cookie');
        }

		if ($params->get('load_jquery_validate', 1)) 
			wp_enqueue_script('pwebcontact-jquery-validate');
		
		wp_enqueue_script('pwebcontact');


		// CSS styles
		if (($file = $params->get('style_bg', 'white')) != -1) {
            wp_register_style('pwebcontact-background-'.$file, $media_url.'css/background/'.$file.'.css');
			wp_enqueue_style('pwebcontact-background-'.$file);
        }
		if (($file = $params->get('style_form', 'blue')) != -1) {
			wp_register_style('pwebcontact-form-'.$file, $media_url.'css/form/'.$file.'.css');
            wp_enqueue_style('pwebcontact-form-'.$file);
        }
		if (($layout == 'slidebox' OR in_array($params->get('handler', 'tab'), array('button', 'tab'))) AND ($file = $params->get('style_toggler', 'blue')) != -1) {
			wp_register_style('pwebcontact-toggler-'.$file, $media_url.'css/toggler/'.$file.'.css');
            wp_enqueue_style('pwebcontact-toggler-'.$file);
        }
		
        // Custom styles
        wp_enqueue_style('pwebcontact-custom', $media_url.'css/custom.css');
        
        
		// Set custom styles
		if ($css = self::getCssDeclaration($form_id)) 
		{
			$path = $params->get('media_path').'css/cache/';
			$file = md5($css).'.css';
			if ($params->get('cache_css', 1) AND !file_exists($path.$file)) 
			{
				// set write permissions to cache folder
				if (!is_writable($path)) {
					//JPath::setPermissions($path, null, '0777'); //TODO WP
				}
				
				// write cache file
				if (!file_put_contents($path.$file, $css))
					$params->set('cache_css', 0);
			}
			
			if ($params->get('cache_css', 1)) {
				wp_register_style('pwebcontact-'.$file, $media_url.'css/cache/'.$file);
                wp_enqueue_style('pwebcontact-'.$file);
            }
			else {
                wp_add_inline_style('pwebcontact-custom', $css);
            }
		}
        
        // CSS IE
		wp_enqueue_style('pwebcontact-ie8');


		if (!self::$loaded['text']) 
		{
			self::$loaded['text'] = true;
			
            wp_localize_script('pwebcontact', 'pwebcontact_l10n = pwebcontact_l10n || {}; pwebcontact_l10n.form', array(
                'INIT'          => __('Initializing form...', 'pwebcontact'), //TODO is rather unused
                'SENDING'       => __('Sending...', 'pwebcontact'),
                'SEND_ERR'      => __('Wait a few seconds before sending next message', 'pwebcontact'),
                'REQUEST_ERR'   => __('Request error: ', 'pwebcontact'),
                'COOKIES_ERR'   => __('Enable cookies and refresh page to use this form', 'pwebcontact')
            ));
		}
	}
    
    
    public static function displayHeader()
    {
        global $wpdb;
        
        if (!self::$loaded['ie_css']) 
		{
			self::$loaded['ie_css'] = false;
            
            $sql =  $wpdb->prepare('SELECT COUNT(`id`) '.
                    'FROM `'.$wpdb->prefix.'pwebcontact_forms` '.
                    'WHERE `publish` = %d', 1);

            if ($wpdb->get_var($sql) > 0)
            {
                self::$loaded['ie_css'] = true;
                
                $media_url = plugins_url('media/', __FILE__); //WP

                echo
                     '<!--[if IE 8]>'."\r\n"
                    .'<style type="text/css">'
                        .'.pwebcontact-form .pweb-input,'
                        .'.pwebcontact-form select,'
                        .'.pwebcontact-form textarea,'
                        .'.pwebcontact-form .btn'
                        .'{behavior:url('.$media_url.'css/PIE.htc)}'
                    .'</style>'."\r\n"
                    .'<![endif]-->'
                    ."\r\n";

                echo
                     '<!--[if lt IE 9]>'."\r\n"
                    //.'<link rel="stylesheet" href="'.$media_url.'css/ie8.css" />'."\r\n"
                    .'<style type="text/css">'
                        .'.pwebcontact_toggler,'
                        .'.pwebcontact-container'
                        .'{behavior:url('.$media_url.'css/PIE.htc)}'
                    .'</style>'."\r\n"
                    .'<![endif]-->'
                    ."\r\n";
            }
		}
    }


	public static function getScript($form_id = 0) 
	{
		$params = self::getParams($form_id);
		
		$form_id 	= (int)$params->get('id');
		$media_url 	= $params->get('media_url');
		$layout 	= $params->get('layout_type', 'slidebox');
		$position 	= $params->get('toggler_position', 'left');
		
		$options = array();	
		$options[] = 'id:'.$form_id;
		$options[] = 'layout:"'.$layout.'"';
		$options[] = 'position:"'.$position.'"';
		$options[] = 'offsetPosition:"'.$params->get('toggler_offset_position').'"';
		$options[] = 'basePath:"'.home_url('', 'relative').'"'; //TODO test
		$options[] = 'ajaxUrl:"'.plugins_url('ajax.php?method=', __FILE__).'"'; //TODO get relative path WP
		
		if (($value = $params->get('msg_position', 'after')) != 'after')
			$options[] = 'msgPosition:"'.$value.'"';
		if (($value = (int)$params->get('msg_close_delay', 10)) != 10)
			$options[] = 'msgCloseDelay:'.$value;
		
		if ($params->get('debug', 0))
			$options[] = 'debug:1';
		
		if ($params->get('cache', 0) == 2)
			$options[] = 'reloadToken:1';
		
        if (($value = (intval($params->get('tooltips_focus', 1)) | (intval($params->get('tooltips_validation', 1)) << 1)) ) !== 3)
			$options[] = 'tooltips:'.$value;
		
		if ($value = $params->get('toggler_name_close') AND !$params->get('toggler_vertical', 0))
			$options[] = 'togglerNameClose:"'.$value.'"';
		
		
		if (($open = (int)$params->get('open_toggler')) > 0)
		{
			$max_count = (int)$params->get('open_count');
			if ($max_count == 0) {
				$options[] = 'openAuto:'.$open;
			} elseif ($max_count > 0) {
                // cookie
                 $options[] = 'openAuto:'.$open;
                $options[] = 'maxAutoOpen:'.$max_count;
                if (($value = (int)$params->get('cookie_lifetime', 30)) != 30)
                    $options[] = 'cookieLifetime:'.($value*3600*24);
                if (($value = home_url('', 'relative')) != '/') //WP
                    $options[] = 'cookiePath:"'.$value.'"';
                //$options[] = 'cookieDomain:"'.str_replace('www.', '', $_SERVER['HTTP_HOST']).'"';
			}
			if (($value = (int)$params->get('open_delay')) > 0) {
				$options[] = 'openDelay:'.$value;
			}
		}
		
		if ($params->get('close_toggler', 0))
			$options[] = 'closeAuto:1';
		if (($value = (int)$params->get('close_delay')) > 0)
			$options[] = 'closeDelay:'.$value;
		
		if (!$params->get('close_other', 1))
			$options[] = 'closeOther:0';
		
		// reset form after email has been sent
		if (($value = (int)$params->get('reset_form', 1)) != 1)
			$options[] = 'reset:'.$value;
		
		// redirect after email has been sent
		if ($value = $params->get('redirect_url')) {
			$options[] = 'redirectURL:"'.$value.'"';
			if (($value = (int)$params->get('redirect_delay', 5)) != 5) {
				$options[] = 'redirectDelay:'.$value;
			}
		}
		
		
		// On complete event
		$options2 = array();
		// Google AdWords Conversion Tracking
		if ($value = $params->get('adwords_url')) {
			$options2[] = '$("<img/>",{"src":"'.$value.'","width":1,"height":1,"border":0}).appendTo(this.Msg);';
		}
		// Microsoft adCenter Conversion Tracking
		if ($value = $params->get('adcenter_url')) {
			$options2[] = '$("<iframe/>",{"src":"'.$value.'","width":1,"height":1,"frameborder":0,"scrolling":"no"}).css({"visibility":"hidden","display":"none"}).appendTo(this.Msg);';
		}
		// After email sent success
		if ($value = $params->get('oncomplete')) {
			$options2[] = 'try{'.strip_tags($value)."\r\n".'}catch(e){this.debug(e)}';
		}
		if (count($options2)) $options[] = 'onComplete:function(data){'.implode('', $options2).'}';
		
		// On error event
		if ($value = $params->get('onerror')) {
			$options[] = 'onError:function(data){try{'.strip_tags($value)."\r\n".'}catch(e){this.debug(e)}}';
		}
		
		// On load, open and close events
		if ($value = $params->get('onload')) {
			$options[] = 'onLoad:function(){try{'.strip_tags($value)."\r\n".'}catch(e){this.debug(e)}}';
		}
		if ($value = $params->get('onopen')) {
			$options[] = 'onOpen:function(){try{'.strip_tags($value)."\r\n".'}catch(e){this.debug(e)}}';
		}
		if ($value = $params->get('onclose')) {
			$options[] = 'onClose:function(){try{'.strip_tags($value)."\r\n".'}catch(e){this.debug(e)}}';
		}
		
		
		// Uploader
		if ($params->get('show_upload', 0)) 
		{
			if ($value = $params->get('upload_allowed_ext'))
				$options[] = 'uploadAcceptFileTypes:/(\.|\/)('.$value.')$/i';
				
			// max file size in bytes
			if (($value = (float)$params->get('upload_size_limit', 1)) != 1) {
				$value *= 1024*1024;
				$options[] = 'uploadMaxSize:'.$value;
			}
			// max files limit
			if (($value = (int)$params->get('upload_files_limit', 5)) != 5)
				$options[] = 'uploadFilesLimit:'.$value;
			
			// start upload after file has been chosen
			if (!$params->get('upload_autostart', 1))
				$options[] = 'uploadAutoStart:0';
		}
		
		// Slide Box
		if ($layout == 'slidebox') 
		{
			// Form width
			if (($value = $params->get('form_width')) AND strpos($value, 'px') !== false)
				$options[] = 'slideWidth:'.(int)$value;
			if (($value = (int)$params->get('effect_duration')) > 0) //NEW
				$options[] = 'effectDuration:'.$value;
			if (($value = $params->get('effect_transition')) != -1 AND $value != -2 AND $value) { //NEW
				$options[] = 'effectTransition:"'.$value.'"';
			}
		}
		// Lightbox window
		else if ($layout == 'modal') 
		{
			if ($params->get('modal_opacity', -1) == 0) 
				$options[] = 'modalBackdrop:0';
			if ($params->get('modal_disable_close'))
				$options[] = 'modalClose:0';
			if (($value = $params->get('style_bg', 'white')) != -1)
				$options[] = 'modalStyle:"'.$value.'"';
			if (($value = $params->get('effect_duration', 400)) != 400)
				$options[] = 'effectDuration:'.(int)$value;
			if (($value = $params->get('effect', 'modal_fade')) != 'modal_fade')
				$options[] = 'effect:"'.$value.'"';
		}
		
		// Custom validation rules and calendar fields
		$fields = self::getFields();
		$rules = $calendars = array();
		foreach ($fields as $field)
		{
			
            if (isset($field['validation']) AND $field['validation']) 
			{
				$options2 = array('name:"'.$field['alias'].'"', 'regexp:'.str_replace('\\\\', '\\', $field['validation'])); //WP
				$rules[] = '{'.implode(',',$options2).'}';
			}
			elseif ($field['type'] == 'date')
			{
				$calendars[] = '{id:"'.$field['alias'].'"'.($field['format'] ? ',format:"'.$field['format'].'"' : '').'}'; //WP
			}
		}
		if (count($rules)) {
			$options[] = 'validatorRules:['.implode(',',$rules).']';
		}
		if (count($calendars)) 
		{
			//TODO load some calendar
			$options[] = 'calendars:['.implode(',',$calendars).']';
			/*if (($value = JFactory::getLanguage()->getFirstDay()) != 0)
				$options[] = 'calendarFirstDay:'.$value;*/
		}
		
		
		// JavaScript initialization
		$script = 
		'jQuery(document).ready(function($){'.
			'pwebContact'.$form_id.'=new pwebContact({'.implode(',', $options).'})'. 
		'});';
		
		
		if ($params->get('debug') AND !self::$loaded['debug_js'])
		{
			self::$loaded['debug_js'] = true;
			
			$script = 
			'jQuery(document).ready(function($){'.
				'if(typeof pwebContact'.$form_id.'Count=="undefined"){'.
					// Check if document header has been loaded
					'if(typeof pwebContact=="undefined")alert("PWeb debug: Contact form module has been loaded incorrect.'.
					// Check if one module instance has been loaded only once
					'pwebContact'.$form_id.'Count=$(".pwebcontact'.$form_id.'_form").length;'.
					'if(pwebContact'.$form_id.'Count>1)'.
						'alert("PWeb debug: Contact form module ID '.$form_id.' has been loaded "+pwebContact'.$form_id.'Count+" times. You can have multiple contact forms, but one instance of module can be loaded only once!")'.
				'}'.
			'});'.
			$script
			;
		}

		return $script;
	}


	protected static function createToggleImage($path = null, $file = null)
	{
		$params 		= self::getParams();
		
		$font_path 		= $params->get('media_path') . 'fonts/'.$params->get('toggler_font', 'NotoSans-Regular').'.ttf'; //WP
		$font_size 		= (int)$params->get('toggler_font_size', 12);
		
		if ($params->get('rtl')) {
			$text_open 	= self::utf8_strrev($params->get('toggler_name_open'));
			$text_close = self::utf8_strrev($params->get('toggler_name_close'));
		} else {
			$text_open 	= $params->get('toggler_name_open');
			$text_close = $params->get('toggler_name_close');
		}
		
		$length 		= strlen($text_open);
		if ($text_close AND strlen($text_close) > $length) $length = strlen($text_close);
		
		$width 			= $params->get('toggler_width', 30);
	    $height 		= is_numeric($params->get('toggler_height')) ? $params->get('toggler_height') : $length * $font_size / 1.2;
		
		$rotate 		= (int)$params->get('toggler_rotate', 1);
		
		// Parse font color
		if ($color = $params->get('toggler_color'))
		{
			$color = self::parseToRgbColor($color);
		}
		if (!is_array($color)) {
			if (in_array($params->get('style_toggler', 'blue'), array('white', 'gray')))
				$color = array('r' => 51, 'g' => 51, 'b' => 51); // gray
			else
				$color = array('r' => 255, 'g' => 255, 'b' => 255); // white
		}
		
		// create image
		$im = imagecreatetruecolor($text_close ? $width * 2 : $width, $height);
		imagesavealpha($im, true);
		imagealphablending($im, false);
		
		// set transparent background color
		$bg = imagecolorallocatealpha($im, 255, 0, 255, 127);
		imagefill($im, 0, 0, $bg);
		
		// set font color
		$font_color = imagecolorallocate($im, $color['r'], $color['g'], $color['b']);
		
		// display text
		if ($rotate > 0) {
			imagettftext($im, 
				$font_size, -90, 
				$width * 0.25, 
				0, 
				$font_color, $font_path, $text_open
			);
			
			if ($text_close) 
				imagettftext($im, 
					$font_size, -90, 
					$width + $width * 0.25, 
					0, 
					$font_color, $font_path, $text_close
				);
		}
		else {
			imagettftext($im, 
				$font_size, 90, 
				$width * 0.75, 
				$height, 
				$font_color, $font_path, $text_open
			);
			
			if ($text_close) 
				imagettftext($im, 
					$font_size, 90, 
					$width + $width * 0.75, 
					$height, 
					$font_color, $font_path, $text_close
				);
		}
		
		// set write permissions to cache folder
		if (!is_writable($path)) {
			//JPath::setPermissions($path, null, '0777'); //TODO WP
		}
			
		// save image
		//TODO consider output image and catch it with ob_get_contents() and then write with JFile
		imagepng($im, $path . $file);
		imagedestroy($im);
	}


	protected static function utf8_strrev($str)
	{
		if (empty($str)) return null;
		
		preg_match_all('/./us', $str, $ar);
		return join('', array_reverse($ar[0]));
	}


	protected static function parseToRgbColor($color = null)
	{
		$color = trim($color);
		// parse hex color
		if (preg_match('/^\#([0-9abcdef]{1,2})([0-9abcdef]{1,2})([0-9abcdef]{1,2})$/i', $color, $match)) 
		{
			if (strlen($match[1]) == 2)
			{
				$color = array(
					'r' => hexdec($match[1]),
					'g' => hexdec($match[2]),
					'b' => hexdec($match[3])
				);
			}
			else 
			{
				$color = array(
					'r' => hexdec($match[1].$match[1]),
					'g' => hexdec($match[2].$match[2]),
					'b' => hexdec($match[3].$match[3])
				);
			}
		}
		// parse rgb color
		elseif (preg_match('/\((\d+),(\d+),(\d+)/i', $color, $match))
		{
			$color = array(
				'r' => $match[1],
				'g' => $match[2],
				'b' => $match[3]
			);
		}
		
		return $color;
	}


	public static function getHiddenFields() 
	{
		$params = self::getParams();
		$html = '';
		
		// CMS and extension version
		if ($params->get('debug') AND ($info = self::getSystemInfo())) {
			$html .= "\r\n<!-- ".implode(', ', $info)." -->\r\n";
		}
		
		return $html;
	}
    
    
    public static function setHeader($name, $value, $replace = false) 
    {
		if (!isset(self::$headers[$name]) OR $replace)
		{
			self::$headers[(string)$name] = (string)$value;
		}
    }
    
    
    public static function getHeaders() 
    {
		return self::$headers;
    }
	
	
	public static function initAjaxResponse() 
	{
		global $wp_version;
        
        if (function_exists('exceptions_error_handler'))
			@set_error_handler('exceptions_error_handler');
		
		$form_id = (int)$_POST['mid'];
		$params = self::getParams($form_id);
		
		// Language
		if ($params->get('rtl', 2) == 2) {
			if (is_rtl()) //WP
				$params->set('rtl', 1);
			else
				$params->set('rtl', 0);
		}
		
		// Debug
		if ((int)$_POST['debug'] === 1) $params->set('debug', 1); //WP
		if (!defined('PWEBCONTACT_DEBUG')) define('PWEBCONTACT_DEBUG', $params->get('debug'));
		
		if (PWEBCONTACT_DEBUG) {
			self::$logs[] = 'WordPress version '.$wp_version;
			self::$logs[] = 'Contact Form version '.self::getVersion();
			self::$logs[] = 'Ajax response';
		}
		
		// Set media path
		$params->set('media_url', 	plugins_url('media/', __FILE__)); //WP
		$params->set('media_path', 	dirname(__FILE__) . '/media/'); //WP
        $upload_dir = wp_upload_dir();
		$params->set('upload_url',  $upload_dir['baseurl'].'/pwebcontact/'.$form_id.'/'); //WP
		$params->set('upload_path', $upload_dir['basedir'].'/pwebcontact/'.$form_id.'/'); //WP
		
		// Internet Explorer < 10
		if (!isset($_SERVER['HTTP_ACCEPT']) OR strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
			// Change response Content-Type
			self:setHeader('Content-Type', 'text/plain', true);
		}
	}


	public static function closeAjaxResponse() 
	{
		if (PWEBCONTACT_DEBUG) 
		{
			self::$logs[] = 'Ajax response exit';
		}

		return count(self::$logs) ? self::$logs : null;
	}


	public static function checkToken()
	{
		$response = true;
		
        $form_id = (int)$_POST['mid'];
		$token = wp_create_nonce('pwebcontact'.$form_id);
        
		try {
			if (!isset($_POST[$token]) OR (int)$_POST[$token] !== 1 OR wp_verify_nonce($_POST[$token], 'pwebcontact'.$form_id) === false)
			{
                $response = array('status' => 302, 'msg' => __('Invalid security token. Refresh page and try again', 'pwebcontact'));
			}
		} catch (Exception $e) {
			$response = array('status' => 302, 'msg' => __('WordPress error', 'pwebcontact'), 'debug' => array($e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()));
		}
		
		return $response;
	}


	public static function getTokenAjax() 
	{
        $form_id = (int)$_POST['mid'];
        
		return array('status' => 103, 'token' => wp_create_nonce('pwebcontact'.$form_id));
	}


	public static function checkCaptchaAjax()
	{
		self::initAjaxResponse();
		if (($response = self::checkToken()) !== true) return $response;
		
		if (PWEBCONTACT_DEBUG) self::$logs[] = 'Checking captcha';
		
		$params = self::getParams();
		
		$response = array('status' => 101, 'msg' => '');
		
		try {
			// TODO veriffy captcha code
			if (false) 
			{
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'Invalid captcha code';
				$response = array('status' => 201, 'msg' => __('Invalid captcha code', 'pwebcontact'));
			}
		} catch (Exception $e) {
			self::$logs[] = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			$response = array('status' => 301, 'msg' => __('WordPress error', 'pwebcontact'));
		}
		
		$response['debug'] = self::closeAjaxResponse();
		
		return $response;
	}
	
	
	public static function uploaderAjax() 
	{
		self::initAjaxResponse();
		if (($response = self::checkToken()) !== true) return $response;
		
		if (PWEBCONTACT_DEBUG) self::$logs[] = 'Uploader';
		
		try {			
			$response = PWebContact_Uploader::uploader();
			$response = array_merge(array('status' => 104), $response);
		} catch (Exception $e) {
			self::$logs[] = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			$response = array('status' => 400, 'msg' => __('WordPress error', 'pwebcontact'));
		}
		
		$response['debug'] = self::closeAjaxResponse();
		
		return $response;
	}


	public static function sendEmailAjax() 
	{
		self::initAjaxResponse();
		if (($response = self::checkToken()) !== true) return $response;
		
		if (PWEBCONTACT_DEBUG) self::$logs[] = 'Sending emails';
		
		$params = self::getParams();
		
		try {			
			$response = self::sendEmail();
		} catch (Exception $e) {
			self::$logs[] = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
			$response = array('status' => 300, 'msg' => __('WordPress error', 'pwebcontact'));
		}
		
		// delete atachments
		if ($params->get('show_upload', 0) AND $params->get('attachment_delete') AND $params->get('attachment_type', 1) == 1 AND ($response['status'] < 200 OR $response['status'] >= 300))
		{
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Deleting attachments';
			
			try {
				PWebContact_Uploader::deleteAttachments();
				$response['deleted'] = true;
			} catch (Exception $e) {
				self::$logs[] = $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
				$response = array('status' => 401, 'msg' => __('WordPress error', 'pwebcontact'));
			}
		}
		
		$response['debug'] = self::closeAjaxResponse();
		
		return $response;
	}
	

	public static function sendEmail() 
	{		
		$user 		= wp_get_current_user();
		$params 	= self::getParams();
		$form_id 	= (int)$params->get('id');
		
		// mail from
		$global_name  = $params->get('email_from_name', get_bloginfo('name'));
		$global_email = $params->get('email_from', get_bloginfo('email'));
		if (!$global_email) {
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Invalid Global Configuration';
			return array('status' => 303, 'msg' => __('MOD_PWEBCONTACT_GLOBAL_CONFIG_ERR', 'pwebcontact'));
		}
		
		$data 		=& self::$data;
		$email_vars =& self::$email_vars;
		
		// Get inputs
		$data = array(
			'fields'			=> (array)$_POST['fields'],
			'mailto'			=> (int)$_POST['mailto'],
			'title' 			=> (string)$_POST['title'],
			'url' 				=> (string)$_POST['url'],
			'screen_resolution' => (string)$_POST['screen_resolution'],
			'attachments' 		=> (array)$_POST['attachments']
		);

		$data['ip_address'] 	= self::detectIP();
		$data['browser'] 		= self::detectBrowser();
		$data['os'] 			= self::detectOS();
		
		$data['user_id'] 		= $user->ID; //WP
		$data['user_subject'] 	= '';
		
		// init email variables
		$email_vars = array(
			'name'				=> '',
			'email'				=> '',
			'username' 			=> $user->display_name, //WP
			'ip_address' 		=> $data['ip_address'],
			'browser' 			=> $data['browser'],
			'os' 				=> $data['os'],
			'screen_resolution' => $data['screen_resolution'],
			'title' 			=> $data['title'],
			'url' 				=> $data['url'],
			'site_name' 		=> get_bloginfo('name'), //WP
			'mailto_name'		=> '',
			'ticket'			=> ''
		);
		
		$fields = self::getFields();
		
		$user_email = null;
		$user_name 	= null;
		$user_cc 	= array();
		$email_to 	= array();
		
		$invalid_fields = array();
		
		// init email variables for fields and validate them
		foreach ($fields as $field)
		{
			// skip all separators which does not have any data
			if (strpos($field->type, 'separator') !== false) continue;
			
			// get field from request
			if (isset($data['fields'][$field->alias])) {
				$value = $data['fields'][$field->alias];
			} else {
				$data['fields'][$field->alias] = $value = null;
			}
			
			// is required
			if ($field->required AND ($value === null OR $value === '')) {
				// required field is empty
				$invalid_fields[] = 'field-'.$field->alias;
				continue;
			}
			
			if ($field->type == 'email') 
			{
				// Validate email
				if ($value AND is_email($value) === false) {
					$invalid_fields[] = 'field-'.$field->alias;
				} else {
					if (!$user_email) 
						$email_vars['email'] = $user_email = $value;
					else 
						$user_cc[] = $value;
				}
			}
			else 
			{
				if ($field->type == 'name') {
					if (!$user_name) 
						$email_vars['name'] = $user_name = $value;
				}
				elseif ($field->type == 'subject') {
					$data['user_subject'] .= ' '.$value;
				}
				
				// validate fields with regular expression
				if (in_array($field->type, array('text', 'name', 'phone', 'subject', 'password')) AND $field->validation AND $value AND !preg_match($field->validation, $value)) { //WP
					$invalid_fields[] = 'field-'.$field->alias;
				}
			}
		}
		
		
		// mailto list
		if ($params->get('email_to_list')) //TODO changed to field options
		{
			if ($data['mailto'] > 0) {
				$rows = @explode(PHP_EOL, $params->get('email_to_list'));
				if (array_key_exists($data['mailto']-1, $rows)) {
					$row = @explode('|', $rows[$data['mailto']-1]);
					if ($row[0]) {
						$email_to[] = $row[0];
						$email_vars['mailto_name'] = $row[1];
					}
				}
			}
			else {
				// required field is empty
				$invalid_fields[] = 'mailto';
			}
		} 
		else {
			$data['mailto'] = null;
		}
		
		
		if ($params->get('show_upload') == 2 AND !count($data['attachments'])) {
			$invalid_fields[] = 'uploader';
		}
		
		// invalid fields
		if (count($invalid_fields)) {
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Invalid fields';
			return array('status' => 200, 'msg' => __('Invalid fields values', 'pwebcontact'), 'invalid' => $invalid_fields);
		}
		
		
		// CMS
		if ($params->get('email_to')) {
			$email_to = array_merge($email_to, @explode(',', $params->get('email_to')));
		}
		if ($params->get('email_user')) 
		{
			$user_info = get_userdata( (int)$params->get('email_user') );
            if ($user_info !== false AND $user_info->email) {
                $email_to[] = $user_info->email;
            }
		}
		
		if (!count($email_to)) {
			$email_to[] = $global_email;
		}
		
		
		// ticket
		$data['ticket'] = '';
		if ($ticket_type = $params->get('ticket_enable', 0)) 
		{
			if ($ticket_type == 1)
			{
				$date = new DateTime();
                $data['ticket'] 		= $date->format('YmdHis'); //WP
				$email_vars['ticket'] 	= sprintf($params->get('ticket_format', '[#%s]'), $data['ticket']);
			}
			elseif ($ticket_type == 2)
			{
				$ticket_file = $params->get('media_path').'tickets/ticket_'.sprintf('%03d', $form_id).'.txt';
				$ticket_counter = is_file($ticket_file) ? (int)file_get_contents($ticket_file) : 0;
				$ticket_counter++;
				file_put_contents($ticket_file, $ticket_counter);
				
				$data['ticket'] 		= $ticket_counter;
				$email_vars['ticket'] 	= sprintf($params->get('ticket_format', '[#%06d]'), $ticket_counter);
			}
			
			if ($data['ticket']) 
			{
				// success message with ticket
				$success_msg = sprintf(__($params->get('msg_success', 'Message successfully sent. Your ticket is: %s'), 'pwebcontact'), $email_vars['ticket']);
				
				// email subject with ticket
				$data['subject'] = sprintf(__($params->get('email_subject', '%s Message sent from'), 'pwebcontact'), $email_vars['ticket']);
			}
		}
		
		// success message
		if (!isset($success_msg)) $success_msg = __($params->get('msg_success', 'Message successfully sent', 'pwebcontact'));
		// clean subject
		//$success_msg = str_replace(array('"','\\'), '', $success_msg);
		
		// email subject
		if (!isset($data['subject'])) $data['subject'] = __($params->get('email_subject', 'Message sent from', 'pwebcontact'));
		
		// email subject suffix
		//TODO test RTL if suffix should be before subject
		switch ($params->get('email_subject_sfx', 2))
		{
			case 1:
				$data['subject'] .= ' '.$email_vars['site_name'];
				break;
			case 2:
				$data['subject'] .= ' '.$data['title'];
				break;
			case 3:
				$data['subject'] .= $data['user_subject'];
		}

		// HOOK PROCCESS DATA - here you can add custom code to proccess variables: $data, $email_vars

		// HTML emails path
		$tmpl_path = $params->get('media_path').'email_tmpl/';
		
		// User email copy and auto-reply
		$email_copy 		= ($params->get('email_copy', 0) AND (int)$_POST['copy']);
		$email_autoreply 	= $params->get('email_autoreply', 0);
		
		if ($user_email AND ($email_copy OR $email_autoreply)) 
		{
			$mail = JFactory::getMailer(); //TODO
			
			// add recipient
			$mail->addRecipient($user_email);
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email: '.$user_email;
			
			// Add carbon copy recipients
			if (count($user_cc))
			{
				$mail->addCC($user_cc);
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email: '.implode(', ', $user_cc);
			}
			
			// set subject
			$mail->setSubject($data['subject']);
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email subject: '.$data['subject'];
			
			// set sender
			$mail->setSender(array($global_email, $global_name));
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email sender: '.$global_email;
			
			// set reply to
			if ($params->get('email_replyto')) 
			{
				$mail->ClearReplyTos();
				$mail->addReplyTo($params->get('email_replyto'), $params->get( 'email_replyto_name', $global_name));
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email reply to: '.$params->get('email_replyto');
			}
			
			// Auto-reply
			if ($email_autoreply)
			{
				// load email body template
				$tmpl = $params->get('email_tmpl_html_autoreply');
				$is_html = $tmpl ? true : false;
				$mail->IsHTML($is_html);
				if ($is_html) {
					$body = file_get_contents($tmpl_path . $tmpl .'.html');
				} else {
					$body = $params->get('email_tmpl_text_autoreply');
				}
	
				self::parseTmplVars($body, $is_html, get_bloginfo('language'));
				
				// set body text direction
				$body = ($params->get('rtl', 0) ? "\xE2\x80\x8F" : "\xE2\x80\x8E") . $body;
				
				// set body text
				$mail->setBody($body);
				
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'User auto-reply email ready';
		
				// send auto-reply email
				if ($mail->Send() !== true) 
				{
					return array('status' => 304, 'msg' => __('Error sending auto-reply', 'pwebcontact'));
				} 
				elseif (PWEBCONTACT_DEBUG) self::$logs[] = 'User auto-reply email sent successfully';
			}
			
			// User email copy
			if ($email_copy)
			{
				// set attachments as files
				if ($params->get('attachment_type', 1) == 1 AND count($data['attachments']))
				{
					$path = $params->get('upload_path');
					foreach ($data['attachments'] as $file)
						$mail->addAttachment($path . $file, $file);
					if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email attachments: '.implode(', ', $data['attachments']);
				}
				
				// load email body template
				$tmpl = $params->get('email_tmpl_html_user');
				$is_html = $tmpl ? true : false;
				$mail->IsHTML($is_html);
				if ($is_html) {
					$body = file_get_contents($tmpl_path . $tmpl .'.html');
				} else {
					$body = $params->get('email_tmpl_text_user');
				}
	
				self::parseTmplVars($body, $is_html, get_bloginfo('language'));
				
				// set body text direction
				$body = ($params->get('rtl', 0) ? "\xE2\x80\x8F" : "\xE2\x80\x8E") . $body;
				
				// set body text
				$mail->setBody($body);
				
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'User email ready';
		
				// send User email
				if ($mail->Send() !== true) 
				{
					return array('status' => 305, 'msg' => __('Error sending email to user', 'pwebcontact'));
				} 
				elseif (PWEBCONTACT_DEBUG) self::$logs[] = 'User email sent successfully';
			}
		}


		// Demo: do not send email to Admin
		if ($params->get('demo', 0)) {
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'DEMO mode is not sending email to Admin';
			return array('status' => 102, 'msg' => $success_msg, 'ticket' => $data['ticket']);
		}


		// Administrator email language
		/*$email_lang = $params->get('email_lang');
		if ($email_lang) 
		{
			if ($email_lang != get_bloginfo('language')) 
			{
				$lang->setLanguage($email_lang);
				$lang->load();
				$lang->load('mod_pwebcontact');
				$params->set('rtl', $lang->isRTL());
				
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email language loaded: '.$email_lang;
			}
			else {
				$params->set('email_lang', false);
			}
		}*/


		// Administrator email
		$mail = JFactory::getMailer(); //TODO

		// add recipient
		$mail->addRecipient($email_to);
		if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin emails: '.implode(', ', $email_to);
			
		// set subject
		$mail->setSubject($data['subject']);
		if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email subject: '.$data['subject'];

		// set sender
		if ($user_email AND !$params->get('server_sender', 0)) {
			$mail->setSender(array($user_email, $user_name));
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email Sender: '.$user_email;
		} else {
			$mail->setSender(array($global_email, $global_name));
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email Sender: '.$global_email;
			
			// set reply to
			if ($user_email) {
				$mail->ClearReplyTos();
				$mail->addReplyTo(array($user_email, $user_name));
				if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email reply to: '.$user_email;
			}
		}

		// Add blind carbon copy recipients
		if ($params->get('email_bcc')) 
		{
			$email_bcc = @explode(',', $params->get('email_bcc'));
			$mail->addBCC($email_bcc);
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin BCC recipients: '.$params->get('email_bcc');
		}
		
		// Add User email as blind carbon copy in debug mode
		if (PWEBCONTACT_DEBUG AND $user_email)
		{
			$mail->addBCC($user_email);
			self::$logs[] = 'Admin BCC debug recipient: '.$user_email;
		}

		// set attachments as files
		if ($params->get('attachment_type', 1) == 1 AND count($data['attachments']))
		{
			$path = $params->get('upload_path');
			foreach ($data['attachments'] as $file)
				$mail->addAttachment($path . $file, $file);
			if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email attachments: '.implode(', ', $data['attachments']);
		}

		// load email body template
		$tmpl = $params->get('email_tmpl_html_admin');
		$is_html = $tmpl ? true : false;
		$mail->IsHTML($is_html);
		if ($is_html) {
			$body = file_get_contents($tmpl_path . $tmpl .'.html');
		} else {
			$body = $params->get('email_tmpl_text_admin');
		}

		self::parseTmplVars($body, $is_html, get_bloginfo('language'));

		// set body text direction
		$body = ($params->get('rtl', 0) ? "\xE2\x80\x8F" : "\xE2\x80\x8E") . $body;

		// set body text
		$mail->setBody($body);

		if (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email ready';

		// send Admin email
		if ($mail->Send() !== true) 
		{
			return array('status' => 306, 'msg' => __('Error sending email to admin', 'pwebcontact'));
		}
		elseif (PWEBCONTACT_DEBUG) self::$logs[] = 'Admin email sent successfully';


		return array('status' => 100, 'msg' => $success_msg, 'ticket' => $data['ticket']);
	}
	
	
	protected static function getSystemInfo() 
	{
		global $wp_version;
        
        if (!self::$sys_info) 
		{
			self::$sys_info = array(
                'WordPress version '.$wp_version, 
                'Contact Form version '.self::getVersion()
            );
		}
		
		return self::$sys_info;
	}
    
    
    protected function getVersion() {
        
        $data = get_plugin_data(dirname(__FILE__).'/pwebcontact.php');
        return $data['Version'];
    }


	protected static function parseTmplVars(&$content, $is_html = true, $lang_code = 'en-GB')
	{
		$cache_key = $lang_code .'_'. (int)$is_html .'_'. md5($content);
		
		if (!isset(self::$email_tmpls[$cache_key]))
		{
			$params = self::getParams();
			
			$patterns = $replacements = $fields_replacements = array();
			
			// text direction
			if ($is_html) 
			{
				$patterns[] = '{dir}';
				$replacements[] = $params->get('rtl', 0) ? 'rtl' : 'ltr';
			}
			
			// Language variables
			if (preg_match_all('/{lang:([^}]+)}/i', $content, $lang_vars, PREG_SET_ORDER))
			{
				foreach ($lang_vars as $variable)
				{
					$patterns[] 	= $variable[0];
					$replacements[] = __($variable[1], 'pwebcontact');
				}
			}
			
			
			// Varaibles with fields
			$cache_fields_key = $lang_code .'_'. (int)$is_html .'_fields';
			$search_fields = strpos($content, '{fields}') !== false;
			$fields = self::getFields();
			
			foreach ($fields as $field)
			{
				// skip all separators which does not have any data
				if (strpos($field->type, 'separator') !== false) continue;
				
				if (isset(self::$data['fields'][$field->alias])) {
					$value = self::$data['fields'][$field->alias];
				} else {
					$value = null;
				}
				
				switch ($field->type)
				{
					case 'textarea':
						if ($is_html AND $value) 
							$value = nl2br($value);
						break;
					case 'checkboxes':
					case 'multiple':
						if (is_array($value)) {
							foreach ($value as &$val)
								$val = __($val, 'pwebcontact');
							$value = implode(', ', $value);
						}
						break;
					case 'checkbox':
					case 'radio':
					case 'select':
						if ($value) 
							$value = __($value, 'pwebcontact');
						break;
				}
				
				$patterns[] 	= '{'.$field->alias.'.value}';
				$replacements[] = $value;
				
				$patterns[] 	= '{'.$field->alias.'.label}';
				$replacements[] = $name = __($field->name, 'pwebcontact');
				
				if ($search_fields AND !isset(self::$email_tmpls[$cache_fields_key])) {
					//TODO test RTL if need to change position of sprintf arguments
					$fields_replacements[] = sprintf(__($is_html ? '<strong>%s</strong>: %s' : '%s: %s', 'pwebcontact'), $name, $value);
				}
			}
			
			
			// all fields
			if ($search_fields) 
			{
				if (!isset(self::$email_tmpls[$cache_fields_key])) {
					self::$email_tmpls[$cache_fields_key] = implode($is_html ? '<br>' : "\r\n", $fields_replacements);
				}
				$patterns[] 	= '{fields}';
				$replacements[] = self::$email_tmpls[$cache_fields_key];
			}


			// attachments
			if (strpos($content, '{files}') !== false)
			{
				$patterns[] = '{files}';
				
				// attachments as links
				if (count(self::$data['attachments']) AND $params->get('attachment_type', 1) == 2)
				{
					$cache_files_key = $lang_code .'_'. (int)$is_html .'_files';
					if (!isset(self::$email_tmpls[$cache_files_key]))
					{
						$urls = array();
						$url = $params->get('upload_url');
						foreach (self::$data['attachments'] as $file)
							$urls[] = sprintf(__($is_html ? '<a href="%s" target="_blank">%s</a>' : '%s', 'pwebcontact'), $url.rawurlencode($file), $file);

						self::$email_tmpls[$cache_files_key] = implode($is_html ? '<br>' : "\r\n", $urls);
					}
					
					$replacements[] = self::$email_tmpls[$cache_files_key];
				}
				else 
				{
					$replacements[] = '';
				}
			}
			
			
			// system
			foreach (self::$email_vars as $variable => $value)
			{
				$patterns[] 	= '{'.$variable.'}';
				$replacements[] = $value;
			}
			
			
			// replace email variables with values
			$content = str_replace($patterns, $replacements, $content);
			
			
			self::$email_tmpls[$cache_key] = $content;
		}
		else $content = self::$email_tmpls[$cache_key];
	}


	protected static function detectBrowser()
	{
		/* Requires plugin
         * http://wordpress.org/plugins/php-browser-detection/
         */
		
        if (function_exists('get_browser_name') AND function_exists('get_browser_version')) {
            return ucfirst( get_browser_name() ).' '.get_browser_version();
        }
        
		return null;
	}


	protected static function detectIP()
	{
		if (isset($_SERVER['REMOTE_ADDR'])) 
			$ip = $_SERVER['REMOTE_ADDR'];
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif (isset($_SERVER['HTTP_VIA'])) 
			$ip = $_SERVER['HTTP_VIA'];
		else 
			$ip = 'unknown';
		
		return $ip;
	}


	protected static function detectOS()
	{
		$os_name = null;
		$os = array(
			// Mircrosoft Windows Operating Systems
			'Windows 8.1' => 'Windows NT 6.3',
			'Windows 8' => 'Windows NT 6.2',
			'Windows 7' => 'Windows NT 6.1',
			'Windows Vista' => 'Windows NT 6.0',
			'Windows XP' => 'Windows NT 5.1',
			'Windows Server 2003' => 'Windows NT 5.2',
			'Windows 2000' => 'Windows NT 5.0|Windows 2000',
			'Windows NT 4.0' => 'Windows NT 4.0|WinNT4.0|WinNT|Windows NT',
			'Windows ME' => 'Windows ME|Windows 98; Win 9x 4.90',
			'Windows 98' => 'Windows 98|Win98',
			'Windows 95' => 'Windows 95|Win95|Windows_95',
			'Windows CE' => 'Windows CE',
			'Windows Phone %s' => 'Windows Phone OS (\d+(\.\d+)*)+',
			'Windows Phone %s' => 'Windows Phone (\d+(\.\d+)*)+',
			'Windows' => 'Windows',
			// Apple Mobile Devices
			'iPod iPhone OS %s' => 'iPod.+iPhone OS (\d+(_\d+)*)+',
			'iPhone OS %s' => 'iPhone OS (\d+(_\d+)*)+',
			'iPad OS %s' => 'iPad.+OS (\d+(_\d+)*)+',
			'iPhone' => 'iPhone',
			// Apple Mac Operating Systems
			'Mac OS X Cheetah' => 'Mac OS X 10.0',
			'Mac OS X Puma' => 'Mac OS X 10.1',
			'Mac OS X Jaguar' => 'Mac OS X 10.2',
			'Mac OS X Panther' => 'Mac OS X 10.3',
			'Mac OS X Tiger' => 'Mac OS X 10.4',
			'Mac OS X Leopard' => 'Mac OS X 10.5',
			'Mac OS X Snow Leopard' => 'Mac OS X 10.6',
			'Mac OS X Lion' => 'Mac OS X 10.7',
			'Mac OS X Mountain Lion' => 'Mac OS X 10.8',
			'Mac OS X Mavericks' => 'Mac OS X 10.9',
			'Mac OS X%s' => 'Mac OS X( \d+\.\d+)*',
			'Mac OS' => 'Mac_PowerPC|PowerPC|Macintosh',
			// Mobile Devices
			'Andriod %s' => 'Android (\d+(\.\d+)*)+',
			'SymbianOS' => 'Symbian|SymbOS',
			// Linux Operating Systems
			'Ubuntu %s' => 'Ubuntu[\/ ]+(\d+(\.\d+)*)+',
			'Ubuntu' => 'Ubuntu',
			'Fedora' => 'Fedora',
			'Red Hat' => 'Red Hat',
			'OpenSUSE' => 'SUSE',
			'Debian' => 'Debian',
			'Mandriva' => 'Mandriva',
			'Linux Mint' => 'Linux Mint',
			'PCLinuxOS' => 'PCLinuxOS',
			'CentOS' => 'CentOS',
			'Aurox' => 'Aurox',
			'Chromium OS' => 'ChromiumOS',
			'Google Chrome OS' => 'ChromeOS',
			// Kernel
			'Linux' => 'Linux|X11',
			// UNIX Like Operating Systems
			'Open BSD' => 'OpenBSD',
			'SunOS' => 'SunOS',
			'Solaris' => 'Solaris',
			'CentOS' => 'CentOS',
			'QNX' => 'QNX',
			// Kernels
			'UNIX' => 'UNIX',
			// BSD Operating Systems
			'OpenBSD' => 'OpenBSD',
			'FreeBSD' => 'FreeBSD',
			'NetBSD' => 'NetBSD',
			//DEC Operating Systems
			'OS/8' => 'OS\/8|OS8',
			'Older DEC OS' => 'DEC|RSTS|RSTS\/E',
			'WPS-8' => 'WPS-8|WPS8',
			// BeOS Like Operating Systems
			'BeOS' => 'BeOS|BeOS r5',
			'BeIA' => 'BeIA',
			// OS/2 Operating Systems
			'OS/2 2.0' => 'OS\/220|OS\/2 2.0',
			'OS/2' => 'OS\/2|OS2'
		);
		
		foreach ($os as $name => $regExp) {
			if (preg_match('/'.$regExp.'/i', $_SERVER['HTTP_USER_AGENT'], $match)) {
				$os_name = sprintf($name, array_key_exists(1, $match) ? $match[1] : '');
				break;
			}
		}
		
		return $os_name;
	}
}

class PWebContact_Params {
    
    protected $data = array();
    
    public function __construct($params) {
        
        if (is_string($params))
            $params = json_decode($params, true);
        
        $this->data = $params;
    }
    
    public function def($key = null, $value = null) {
        
        $this->data[$key] = $value;
    }
    
    public function set($key = null, $value = null) {
        
        $old_value = $this->get($key);
        $this->data[$key] = $value;
        
        return $old_value;
    }
    
    public function get($key = null, $default = null) {
        
        if ($key) {
            if (isset($this->data[$key]) AND $this->data[$key]) {
                return $this->data[$key];
            }
            return $default;
        }
        else return $this->data;
    }
}

if (!function_exists('exceptions_error_handler'))
{
	function exceptions_error_handler($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting
			return;
		}
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
}