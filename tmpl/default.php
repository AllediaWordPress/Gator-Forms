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

$user = wp_get_current_user();

$separators = 0;
$row        = 0;
$column 	= 0;
$page 		= 0;
$pages 		= array();

$toggler = 
	 '<div id="pwebcontact'.$form_id.'_toggler" class="pwebcontact'.$form_id.'_toggler pwebcontact_toggler pweb-closed '.$params->get('togglerClass').'">'
	.'<span class="pweb-text">'.(!$params->get('toggler_vertical', 0) ? $params->get('toggler_name_open') : ' ').'</span>'
	.'<span class="pweb-icon"></span>'
	.'</div>';
	
$message =
	 '<div class="pweb-msg pweb-msg-'.$params->get('msg_position', 'after').'"><div id="pwebcontact'.$form_id.'_msg" class="pweb-progress">'
	.'<script type="text/javascript">document.getElementById("pwebcontact'.$form_id.'_msg").innerHTML="'.__('Initializing form...', 'pwebcontact').'"</script>'
	.'</div></div>';

?>
<!-- PWebContact -->

<!-- PRO START -->
<?php if ($layout == 'modal' AND $params->get('handler') == 'button') : ?>
<div class="<?php echo $params->get('moduleClass'); ?>" dir="<?php echo $params->get('rtl', 0) ? 'rtl' : 'ltr'; ?>">
	<?php echo $toggler; ?>
</div>
<?php endif; ?>
<!-- PRO END -->

<div id="pwebcontact<?php echo $form_id; ?>" class="pwebcontact <?php echo $params->get('positionClass').' '.$params->get('moduleClass'); ?>" dir="<?php echo $params->get('rtl', 0) ? 'rtl' : 'ltr'; ?>">
	
	<?php 
    if ( ($layout == 'accordion' AND $params->get('handler') == 'button') 
        OR ( ( ($layout == 'slidebox' AND !$params->get('toggler_slide')) OR $layout == 'modal') AND $params->get('handler') == 'tab' ) 
       )
        echo $toggler; 
    ?>
	
    <!-- PRO START -->
	<?php if ($layout == 'modal') : ?><div id="pwebcontact<?php echo $form_id; ?>_modal" class="pwebcontact-modal modal<?php if ((int)$params->get('bootstrap_version', 2) === 2) echo ' hide fade'; ?>" style="display:none"><?php endif; ?>
	<!-- PRO END -->
    
    <div id="pwebcontact<?php echo $form_id; ?>_box" class="pwebcontact-box <?php echo $params->get('moduleClass').' '.$params->get('boxClass'); ?>" dir="<?php echo $params->get('rtl', 0) ? 'rtl' : 'ltr'; ?>">
	<div id="pwebcontact<?php echo $form_id; ?>_container" class="pwebcontact-container">
	
		<?php 
		if ($layout == 'slidebox' AND $params->get('handler') == 'tab' AND $params->get('toggler_slide')) echo $toggler;
		
		if ($layout == 'accordion' OR ($layout == 'modal' AND !$params->get('modal_disable_close', 0))) : ?>
		<button type="button" class="pwebcontact<?php echo $form_id; ?>_toggler pweb-button-close" aria-hidden="true"<?php if ($value = $params->get('toggler_name_close')) echo ' title="'.$value.'"' ?> data-role="none">&times;</button>
		<?php endif; ?>
		
		<?php if ($layout == 'accordion') : ?><div class="pweb-arrow"></div><?php endif; ?>
		
		<form name="pwebcontact<?php echo $form_id; ?>_form" id="pwebcontact<?php echo $form_id; ?>_form" class="pwebcontact-form" action="<?php echo esc_url( home_url() ); ?>" method="post" accept-charset="utf-8">
			
            <!-- PRO START -->
			<?php if ($params->get('msg_position', 'after') == 'before') echo $message; ?>
            <!-- PRO END -->
			
			<div class="pweb-fields">
			<?php 
            
			/* ----- Form --------------------------------------------------------------------------------------------- */
			foreach ($fields as $field) :
			
				/* ----- Separators ----- */
				if ($field['type'] == 'page') : 
					$page++;
                    $row = 0;
					$column = 0;
                    $pages[$page] = array();
                
                elseif ($field['type'] == 'row') : 
					$row++;
					$column = 0;
                    $pages[$page][$row] = array();
                
                elseif ($field['type'] == 'column') : 
					// create new empty column slot
                    $column++;
                    $pages[$page][$row][$column] = null;
				
				
				else :
					
                    // create new column slot
                    $column++;
                    $pages[$page][$row][$column] = null;
                
					ob_start();
                    
                    
                    /* ----- Buttons ------------------------------------------------------------------------------------------ */
                    if ($field['type'] == 'button_send') :
                     ?>
					<div class="pweb-field-container pweb-field-buttons">
						<div class="pweb-field">
							<button id="pwebcontact<?php echo $form_id; ?>_send" type="button" class="btn" data-role="none"><?php _e($field['label'] ? $field['label'] : 'Send', 'pwebcontact') ?></button>
							<?php if ($params->get('reset_form', 1) == 3) : ?>
							<button id="pwebcontact<?php echo $form_id; ?>_reset" type="reset" class="btn" style="display:none" data-role="none"><i class="icon-remove-sign icon-white"></i> <?php _e($params->get('button_reset', 'Reset'), 'pwebcontact') ?></button>
							<?php endif; ?>
                            <!-- PRO START -->
							<?php if ($params->get('msg_position', 'after') == 'button' OR $params->get('msg_position', 'after') == 'popup') echo $message; ?>
                            <!-- PRO END -->
                        </div>
					</div>
                    <?php
					
                    
					/*** PRO START ***/
					/* ----- Text separator --------------------------------------------------------------------------- */
					elseif ($field['type'] == 'separator_text') : 
						$fieldId = 'pwebcontact'.$form_id.'_text-'.$separators++;
					?>
					<div class="pweb-field-container pweb-separator-text" id="<?php echo $fieldId; ?>">
						<?php _e($field['value'], 'pwebcontact'); ?>
					</div>
					<?php 
                    
                    /* ----- Header separator --------------------------------------------------------------------------- */
					elseif ($field['type'] == 'separator_header') : 
						$fieldId = 'pwebcontact'.$form_id.'_header-'.$separators++;
					?>
					<div class="pweb-field-container pweb-separator-header" id="<?php echo $fieldId; ?>">
						<?php _e($field['label'], 'pwebcontact'); ?>
					</div>
					<?php 
					
                    
					/* ----- Mail to list ------------------------------------------------------------------------------- */
					elseif ($field['type'] == 'mailto_list') :
						
						$optValues = @explode(PHP_EOL, $field['values']);
                        if (count($optValues)) :

                            $fieldId 	= 'pwebcontact'.$form_id.'_mailto';
                            $i 			= 1;
					?>
					<div class="pweb-field-container pweb-field-select pweb-field-mailto">
						<div class="pweb-label">
							<label for="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>-lbl">
								<?php _e('Contact with', 'pwebcontact'); ?>
								<span class="pweb-asterisk">*</span>
							</label>
						</div>
						<div class="pweb-field">
							<select name="mailto" id="<?php echo $fieldId; ?>" class="required" data-role="none">
								<option value=""><?php _e('-- Select --', 'pwebcontact'); ?></option>
							<?php foreach ($optValues as $value) : 
								// Skip empty rows
								if (empty($value)) continue;
								// Get recipient
								$recipient = @explode('|', $value);
								// Skip incorrect rows
								if (!array_key_exists(1, $recipient)) continue;
							?>
								<option value="<?php echo $i++; ?>"><?php echo $recipient[1]; ?></option>
							<?php endforeach; ?>
							</select>
						</div>
					</div>
					<?php
						endif;
					
					
					/* ----- Captcha ---------------------------------------------------------------------------- */
					elseif ($field['type'] == 'captcha') :
						
						$fieldId = 'pwebcontact'.$form_id.'_captcha';
					?>
					<div class="pweb-field-container pweb-field-captcha">
						<div class="pweb-label">
							<label for="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>-lbl">
								<?php _e($field['label'] ? $field['label'] : 'Captcha code', 'pwebcontact'); ?>
								<span class="pweb-asterisk">*</span>
							</label>
						</div>
						<div class="pweb-field pweb-captcha">
							<?php //TODO ?>
						</div>
					</div>
					<?php 
                    
                    
                    /* ----- Email copy --------------------------------------------------------------------------- */
                    elseif ($field['type'] == 'email_copy') :
						
                        if ($params->get('email_copy', 2) == 1) :
                            $fieldId = 'pwebcontact'.$form_id.'_copy';
					?>
					<div class="pweb-field-container pweb-field-checkbox pweb-field-copy">
						<div class="pweb-field">
							<input type="checkbox" name="copy" id="<?php echo $fieldId; ?>" value="1" class="pweb-checkbox" data-role="none">
							<label for="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>-lbl">
								<?php _e($field['label'] ? $field['label'] : 'Send a copy to yourself', 'pwebcontact'); ?>
							</label>
						</div>
					</div>
					<?php 
                        endif;
					
					/* ----- Upload ----------------------------------------------------------------------------------- */
					elseif ($field['type'] == 'upload') :
						
                        $params->def('show_upload', 1);
                    
                        $fieldId = 'pwebcontact'.$form_id.'_uploader';

                        $field['attributes'] = null;
                        $field['class'] = null;
                        $field['title'] = array();
                        if ($params->get('upload_show_limits')) {
                            $exts = @explode('|', $params->get('upload_allowed_ext'));
                            $types = array();
                            foreach ($exts as $ext) {
                                $tmp = @explode('?', $ext);
                                $types[] = $tmp[0];
                                if (array_key_exists(1, $tmp)) $types[] = $tmp[0].$tmp[1];
                            }
                            $field['title'][] = sprintf(__('Select a file or drag and drop on form. Max file size %s, max number of files %s, allowed file types: %s. ', 'pwebcontact'), 
                                floatval($params->get('upload_size_limit', 1)).'MB',
                                intval($params->get('upload_files_limit', 5)),
                                implode(', ', $types)
                            );
                        }
                        if ($value = $field['tooltip']) {
                            $field['title'][] = __($value, 'pwebcontact');
                        }
                        if (count($field['title'])) {
                            $field['class'] = ' pweb-tooltip';
                            $field['attributes'] .= ' title="'.htmlspecialchars(implode(' ', $field['title']), ENT_COMPAT, 'UTF-8').'"';
                        }
					?>
					<div class="pweb-field-container pweb-field-uploader">
						<div class="pweb-label">
							<label for="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>-lbl">
								<?php _e($field['label'] ? $field['label'] : 'Attachment', 'pwebcontact'); ?>
								<?php if ($field['required']) : ?><span class="pweb-asterisk">*</span><?php endif; ?>
							</label>
						</div>
						<div class="pweb-field pweb-uploader" id="<?php echo $fieldId; ?>_container">
							<div class="fileupload-buttonbar">
								<span class="fileinput-button btn<?php echo $field['class']; ?>"<?php echo $field['attributes']; ?>>
				                    <i class="icon-plus-sign icon-white"></i>
				                    <span><?php _e('Add files', 'pwebcontact'); ?></span>
				                    <input type="file" name="files[]" multiple="multiple" id="<?php echo $fieldId; ?>"<?php if ($field['required']) echo ' class="pweb-validate-uploader"'; ?> data-role="none">
				                </span>
							</div>
							<div class="files"></div>
							<div class="templates" style="display:none" aria-hidden="true">
								<div class="template-upload fade">
									<span class="ready"><i class="icon-upload"></i></span>
									<span class="warning"><i class="icon-warning-sign"></i></span>
				                	<span class="name"></span>
				                	<span class="size"></span>
				                	<span class="error invalid"></span>
				                	<a href="#" class="cancel"><i class="icon-remove"></i><?php _e('Cancel', 'pwebcontact'); ?></a>
				                	<div class="progress progress-striped active"><div class="bar progress-bar" style="width:0%"></div></div>
				                </div>
								<div class="template-download fade">
									<span class="success"><i class="icon-ok"></i></span>
									<span class="warning"><i class="icon-warning-sign"></i></span>
				                	<span class="name"></span>
				                    <span class="size"></span>
				                    <span class="error invalid"></span>
				                    <a href="#" class="delete"><i class="icon-trash"></i><?php _e('Delete', 'pwebcontact'); ?></a>
				                </div>
							</div>
						</div>
					</div>
					<?php 
                    /*** PRO END ***/
                    
                    /* ----- Fields ----------------------------------------------------------------------------------- */
					else : 
						
						$fieldId = 'pwebcontact'.$form_id.'_field-'.$field['alias'];
						$fieldName = 'fields['.$field['alias'].']';
					?>
					<div class="pweb-field-container pweb-field-<?php echo $field['type']; ?> pweb-field-<?php echo $field['alias']; ?>">
						<?php 
						
						if ($field['type'] != 'checkbox') : 
						/* ----- Label -------------------------------------------------------------------------------- */ ?>
						<div class="pweb-label">
							<label id="<?php echo $fieldId; ?>-lbl"<?php if ($field['type'] != 'checkboxes' AND $field['type'] != 'radio') echo ' for="'.$fieldId.'"'; ?>>
								<?php _e($field['label'], 'pwebcontact'); ?>
								<?php if ($field['required']) : ?><span class="pweb-asterisk">*</span><?php endif; ?>
							</label>
						</div>
						<?php endif; ?>
						<div class="pweb-field">
							<?php 
							
							
							/* ----- Text fields: text, name, email, phone, subject, password, date ------------------------- */
							if (in_array($field['type'], array('text', 'name', 'email', 'phone', 'subject', 'password', 'date'))) : 
								
                                /*** PRO START ***/
								if ($user->ID AND ($field['type'] == 'name' OR $field['type'] == 'email') AND $params->get('user_data', 1)) {
									$field['values'] = $field['type'] == 'email' ? $user->email : $user->display_name;
								}
                                /*** PRO END ***/
								
								$field['attributes'] = null;
								$field['classes'] = array('pweb-input');
								if ($field['required']) 
									$field['classes'][] = 'required';
								
								if (isset($field['validation']) AND $field['validation']) 
									$field['classes'][] = 'pweb'.$form_id.'-validate-'.$field['alias'];
								
								if ($field['tooltip']) {
									$field['classes'][] = 'pweb-tooltip';
									$field['attributes'] .= ' title="'.htmlspecialchars($field['tooltip'], ENT_COMPAT, 'UTF-8').'"';
								}
	
								if (count($field['classes']))
									$field['attributes'] .= ' class="'.implode(' ', $field['classes']).'"';
								
								switch ($field['type']) {
									case 'email':
										$field['classes'][] = 'email';
										$type = 'email';
										break;
                                    /*** PRO START ***/
									case 'password':
										$type = 'password';
										break;
									case 'phone':
										$type = 'tel';
										break;
                                    /*** PRO END ***/
									default:
										$type = 'text';
								}
							?>
							<input type="<?php echo $type; ?>" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>"<?php echo $field['attributes']; ?> value="<?php echo htmlspecialchars($field['values'], ENT_COMPAT, 'UTF-8'); ?>" data-role="none">
							<?php 
                            /*** PRO START ***/
                            if ($field['type'] == 'date') : ?>
							<span class="pweb-calendar-btn" id="<?php echo $fieldId; ?>_btn"><i class="icomoon-calendar"></i></span>
							<?php endif;
                            /*** PRO END ***/
							
							
							/* ----- Textarea ------------------------------------------------------------------------- */
							elseif ($field['type'] == 'textarea') :
								$field['attributes'] = null;
								$field['classes'] = array();
								
								$field['attributes'] .= ' rows="'.($field['rows'] ? (int)$field['rows'] : 5).'"';
								if ($field['maxlength']) {
									$field['attributes'] .= ' maxlength="'.$field['maxlength'].'"';
								}
								if ($field['required']) 
									$field['classes'][] = 'required';
								
								if ($field['tooltip']) {
									$field['classes'][] = 'pweb-tooltip';
									$field['attributes'] .= ' title="'.htmlspecialchars($field['tooltip'], ENT_COMPAT, 'UTF-8').'"';
								}
								if (count($field['classes']))
									$field['attributes'] .= ' class="'.implode(' ', $field['classes']).'"';
							?>
							<textarea name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>" cols="50"<?php echo $field['attributes']; ?> data-role="none"><?php echo htmlspecialchars($field['values'], ENT_COMPAT, 'UTF-8'); ?></textarea>
							<?php if ($field['maxlength']) : ?>
							<div class="pweb-chars-counter"><?php echo sprintf(__('%s characters left', 'pwebcontact'), '<span id="'.$fieldId.'-limit">'.$field['maxlength'].'</span>'); ?></div>
							<?php endif; ?>	
							<?php 
							
							
                            /*** PRO START ***/
							/* ----- Select and Multiple select ------------------------------------------------------- */
							elseif ($field['type'] == 'select' OR $field['type'] == 'multiple') : 
								$optValues = is_array($field['values']) ? $field['values'] : @explode('|', $field['values']);
								$field['attributes'] = null;
								$field['classes'] = array();
								
								if ($field['required']) 
									$field['classes'][] = 'required';
								
								if ($field['type'] == 'multiple') 
								{
									$field['classes'][] = 'pweb-multiple';
									$fieldName 		 .= '[]';
									
									$optCount 		= count($optValues);
									$field['rows'] 	= $field['rows'] ? (int)$field['rows'] : 4;
									$field['rows'] 	= $field['rows'] > $optCount ? $optCount : $field['rows'];
									
									$field['attributes'] .= ' multiple="multiple" size="'.$field['rows'].'"';
								}
								else {
									$field['classes'][] = 'pweb-select';
								}
								
								if ($field['tooltip']) {
									$field['classes'][] = 'pweb-tooltip';
									$field['attributes'] .= ' title="'.htmlspecialchars($field['tooltip'], ENT_COMPAT, 'UTF-8').'"';
								}
								
								if (count($field['classes']))
									$field['attributes'] .= ' class="'.implode(' ', $field['classes']).'"';
							?>
							<select name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>"<?php echo $field['attributes']; ?> data-role="none">
							<?php if ($field['type'] == 'select' AND $field['default']) : ?>
								<option value=""><?php _e($field['default'], 'pwebcontact'); ?></option>
							<?php endif; ?>
							<?php foreach ($optValues as $value) : ?>
								<option value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"><?php _e($value, 'pwebcontact'); ?></option>
							<?php endforeach; ?>
							</select>
							<?php 
							
							
							/* ----- Checkboxes and Radio group ------------------------------------------------------- */
							elseif ($field['type'] == 'checkboxes' OR $field['type'] == 'radio') : 
								$i 			= 0;
								
								$type 		= $field['type'] == 'checkboxes' ? 'checkbox' : 'radio';
								$optValues 	= is_array($field['values']) ? $field['values'] : @explode('|', $field['values']);
								
								$optCount 	= count($optValues);
								$optColumns = (int)$field['cols'];
								$optRows	= false;
								if ($optColumns > 1 AND $optCount >= $optColumns) 
								{
									$optCount 	= count($optValues);
									$optRows 	= ceil($optCount / $optColumns);
									$width 		= floor(100 / $optColumns);
									$cols 		= 1;
								}
								if ($field['type'] == 'checkboxes') 
									$fieldName .= '[]';
							?>
							<fieldset id="<?php echo $fieldId; ?>" class="pweb-fields-group<?php if ($field['tooltip']) echo ' pweb-tooltip" title="'.htmlspecialchars($field['tooltip'], ENT_COMPAT, 'UTF-8'); ?>">
							<?php 
							/* ----- Options in multiple columns ----- */
							if ($optRows) : ?>
							<div class="pweb-column pweb-width-<?php echo $width; ?>">
							<?php foreach ($optValues as $value) : ?>
								<input type="<?php echo $type; ?>" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId.'_'.$i; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" class="pweb-<?php echo $type; ?> pweb-fieldset<?php if ($i == 0 AND $field['required']) echo ' required'; ?>" data-role="none">
								<label for="<?php echo $fieldId.'_'.$i++; ?>">
									<?php _e($value, 'pwebcontact'); ?>
								</label>
							<?php // Column separator
							if (($i % $optRows) == 0 AND $cols < $optColumns) : $cols++; ?>
							</div><div class="pweb-column pweb-width-<?php echo $width; ?>">
							<?php endif;
							endforeach; ?>
							</div>
							<?php 
							/* ----- Options in one column ----- */
							else :
							foreach ($optValues as $value) : ?>
								<input type="<?php echo $type; ?>" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId.'_'.$i; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" class="pweb-<?php echo $type; ?> pweb-fieldset<?php if ($i == 0 AND $field['required']) echo ' required'; ?>" data-role="none">
								<label for="<?php echo $fieldId.'_'.$i++; ?>">
									<?php _e($value, 'pwebcontact'); ?>
								</label>
							<?php endforeach;
							endif; ?>
							</fieldset>
							<?php 
							
							
							/* ----- Single checkbox ------------------------------------------------------------------ */
							elseif ($field['type'] == 'checkbox') : ?>
								<input type="checkbox" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>" class="pweb-checkbox pweb-single-checkbox<?php if ($field['required']) echo ' required'; ?>" value="<?php echo $field['values'] ? htmlspecialchars($field['values'], ENT_COMPAT, 'UTF-8') : 'JYes'; ?>" data-role="none">
								<label for="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>-lbl"<?php if ($field['tooltip']) echo ' class="pweb-tooltip" title="'.htmlspecialchars($field['tooltip'], ENT_COMPAT, 'UTF-8').'"'; ?>>
                                    <?php _e($field['label'], 'pwebcontact'); ?>
                                    <?php if ($field['required']) : ?>
                                        <span class="pweb-asterisk">*</span>
                                    <?php endif; ?>
								</label>
							<?php
                            
                            
                            /* ----- Single checkbox with Terms & Conditions ----------------------------------------- */
							elseif ($field['type'] == 'checkbox_modal') : ?>
								<input type="checkbox" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>" class="pweb-checkbox pweb-single-checkbox<?php if ($field['required']) echo ' required'; ?>" value="<?php echo $field['values'] ? htmlspecialchars($field['values'], ENT_COMPAT, 'UTF-8') : 'JYes'; ?>" data-role="none">
								<label for="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>-lbl"<?php if ($field['tooltip']) echo ' class="pweb-tooltip" title="'.htmlspecialchars($field['tooltip'], ENT_COMPAT, 'UTF-8').'"'; ?>>
								<?php if ($field['url']) : ?>
									<a href="<?php echo $field['url']; ?>" target="_blank"<?php if ($field['target'] == 1) echo ' class="pweb-modal-url"'; ?>>
                                        <?php _e($field['label'], 'pwebcontact'); ?>
                                        <span class="icon-out"></span>
                                    </a>
								<?php else : 
									_e($field['label'], 'pwebcontact'); 
								endif; ?>
								<?php if ($field['required']) : ?>
									<span class="pweb-asterisk">*</span>
								<?php endif; ?>
								</label>
							<?php 
                            /*** PRO START ***/
                            endif; ?>
						</div>
					</div>
					<?php endif;
				
					$pages[$page][$row][$column] .= ob_get_clean(); 
				
				endif;
			endforeach; 
            
	
			/* ----- Display form pages, rows and columns ------------------------------------------------------------------- */
				$pages_count = count($pages);
				foreach ($pages as $page => $rows) 
				{
					if ($pages_count > 1) echo '<div class="pweb-page" id="pwebcontact'.$form_id.'_page-'.$page.'">';
					
                    foreach ($rows as $row => $columns) 
                    {
                        //TODO join rows if have the same number of columns
                        echo '<div class="pweb-row">';
                        
                        $width = floor(100 / count($columns));
                        foreach ($columns as $column) 
                        {
                            $column = $column ? $column : '&nbsp;';
                            
                            if ($width < 100) 
                                echo '<div class="pweb-column pweb-width-'.$width.'">'.$column.'</div>';
                            else
                                echo '<div>'.$column.'</div>';
                        }
                        
                        echo '</div>';
                    }
                    
					if ($pages_count > 1) echo '</div>';
				}
				
			/* ----- Display pages navigation ------------------------------------------------------------------------- */
				if ($pages_count > 1) : ?>
					<div class="pweb-pagination">
						<button id="pwebcontact<?php echo $form_id; ?>_prev" class="btn pweb-prev" type="button" data-role="none"><span class="icon-chevron-left"></span> <?php _e('Previous', 'pwebcontact'); ?></button>
						<div class="pweb-counter">
							<span id="pwebcontact<?php echo $form_id; ?>_page_counter">1</span>
							<?php _e('of', 'pwebcontact'); ?>
							<span><?php echo $pages_count; ?></span>
						</div>
						<button id="pwebcontact<?php echo $form_id; ?>_next" class="btn pweb-next" type="button" data-role="none"><?php _e('Next', 'pwebcontact'); ?> <span class="icon-chevron-right"></span></button>
					</div>
				<?php endif;
			?>
			</div>
			
			<?php if ($params->get('msg_position', 'after') == 'after') echo $message; ?>
			
			<?php echo PWebContact::getHiddenFields(); ?>
			<input type="hidden" name="<?php echo wp_create_nonce('pwebcontact'.$form_id); ?>" value="1" id="pwebcontact<?php echo $form_id; ?>_token">
		</form>
		
        <!-- PRO START -->
		<?php if ($params->get('show_upload', 0)) : ?>
		<div class="pweb-dropzone" aria-hidden="true"><div><?php _e('Drop files here to upload', 'pwebcontact'); ?></div></div>
		<?php endif; ?>
        <!-- PRO END -->
        
	</div>
	</div>
    <!-- PRO START -->
	<?php if ($layout == 'modal') : ?></div><?php endif; ?>
    <!-- PRO END -->
</div>

<script type="text/javascript">
<?php echo $script; ?>
</script>
<!-- PWebContact end -->