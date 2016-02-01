<?php

/**
 * @version 2.1.4
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */
// no direct access
function_exists('add_action') or die;

class PWebContact_Captcha
{

    private $public_key = '6LcPnAoTAAAAAELHJ46n697mYqOKzC_kyvhmmu5s';
    private $private_key = '6LcPnAoTAAAAAO9ZxguoPNVGHwa-62AazCFVcJQe';
    private $options = array('theme' => 'light');

    public function __construct($options = null)
    {
        if (is_array($options))
        {
            $this->options = array_merge($this->options, $options);
        }
    }

    public function display($id, $class = '')
    {
        $recaptchaToken = new PWebContact_ReCaptchaSecureToken(array(
            'site_key' => $this->public_key,
            'site_secret' => $this->private_key));
        $secureToken = $recaptchaToken->secureToken(session_id());

        return '<div id="' . $id . '" class="' . $class . '" style="min-height:78px;min-width:304px"></div>'
                . '<script type="text/javascript">'
                . 'jQuery(document).ready(function($){'
                . '$(window).load(function(){'
                // Get captcha widget ID
                . 'var grecaptchaId='
                // Render captcha
                . 'grecaptcha.render("' . $id . '",{'
                . 'sitekey:"' . $this->public_key . '"'
                . (strpos($_SERVER['HTTP_HOST'], '127.0.') === 0 || $_SERVER['HTTP_HOST'] == 'localhost' ? '' : ',stoken:"' . $secureToken . '"')
                . ',theme:"' . $this->options['theme'] . '"'
                . ($this->options['form_id'] ? ',"expired-callback":function(){pwebContact' . $this->options['form_id'] . '.captchaExpired()}' : '')
                . '});'
                // Store captcha widget ID
                . '$("#pwebcontact' . $this->options['form_id'] . '_captcha").data("grecaptchaId",grecaptchaId)'
                . '})'
                . '});'
                . '</script>';
    }

    public function checkAnswer()
    {
        $remoteip = PWebContact::detectIP();
        $answer = isset($_POST['g-recaptcha-response']) ? (string) $_POST['g-recaptcha-response'] : null;

        // Discard spam submissions
        if (empty($remoteip) || empty($answer))
        {
            return false;
        }

        $http = new WP_Http;
        $response = $http->post('https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret' => $this->private_key,
                'response' => $answer,
                'remoteip' => $remoteip
            ),
            'timeout' => 5,
            'sslverify' => false)
        );

        if (is_wp_error($response))
        {
            throw new Exception($response);
        }
        elseif ($response['response']['code'] >= 200 AND $response['response']['code'] < 400)
        {
            $result = json_decode($response['body'], true);
            if (isset($result['success']) && $result['success'] == true)
            {
                return true;
            }
            elseif (isset($result['error-codes']) && $result['error-codes'])
            {
                throw new Exception(implode(', ', $result['error-codes']));
            }
        }

        return false;
    }

}

/**
 * @author josh
 * @url https://github.com/slushie/recaptcha-secure-token
 */
class PWebContact_ReCaptchaSecureToken
{

    protected $site_key;
    protected $site_secret;

    public function __construct($config = array())
    {
        if (isset($config['site_key']))
            $this->site_key = $config['site_key'];

        if (isset($config['site_secret']))
            $this->site_secret = $config['site_secret'];
    }

    /**
     * @return string
     */
    public function getSiteKey()
    {
        return $this->site_key;
    }

    /**
     * @param string $site_key
     */
    public function setSiteKey($site_key)
    {
        $this->site_key = $site_key;
    }

    /**
     * @param string $site_secret
     */
    public function setSiteSecret($site_secret)
    {
        $this->site_secret = $site_secret;
    }

    /**
     * Create an encrypted secure token for the given session id.
     *
     * @see https://developers.google.com/recaptcha/docs/secure_token
     *
     * @param string $session_id a unique session identifier.
     * @param int|null $timestamp in milliseconds, defaults to current time.
     * @return string Recaptcha-compatible base64 encoded encrypted binary data.
     */
    public function secureToken($session_id, $timestamp = null)
    {
        if (is_null($timestamp))
            $timestamp = $this->currentTimestamp();

        $params = array('session_id' => $session_id, 'ts_ms' => $timestamp);
        $plaintext = json_encode($params);

        $encrypted = $this->encryptData($plaintext);

        return $this->base64Encode($encrypted);
    }

    /**
     * Decode and decrypt a secure token generated using this algorithm.
     *
     * @param string $secure_token base64 encoded secure token
     * @return array includes the keys 'session_id' and 'ts_ms'
     */
    public function decodeSecureToken($secure_token)
    {
        $binary = $this->base64Decode($secure_token);

        $decrypted = $this->decryptData($binary);

        return json_decode($decrypted);
    }

    /**
     * Encrypt an arbitrary string using the site secret.
     *
     * @param string $plaintext
     * @return string binary data
     */
    public function encryptData($plaintext)
    {
        $padded = $this->pad($plaintext, 16);

        $site_secret = $this->secretKey();

        return $this->encryptAes($padded, $site_secret);
    }

    /**
     * Decrypt the given data using the site secret.
     *
     * @param string $encrypted binary data
     * @return string plaintext string
     */
    public function decryptData($encrypted)
    {
        $site_secret = $this->secretKey();

        $padded = $this->decryptAes($encrypted, $site_secret);

        return $this->stripPadding($padded);
    }

    /**
     * Get the current timestamp in milliseconds.
     *
     * @return int
     */
    protected function currentTimestamp()
    {
        return round(microtime(true) * 1000);
    }

    /**
     * Returns the site secret in the key format required for encryption.
     *
     * @return string
     */
    protected function secretKey()
    {
        if (!isset($this->site_secret))
            throw new Exception('Missing site_secret');

        $secret_hash = hash('sha1', $this->site_secret, true);
        return substr($secret_hash, 0, 16);
    }

    /**
     * Encrypts the given input string using the provided key.
     *
     * Note that the algorithm, block mode, and key format
     * are defined by ReCaptcha code linked below.
     *
     * @see https://github.com/google/recaptcha-java/blob/master/appengine/src/main/java/com/google/recaptcha/STokenUtils.java
     *
     * @param $input
     * @param $secret
     *
     * @return string
     */
    protected function encryptAes($input, $secret)
    {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $secret, $input, MCRYPT_MODE_ECB);
    }

    /**
     * @param $input
     * @param $secret
     *
     * @return string
     */
    protected function decryptAes($input, $secret)
    {
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $secret, $input, MCRYPT_MODE_ECB);
    }

    /**
     * Pad the input string to a multiple of {$block_size}. The
     * padding algorithm is defined in the PKCS#5 and PKCS#7 standards
     * (which differ only in block size). See RFC 5652 Sec 6.3 for
     * implementation details.
     *
     * NB: the Java implementation of the ReCaptcha encryption algorithm
     * uses a block size of 16, despite being named PKCS#5. This is
     * consistent with the AES 128-bit cipher.
     *
     * @param string $input
     * @param int $block_size
     * @return string
     */
    protected function pad($input, $block_size = 16)
    {
        $pad = $block_size - (strlen($input) % $block_size);
        return $input . str_repeat(chr($pad), $pad);
    }

    /**
     * Naively strip padding from an input string.
     *
     * @param string $input padded input string.
     * @return string
     */
    protected function stripPadding($input)
    {
        $padding_length = ord(substr($input, -1));
        return substr($input, 0, strlen($input) - $padding_length);
    }

    /**
     * Generate an "URL-safe" base64 encoded string from the
     * given input data.
     *
     * @param string $input
     * @return string
     */
    protected function base64Encode($input)
    {
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($input));
    }

    /**
     * Decode an "URL-safe" base64 encoded string.
     *
     * @param string $input
     * @return string
     */
    protected function base64Decode($input)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $input));
    }

}
