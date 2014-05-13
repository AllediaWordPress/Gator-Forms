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

<h3 class="pweb-load-samples">
    <?php echo $this->_get_label(array(
        'group' => 'load',
        'name' => 'email_scheme',
        'label' => 'Choose your answer scheme'
    )); ?>

    <?php echo $this->_get_field_control(array(
        'type' => 'select',
        'group' => 'load',
        'name' => 'email_scheme',
        'options' => array(
            array(
                'name' => '- Select -',
                'value' => ''
            ),
            array(
                'name' => 'Boring',
                'value' => '{"tmpl":"boring","msg":"'.__('Your message has been successfully submitted. Thank you.', 'pwebcontact').'"}'
            ),
            array(
                'name' => 'Fancy',
                'value' => '{"tmpl":"fancy","msg":"'.__('Thanks! Message from you is now on the way to our mailbox. We can’t wait to read it :)', 'pwebcontact').'"}'
            ),
            array(
                'name' => 'Superformal',
                'value' => '{"tmpl":"superformal","msg":"'.__('The message of yours has been consigned.', 'pwebcontact').'"}'
            )
        )
    )); ?>
</h3>

<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'msg_success',
    'label' => 'Enter message which will appear after successful sending email by contact form',
    'class' => 'pweb-input-large'
)); ?>

<div class="pweb-field pweb-field-text">
    <?php echo $this->_get_label(array(
        'name' => 'email_to',
        'label' => 'Enter one or more emails to which message should be sent to',
    )); ?>
    <div class="pweb-field-control">
        <?php echo $this->_get_field_control(array(
            'type' => 'text',
            'name' => 'email_to',
            'class' => 'pweb-filter-emails pweb-input-large'
        )); ?>
        
        <?php echo __('or/and', 'pwebcontact') .' '. $this->_get_field_control(array(
            'type' => 'wp_user',
            'name' => 'email_cms_user',
            'label' => 'or choose WordPress Administrator to whom message will be sent to',
            'tooltip' => 'Enable this option to send email to selected WordPress Administrator. Do not use this option if Administrator has the same email address as in above field!'
        )); ?>
    </div>
</div>


<div class="pweb-field pweb-field-textarea">
    <h3><?php _e( 'User email', 'pwebcontact' ); ?></h3>
    <?php echo $this->_get_label(array(
        'name' => 'email_user_tmpl',
        'label' => 'Enter message which will be sent to User as copy'
    )); ?>
    <div class="pweb-field-control">
        <?php echo $this->_get_field_control(array(
            'type' => 'textarea',
            'name' => 'email_user_tmpl',
            'desc' => 'Remeber to create field type of: Send copy to yourself, to use this option.',
            'class' => 'widefat',
            'attributes' => array(
                'rows' => 10,
                'cols' => 50
            )
        )); ?>
        <div class="pweb-field-desc"><?php _e('Remeber to create field type of: Send copy to yourself, to use this option.', 'pwebcontact'); ?></div>
    </div>
    <div class="pweb-field-control">
        <?php echo $this->_get_label(array(
            'name' => 'email_user_tmpl_format',
            'label' => 'Select format'
        )); ?>
        
        <?php echo $this->_get_field_control(array(
            'type' => 'radio',
            'name' => 'email_user_tmpl_format',
            'default' => 1,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 1,
                    'name' => 'Text'
                ),
                array(
                    'value' => 2,
                    'name' => 'HTML'
                )
            )
        )); ?>
        
        <?php echo $this->_get_label(array(
            'name' => 'email_user_tmpl_list',
            'label' => 'Load template'
        )); ?>
        
        <?php echo $this->_get_field_control(array(
            'type' => 'filelist',
            'name' => 'email_user_tmpl_list',
            'filter' => '\.html$',
            'directory' => 'media/email_tmpl',
            'strip_ext' => true,
            'class' => 'pweb-load-email-tmpl',
            'attributes' => array(
                'data-action' => admin_url( 'admin.php?page=pwebcontact&task=load_email&ajax=1&_wpnonce='. wp_create_nonce('load-email') )
            ),
            'options' => array(
                array(
                    'value' => '',
                    'name' => '- Select template -'
                )
            )
        )); ?>
        
        <span class="pweb-field-desc"><?php _e( 'If you changed format of email then load template again', 'pwebcontact' ); ?></span>
    </div>
</div>

<div class="pweb-advanced-options">
    <a href="#" class="pweb-advanced-options-toggler">
        <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
    </a>
    <div class="pweb-advanced-options-content">
        
        
        <div class="pweb-field pweb-field-textarea">
            <h3><?php _e( 'Administrator email', 'pwebcontact' ); ?></h3>
            <?php echo $this->_get_label(array(
                'name' => 'email_admin_tmpl',
                'label' => 'Enter message which will be sent to Administrator'
            )); ?>
            <div class="pweb-field-control">
                <?php echo $this->_get_field_control(array(
                    'type' => 'textarea',
                    'name' => 'email_admin_tmpl',
                    'class' => 'widefat',
                    'attributes' => array(
                        'rows' => 10,
                        'cols' => 50
                    ),
                    'default' => '{fields}

{lang:Tiket}: {ticket}
{lang:Page title}: {title}
{lang:Page URL}: {url}
{lang:IP}: {ip_address}
{lang:Browser}: {browser}
{lang:Operating system}: {os}
{lang:Screen resolution}: {screen_resolution}

{files}'
                )); ?>
            </div>
            <div class="pweb-field-control">
                <?php echo $this->_get_label(array(
                    'name' => 'email_admin_tmpl_format',
                    'label' => 'Select format'
                )); ?>

                <?php echo $this->_get_field_control(array(
                    'type' => 'radio',
                    'name' => 'email_admin_tmpl_format',
                    'default' => 1,
                    'class' => 'pweb-radio-group',
                    'options' => array(
                        array(
                            'value' => 1,
                            'name' => 'Text'
                        ),
                        array(
                            'value' => 2,
                            'name' => 'HTML'
                        )
                    )
                )); ?>

                <?php echo $this->_get_label(array(
                    'name' => 'email_admin_tmpl_list',
                    'label' => 'Load template'
                )); ?>

                <?php echo $this->_get_field_control(array(
                    'type' => 'filelist',
                    'name' => 'email_admin_tmpl_list',
                    'filter' => '\.html$',
                    'directory' => 'media/email_tmpl',
                    'strip_ext' => true,
                    'class' => 'pweb-load-email-tmpl',
                    'attributes' => array(
                        'data-action' => admin_url( 'admin.php?page=pwebcontact&task=load_email&ajax=1&_wpnonce='. wp_create_nonce('load-email') )
                    ),
                    'options' => array(
                        array(
                            'value' => '',
                            'name' => '- Select template -'
                        )
                    )
                )); ?>
                
                <span class="pweb-field-desc"><?php _e( 'If you changed format of email then load template again', 'pwebcontact' ); ?></span>
            </div>
        </div>
        
        
        
        <div class="pweb-field pweb-field-textarea">
            <h3><?php _e( 'Auto-reply email', 'pwebcontact' ); ?></h3>
            <?php echo $this->_get_label(array(
                'name' => 'email_autoreply_tmpl',
                'label' => 'Enter message of auto-reply which will be sent to User always'
            )); ?>
            <div class="pweb-field-control pweb-child pweb_params_email_autoreply_format_1 pweb_params_email_autoreply_format_2">
                <?php echo $this->_get_field_control(array(
                    'type' => 'textarea',
                    'name' => 'email_autoreply_tmpl',
                    'class' => 'widefat',
                    'attributes' => array(
                        'rows' => 10,
                        'cols' => 50
                    )
                )); ?>
            </div>
            <div class="pweb-field-control">
                <?php echo $this->_get_label(array(
                    'name' => 'email_autoreply_tmpl_format',
                    'label' => 'Select format'
                )); ?>

                <?php echo $this->_get_field_control(array(
                    'type' => 'radio',
                    'name' => 'email_autoreply_tmpl_format',
                    'default' => 0,
                    'class' => 'pweb-radio-group',
                    'options' => array(
                        array(
                            'value' => 0,
                            'name' => 'Do not send'
                        ),
                        array(
                            'value' => 1,
                            'name' => 'Text',
                            'is_parent' => true
                        ),
                        array(
                            'value' => 2,
                            'name' => 'HTML',
                            'is_parent' => true
                        )
                    )
                )); ?>

                <span class="pweb-child pweb_params_email_autoreply_format_1 pweb_params_email_autoreply_format_2">
                    <?php echo $this->_get_label(array(
                        'name' => 'email_autoreply_tmpl_list',
                        'label' => 'Load template'
                    )); ?>

                    <?php echo $this->_get_field_control(array(
                        'type' => 'filelist',
                        'name' => 'email_autoreply_tmpl_list',
                        'filter' => '\.html$',
                        'directory' => 'media/email_tmpl',
                        'strip_ext' => true,
                        'class' => 'pweb-load-email-tmpl',
                        'attributes' => array(
                            'data-action' => admin_url( 'admin.php?page=pwebcontact&task=load_email&ajax=1&_wpnonce='. wp_create_nonce('load-email') )
                        ),
                        'options' => array(
                            array(
                                'value' => '',
                                'name' => '- Select template -'
                            )
                        )
                    )); ?>

                    <span class="pweb-field-desc"><?php _e( 'If you changed format of email then load template again', 'pwebcontact' ); ?></span>
                </span>
            </div>
        </div>


        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'msg_position',
            'label' => 'System message position',
            'tooltip' => 'Display message before or after form, next to send button or in popup layer',
            'header' => 'System message',
            'default' => 'after',
            'options' => array(
                array(
                    'value' => 'before',
                    'name' => 'Before form'
                ),
                array(
                    'value' => 'after',
                    'name' => 'After form'
                ),
                array(
                    'value' => 'button',
                    'name' => 'Next to Send buton'
                ),
                array(
                    'value' => 'popup',
                    'name' => 'In popup',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'msg_close_delay',
            'label' => 'Popup message close delay [s]',
            'tooltip' => 'Set 0 to disable auto-close of popup message',
            'default' => 10,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('msg_position_popup')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'color',
            'name' => 'msg_success_color',
            'label' => 'Success message color',
            'tooltip' => 'Select custom color of success message'
        )); ?>

        <?php echo $this->_get_field(array(
            'type' => 'color',
            'name' => 'msg_error_color',
            'label' => 'Error message color',
            'tooltip' => 'Select custom color of error message'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'tooltips_validation',
            'label' => 'Show tooltips on validation error',
            'default' => 1,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No',
                    'is_parent' => true
                ),
                array(
                    'value' => 1,
                    'name' => 'Yes',
                    'is_parent' => true
                )
            )
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'reset_form',
            'label' => 'Reset form',
            'tooltip' => 'Reset all data filled in by User after email has been successfully sent. Success message will stay.',
            'default' => 1,
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'After successfully sent'
                ),
                array(
                    'value' => 2,
                    'name' => 'After closing successfully sent form'
                ),
                array(
                    'value' => 3,
                    'name' => 'With reset button',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'button_reset',
            'label' => 'Reset button label',
            'parent' => array('reset_form_3')
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_subject',
            'label' => 'Subject of email',
            'header' => 'Email subject',
            'tooltip' => '',
            'class' => 'pweb-input-large'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'email_subject_sfx',
            'label' => 'Email subject suffix',
            'tooltip' => '',
            'default' => 1,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'Site name'
                ),
                array(
                    'value' => 2,
                    'name' => 'Page title'
                )
            )
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'ticket_enable',
            'label' => 'Enable tickets',
            'header' => 'Tickets',
            'tooltip' => '',
            'default' => 0,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'Date-time',
                    'is_parent' => true
                ),
                array(
                    'value' => 2,
                    'name' => 'Number counter',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'ticket_format',
            'label' => 'Ticket format',
            'tooltip' => '',
            'parent' => array('ticket_enable_1', 'ticket_enable_2')
        )); ?>
        
        
        
        <?php 
        $host = $_SERVER['SERVER_NAME'];
        $isLocalhsot = ($host == 'localhost' OR $host == '127.0.0.1');
        $domain = str_replace('www.', '', $host);
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			$domain = $regs['domain'];
		}
        
        echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_from',
            'label' => 'Sender email',
            'header' => 'Email settings',
            'desc' => $isLocalhsot ? '' : sprintf(__('Sender email should be in the same domain as your website, example: %s'), 'info@'.$domain),
            'class' => 'pweb-filter-email',
            'default' => get_bloginfo('admin_email'),
            'required' => true
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_from_name',
            'label' => 'Sender name',
            'default' => get_bloginfo('name'),
            'required' => true
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_replyto',
            'label' => 'Reply to email',
            'tooltip' => 'Leave blank if you want User to reply to sender',
            'class' => 'pweb-filter-email'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_replyto_name',
            'label' => 'Reply to name',
            'tooltip' => 'Enter `Reply to name` if you have set `Reply to email`'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_bcc',
            'label' => 'BCC emails',
            'tooltip' => 'Add blind carbon copy recipients to the email. To add multiple recipients separate each email with , (coma). Do not add any email address which was already set in another field!',
            'class' => 'pweb-filter-emails pweb-input-large'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'server_sender',
            'label' => 'Send from one domain',
            'tooltip' => 'Send all emails from one domain. Administrator will receive email from address set in `Sender email` with reply to email address completed by the User. It is useful for some servers which do not allow to send emails from another domains.',
            'default' => 1,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'Yes (recomended)'
                )
            )
        )); ?>
        
        <?php 
        $php_mail_enabled = (function_exists('mail') AND is_callable('mail'));
        echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'mailer',
            'label' => 'Mailer type',
            'default' => $php_mail_enabled ? 'mail' : 'smtp',
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 'mail',
                    'name' => 'PHP mail function',
                    'disabled' => !$php_mail_enabled
                ),
                array(
                    'value' => 'smtp',
                    'name' => 'SMTP',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'smtp_username',
            'label' => 'SMTP Username',
            'desc' => $isLocalhsot ? '' : sprintf(__('Email account used for authentication should be in the same domain as your website, example: %s'), 'info@'.$domain),
            'parent' => 'mailer_smtp'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'password',
            'name' => 'smtp_password',
            'label' => 'SMTP Password',
            'parent' => 'mailer_smtp'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'smtp_host',
            'label' => 'SMTP Host',
            'desc' => $isLocalhsot ? '' : sprintf(__('Host used for SMTP should be in the same domain as your website, example: %s'), 'mail.'.$domain),
            'parent' => 'mailer_smtp'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'smtp_secure',
            'label' => 'SMTP Security',
            'tooltip' => 'Select the security model that your SMTP server uses.',
            'default' => 'none',
            'class' => 'pweb-radio-group',
            'parent' => 'mailer_smtp',
            'options' => array(
                array(
                    'value' => 'none',
                    'name' => 'None'
                ),
                array(
                    'value' => 'ssl',
                    'name' => 'SSL'
                ),
                array(
                    'value' => 'tls',
                    'name' => 'TLS'
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'smtp_port',
            'label' => 'SMTP Port',
            'tooltip' => '>Enter the port number of your SMTP server. Use 25 for most unsecured servers and 465 for most secure servers.',
            'default' => 25,
            'parent' => 'mailer_smtp',
            'class' => 'pweb-input-mini'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'redirect',
            'label' => 'Redirect after send',
            'header' => 'Redirect',
            'default' => 0,
            'class' => 'pweb-radio-group',
            'options' => array(
                array(
                    'value' => 0,
                    'name' => 'No'
                ),
                array(
                    'value' => 1,
                    'name' => 'Yes',
                    'is_parent' => true
                )
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'redirect_url',
            'label' => 'Redirect URL',
            'tooltip' => 'Enter URL for redirect to thank you page after successful email sent. Do not encode ampersands &amp;',
            'class' => 'pweb-filter-url pweb-input-xlarge',
            'parent' => array('redirect_1')
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'redirect_delay',
            'label' => 'Redirect delay [s]',
            'tooltip' => 'Enter delay time in seconds before redirect.',
            'default' => 5,
            'class' => 'pweb-filter-int pweb-input-mini',
            'parent' => array('redirect_1')
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'text_button',
            'name' => 'adwords_url',
            'label' => 'Google AdWords Conversion Tracker - image URL',
            'header' => 'Tracking',
            'tooltip' => 'Paste URL of image from generated tracking script or you can use <em>Paste</em> button to extract this link from conversion tracking script.',
            'button' => 'Paste',
            'class' => 'pweb-input-xlarge'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text_button',
            'name' => 'adcenter_url',
            'label' => 'Microsoft adCenter Conversion Tracker - MSTag iframe URL',
            'tooltip' => 'Paste URL of iframe from generated tracking script or you can use <em>Paste</em> button to extract this link from conversion tracking script.',
            'button' => 'Paste',
            'class' => 'pweb-input-xlarge'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'textarea',
            'name' => 'oncomplete',
            'label' => 'JavaScript on mail success event',
            'header' => 'JavaScript events',
            'tooltip' => 'JavaScript code called after successful send of email. This event has one argument `data` type of object with property `ticket`. Do not insert any HTML tags!',
            'class' => 'pweb-filter-javascript widefat',
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'textarea',
            'name' => 'onerror',
            'label' => 'JavaScript on mail error event',
            'tooltip' => 'JavaScript code called after mail send error or invalid captcha. This event has one argument `data` type of object with possible property `invalid`. Do not insert any HTML tags!',
            'class' => 'pweb-filter-javascript widefat',
            'attributes' => array(
                'rows' => 5,
                'cols' => 50
            )
        )); ?>
        
        
    </div>
</div>

<div id="pweb-dialog-email-load" title="<?php esc_attr_e( 'Load email template', 'pwebcontact' ); ?>" style="display:none">
    <p><?php _e( 'Current content of email message will be replaced with selected template!', 'pwebcontact' ); ?></p>
</div>
<div id="pweb-dialog-email-scheme-load" title="<?php esc_attr_e( 'Load email scheme', 'pwebcontact' ); ?>" style="display:none">
    <p><?php _e( 'Current content of message which will appear after successful sending and message which will be sent to User as copy will be replaced with selected scheme!', 'pwebcontact' ); ?></p>
</div>