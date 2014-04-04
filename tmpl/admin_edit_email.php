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

<?php echo $this->_get_field(array(
    'type' => 'text',
    'name' => 'email_to',
    'label' => 'Enter one or more emails to which message should be sent to',
    'tooltip' => 'To add multiple recipients separate each email with , (coma).',
    'class' => 'pweb-filter-emails pweb-input-large'
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'wp_user',
    'name' => 'email_user',
    'label' => 'or choose WordPress Administrator to whom message will be sent to',
    'tooltip' => 'Enable this option to send email to selected WordPress Administrator. Do not use this option if Administrator has the same email address as in above field!'
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'email_admin',
    'label' => 'Email to Administrator',
    'tooltip' => '',
    'default' => 1,
    'class' => 'pweb-radio-group',
    'options' => array(
        array(
            'value' => 1,
            'name' => 'HTML format',
            'is_parent' => true
        ),
        array(
            'value' => 2,
            'name' => 'Custom text format',
            'is_parent' => true
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'filelist',
    'name' => 'email_tmpl_html_user',
    'label' => 'Administrator HTML email',
    'tooltip' => '',
    'default' => 'default',
    'filter' => '\.html$',
    'directory' => 'media/email_tmpl',
    'strip_ext' => true,
    'parent' => array('email_admin_1')
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'textarea',
    'name' => 'email_tmpl_text_user',
    'label' => 'Administrator text email',
    'tooltip' => '',
    'class' => 'widefat',
    'parent' => array('email_admin_2'),
    'attributes' => array(
        'rows' => 5,
        'cols' => 50
    )
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'email_copy',
    'label' => 'Send copy to User',
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
            'name' => 'Yes, HTML format',
            'is_parent' => true
        ),
        array(
            'value' => 2,
            'name' => 'Yes, custom text format',
            'is_parent' => true
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'filelist',
    'name' => 'email_tmpl_html_user',
    'label' => 'User HTML email',
    'tooltip' => '',
    'default' => 'default',
    'filter' => '\.html$',
    'directory' => 'media/email_tmpl',
    'strip_ext' => true,
    'parent' => array('email_copy_1')
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'textarea',
    'name' => 'email_tmpl_text_user',
    'label' => 'User text email',
    'tooltip' => '',
    'class' => 'widefat',
    'parent' => array('email_copy_2'),
    'attributes' => array(
        'rows' => 5,
        'cols' => 50
    )
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'email_autoreply',
    'label' => 'Send auto-reply',
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
            'name' => 'Yes, HTML format',
            'is_parent' => true
        ),
        array(
            'value' => 2,
            'name' => 'Yes, custom text format',
            'is_parent' => true
        )
    )
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'filelist',
    'name' => 'email_tmpl_html_autoreply',
    'label' => 'Auto-reply HTML email',
    'tooltip' => '',
    'default' => 'auto-reply',
    'filter' => '\.html$',
    'directory' => 'media/email_tmpl',
    'strip_ext' => true,
    'parent' => array('email_autoreply_1')
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'textarea',
    'name' => 'email_tmpl_text_autoreply',
    'label' => 'Auto-reply text email',
    'tooltip' => '',
    'class' => 'widefat',
    'parent' => array('email_autoreply_2'),
    'attributes' => array(
        'rows' => 5,
        'cols' => 50
    )
)); ?>

<div class="pweb-advanced-options">
    <a href="#" class="pweb-advanced-options-toggler">
        <?php _e( 'Advanced', 'pwebcontact' ); ?><i class="dashicons dashicons-arrow-down"></i>
    </a>
    <div class="pweb-advanced-options-content">
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_subject',
            'label' => 'Custom subject of email',
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
                ),
                array(
                    'value' => 3,
                    'name' => 'Form field'
                )
            )
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'ticket_enable',
            'label' => 'Enable tickets',
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
        
        
    </div>
</div>