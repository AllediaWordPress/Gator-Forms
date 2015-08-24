<?php
/**
 * @version 2.1.1
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2015 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Mateusz Podraza, based on Never Settle distributed on GNU/GPL
 */
//TODO: Implement an autoloader
require_once plugin_dir_path(__FILE__) . 'lib/php-google-oauth/Google_Client.php';
//Well.. Fuck.
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/ServiceRequestInterface.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/DefaultServiceRequest.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/ServiceRequestFactory.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/SpreadsheetService.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/SpreadsheetFeed.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/Spreadsheet.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/Util.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/WorksheetFeed.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/Worksheet.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/ListFeed.php';
require_once plugin_dir_path(__FILE__) . 'lib/asimlqt/php-google-spreadsheet-client/src/Google/Spreadsheet/Exception.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class PWebGoogleConnector
{

    /*
     * The following constants were generated at Google's Developer Console
     */
    const clientID = '300313169789-tjp5sq43n8shqra9okb1j06ovlsrh0b6.apps.googleusercontent.com';
    const clientSecret = 'M-RH9S2zGtGv12PdCzQ-LKJs';
    const redirect = 'urn:ietf:wg:oauth:2.0:oob';

    const optionName = 'pwebcontact_googledocs_token';

    protected $token;
    protected $spreadsheet;
    protected $worksheet;
    protected $settings;
    protected $token_data = array();

    public function __construct()
    {
        $this->settings = PWebContact::getSettings();

        $this->token_data = json_decode(get_option(self::optionName), true);

    }

    public function setAccessCode($access_code)
    {
        $client = new Google_Client();
        $client->setClientId(self::clientID);
        $client->setClientSecret(self::clientSecret);
        $client->setRedirectUri(self::redirect);
        $client->setScopes(array('https://spreadsheets.google.com/feeds'));
        try {
            $results = $client->authenticate($access_code);
        } catch (Exception $e) {

        }
        $tokenData = json_decode($client->getAccessToken(), true);
        $this->updateToken($tokenData);
    }

    public function updateToken($tokenData)
    {
        $tokenData['expire'] = time() + intval($tokenData['expires_in']);
        try {
            $tokenJson = json_encode($tokenData);
            update_option(self::optionName, $tokenJson);
            $this->token_data = $tokenData;
        } catch (Exception $e) {
            //TODO: Better exception handling
            die("updateToken() failed with: " . $e->getMessage());
        }
    }

    public function auth()
    {
        $tokenData = $this->token_data;

        if (time() > $tokenData['expire']) {
            $client = new Google_Client();
            $client->setClientId(self::clientID);
            $client->setClientSecret(self::clientSecret);
            $client->refreshToken($tokenData['refresh_token']);
            $tokenData = array_merge($tokenData, json_decode($client->getAccessToken(), true));
            $this->updateToken($tokenData);
        }
        $serviceRequest = new DefaultServiceRequest($tokenData['access_token']);
        ServiceRequestFactory::setInstance($serviceRequest);
    }

    //preg_match is a key of error handle in this case
    public function settitleSpreadsheet($title)
    {
        $this->spreadsheet = $title;
    }

    //finished setting the title
    public function settitleWorksheet($title)
    {
        $this->worksheet = $title;
    }

    protected function getListFeed(){
        try{
            $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
            $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
            if(is_null($spreadsheetFeed)){
               return false; //failed to get access to the drive
            }
            $spreadsheet = $spreadsheetFeed->getByTitle($this->spreadsheet);

            if(is_null($spreadsheet)){
                return false; //failed to get spreadsheet
            }
            $worksheetFeed = $spreadsheet->getWorksheets();

            $worksheet = $worksheetFeed->getByTitle($this->worksheet);

            if(is_null($worksheet)){
                return false; //failed to get the worksheet
            }
            return $worksheet->getListFeed();

        }
        catch(Exception $e) {
            //TODO: Better exception handling
            //die("getListFailed() with: " . $e->getMessage());
            return false;
        }
    }

    //choosing the worksheet
    public function add_row($data)
    {
        $listFeed = $this->getListFeed();
        if($listFeed === false){
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
        //Check if Google Docs Integration is enabled for this form - if not, exit
        if (!isset($args['data']['googledocs_enable']) || true !== $args['data']['googledocs_enable']) {
            return;
        }
        //Check if we have Google Access Code set - if not, exit
        if (empty($args['data']['googledocs_accesscode'])) {
            return;
        }
        //Same for the sheet name
        if (empty($args['data']['googledocs_sheetname'])) {
            return;
        }
        //And the worksheet
        if (empty($args['data']['googledocs_worksheetname'])) {
            return;
        }

        $bridge = new PWebGoogleConnector();
        $bridge->auth();
        $bridge->settitleSpreadsheet($args['data']['googledocs_sheetname']);
        $bridge->settitleWorksheet($args['data']['googledocs_worksheetname']);
        $bridge->add_row($args['data']['fields']);
    }

    public static function onSettingsChangeEvent($args)
    {
        $settings = $args['settings'];
        if (!empty($settings['googledocs_accesscode'])) {
            //Let's get a request token :)
            $bridge = new PWebGoogleConnector();
            $bridge->setAccessCode($settings['googledocs_accesscode']);
        }
    }
}

add_action('pwebcontact_data', 'PWebGoogleDocsPlg::onFormDataEvent');
add_action('pwebcontact_settingschange', 'PWebGoogleDocsPlg::onSettingsChangeEvent');