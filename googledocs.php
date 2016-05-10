<?php

/**
 * @version   2.2.0
 * @package   Perfect Easy & Powerful Contact Form
 * @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license   GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author    Mateusz Podraza, based on Never Settle distributed on GNU/GPL
 */
// no direct access
function_exists('add_action') or die;

//TODO: Implement an autoloader
define('GOOGLE_ROOT', dirname(__FILE__) . '/vendor/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet');

require_once dirname(__FILE__) . '/vendor/php-google-oauth/Google_Client.php';

require_once GOOGLE_ROOT . '/ServiceRequestInterface.php';
require_once GOOGLE_ROOT . '/DefaultServiceRequest.php';
require_once GOOGLE_ROOT . '/ServiceRequestFactory.php';
require_once GOOGLE_ROOT . '/SpreadsheetService.php';
require_once GOOGLE_ROOT . '/SpreadsheetFeed.php';
require_once GOOGLE_ROOT . '/Spreadsheet.php';
require_once GOOGLE_ROOT . '/Util.php';
require_once GOOGLE_ROOT . '/WorksheetFeed.php';
require_once GOOGLE_ROOT . '/Worksheet.php';
require_once GOOGLE_ROOT . '/ListFeed.php';
require_once GOOGLE_ROOT . '/Exception.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class PWebGoogleConnector
{
    /*
     * The following constants were generated at Google's Developer Console
     */

    const clientID     = '300313169789-tjp5sq43n8shqra9okb1j06ovlsrh0b6.apps.googleusercontent.com';
    const clientSecret = 'M-RH9S2zGtGv12PdCzQ-LKJs';
    const redirect     = 'urn:ietf:wg:oauth:2.0:oob';
    const optionName = 'pwebcontact_googledocs_token';

    protected $token;
    protected $spreadsheet;
    protected $worksheet;
    protected $settings;
    protected $token_data = array();

    public function __construct($spreadsheet = '', $worksheet = '')
    {
        $this->settings    = PWebContact::getSettings();
        $this->spreadsheet = $spreadsheet;
        $this->worksheet   = $worksheet;
        $this->token_data  = json_decode(get_option(self::optionName), true);
    }

    public function setAccessCode($access_code)
    {
        if (empty($access_code) || $access_code == $this->settings->get('googledocs_accesscode', ''))
		{
			return; //No change, Google will response with null...
		}
        $client    = new Google_Client();
        $client->setClientId(self::clientID);
        $client->setClientSecret(self::clientSecret);
        $client->setRedirectUri(self::redirect);
        $client->setScopes(array('https://spreadsheets.google.com/feeds'));
        $client->authenticate($access_code);
        $tokenData = json_decode($client->getAccessToken(), true);
        $this->updateToken($tokenData);
    }

    public function updateToken($tokenData)
    {
        if (empty($tokenData))
        {
            return;
        }

        $tokenData['expire'] = time() + intval($tokenData['expires_in']);

        $tokenJson        = json_encode($tokenData);
        update_option(self::optionName, $tokenJson);
        $this->token_data = $tokenData;
    }

    public function auth()
    {
        $tokenData = $this->token_data;
        if (!is_array($tokenData) || empty($tokenData))
        {
            return; //invalid data
        }
        if (time() > $tokenData['expire'])
        {
            $client    = new Google_Client();
            $client->setClientId(self::clientID);
            $client->setClientSecret(self::clientSecret);
            $client->refreshToken($tokenData['refresh_token']);
            $tokenData = array_merge($tokenData, json_decode($client->getAccessToken(), true));
            $this->updateToken($tokenData);
        }
        $serviceRequest = new DefaultServiceRequest($tokenData['access_token']);
        ServiceRequestFactory::setInstance($serviceRequest);
    }

    public function setSpreadsheetName($spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function setWorksheetName($worksheet)
    {
        $this->worksheet = $worksheet;
    }

    protected function getListFeed()
    {
        try
        {
            $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
            $spreadsheetFeed    = $spreadsheetService->getSpreadsheets();
            if (is_null($spreadsheetFeed))
            {
                return false; //failed to get access to the drive
            }
            $spreadsheet = $spreadsheetFeed->getByTitle($this->spreadsheet);

            if (is_null($spreadsheet))
            {
                return false; //failed to get spreadsheet
            }
            $worksheetFeed = $spreadsheet->getWorksheets();

            $worksheet = $worksheetFeed->getByTitle($this->worksheet);

            if (is_null($worksheet))
            {
                return false; //failed to get the worksheet
            }

            return $worksheet->getListFeed();
        }
        catch (Exception $e)
        {
            //TODO: Better exception handling
            //die("getListFailed() with: " . $e->getMessage());
            return false;
        }
    }

    //choosing the worksheet
    public function add_row($data)
    {
        $listFeed = $this->getListFeed();
        if ($listFeed === false)
        {
            return; //couldn't get the feed :c
        }
        $listFeed->insert($data);
    }

}

class PWebGoogleDocsPlg
{

    /**
     * Process the event fired in site.php
     *
     * Fired by:
     * do_action('pwebcontact_data', array('data' => $data, 'email_vars' => $email_vars));
     *
     * @param $args array array of arguments
     */
    public static function onFormDataEvent($args)
    {
        $settings = PWebContact::getSettings();
        $params   = PWebContact::getParams($args['form_id']);

        //Check if Google Docs Integration is enabled for this form - if not, exit
        if (!$params->get('googledocs_enable', 0))
        {
            return;
        }
        //Check if we have Google Access Code set - if not, exit
        if (!$settings->get('googledocs_accesscode'))
        {
            return;
        }

        //Prepare data
        $email_vars = $args['email_vars'];
        $data       = $args['data'];

        $gData = array(
            'sent-on' => $email_vars['sent_on'],
            'ticket' => $email_vars['ticket']
        );

        foreach ($data['fields'] as $key => $value)
        {
            if (is_array($value))
                $value                                       = implode(', ', $value);
            $gData['field-' . str_replace('_', '-', $key)] = $value;
        }

        $gData['ip-address']        = $data['ip_address'];
        $gData['browser']           = $data['browser'];
        $gData['os']                = $data['os'];
        $gData['screen-resolution'] = $data['screen_resolution'];
        $gData['title']             = $data['title'];
        $gData['url']               = $data['url'];
        $gData['attachments']       = '';

        if (count($data['attachments']))
        {
            if ($params->get('attachment_type', 1) == 2 OR ! $params->get('attachment_delete', 1))
            {
                $files      = array();
                $upload_url = $params->get('upload_url');
                foreach ($data['attachments'] as $file)
                    $files[]    = $upload_url . rawurlencode($file);

                $gData['attachments'] = implode(' , ', $files);
            }
            else
            {
                $gData['attachments'] = implode(', ', $data['attachments']);
            }
        }

        //Connect to the spreadsheet and insert our data in a new row
        $bridge = new PWebGoogleConnector($params->get('googledocs_sheetname'), $params->get('googledocs_worksheetname'));
        $bridge->auth();
        $bridge->add_row($gData);
    }

    public static function onSettingsChangeEvent($args)
    {
        $settings = $args['settings'];
        if (!empty($settings['googledocs_accesscode']))
        {
            //Let's get a request token :)
            $bridge = new PWebGoogleConnector();
            $bridge->setAccessCode($settings['googledocs_accesscode']);
        }
    }

}

add_action('pwebcontact_data', 'PWebGoogleDocsPlg::onFormDataEvent');
add_action('pwebcontact_settingschange', 'PWebGoogleDocsPlg::onSettingsChangeEvent');
