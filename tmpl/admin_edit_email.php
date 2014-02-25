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
    'class' => 'pweb-filter-emails'
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'wp_user',
    'name' => 'email_user',
    'label' => 'Choose Admin to whom message will be sent to'
)); ?>



<?php echo $this->_get_field(array(
    'type' => 'radio',
    'name' => 'email_admin',
    'label' => 'Email to Admin',
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
    'label' => 'Admin HTML email',
    'default' => 'default',
    'filter' => '\.html$',
    'directory' => 'media/email_tmpl',
    'strip_ext' => true,
    'parent' => array('email_admin_1')
)); ?>

<?php echo $this->_get_field(array(
    'type' => 'textarea',
    'name' => 'email_tmpl_text_user',
    'label' => 'Admin text email',
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
            'type' => 'textarea',
            'name' => 'email_to_list',
            'label' => 'Email to list',
            'class' => 'pweb-filter-email',
            'attributes' => array(
                'rows' => 5,
                'cols' => 30
            )
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_from',
            'label' => 'Sender email',
            'class' => 'pweb-filter-email'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_replyto',
            'label' => 'Reply to email',
            'class' => 'pweb-filter-email'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_replyto_name',
            'label' => 'Reply to name'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_bcc',
            'label' => 'BCC emails',
            'class' => 'pweb-filter-emails'
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'text',
            'name' => 'email_subject',
            'label' => 'Custom subject of email'
        )); ?>
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'email_subject_sfx',
            'label' => 'Email subject suffix',
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
            'parent' => array('ticket_enable_1', 'ticket_enable_2')
        )); ?>
        
        
        
        <?php echo $this->_get_field(array(
            'type' => 'radio',
            'name' => 'server_sender',
            'label' => 'Send from one domain',
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
    </div>
</div>