<?php
/**
 * @version 2.1.1
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2015 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Mateusz Podraza
 */
class PWebGoogleConnector{

    /*
     * The following constants were generated at Google's Developer Console
     */
    const clientID = '300313169789-tjp5sq43n8shqra9okb1j06ovlsrh0b6.apps.googleusercontent.com';
    const clientSecret = 'VpEeq_HdTIdO8O5kbUOlnYsi';
    const redirect = '';
}
class PWebGoogleDocsPlg{

    /**
     * Process the event fired in site.php
     *
     * Fired by:
     * do_action('pwebcontact_data', array('data' => $data, 'email_vars' => $email_vars));
     *
     * @param $data
     */
    public static function onFormDataEvent($data){
        //TODO: Process the data
    }
}
add_action('pwebcontact_data', 'PWebGoogleDocsPlg::onFormDataEvent');