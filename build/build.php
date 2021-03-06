<?php
/**
 * @version 2.0.0
 * @package Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

class PWebCompiler {

    protected $path = null;
    protected $zip = null;
    protected $zip_base_path = null;
    protected $is_pro = false;

    protected $exclude_regex = array(
        '/^\./',
        '/^media\/cache\/.+\.((?!html).)+$/i',
        '/^media\/css\/(background|button|form|toggler)/i'
    );
    protected $exclude = array(
        'media/images/admin/psd',
        'media/images/admin/bkp',
        'media/css/custom.css',
        'media/js/jquery.pwebcontact.js',
        'assets',
        'build'
    );
    protected $include_pro = array(
        'edd/EDDUpdateChecker.php',
        'edd/index.html'
    );
    protected $exclude_pro = array();

    protected $include_free = array(
        'media/css/themes/index.html',
        'media/css/themes/free.css'
    );
    protected $exclude_free = array(
        'captcha.php',
        'google.php',
        'media/css/themes/',
        'media/css/uploader',
        'media/fonts', // Toggler font
        'media/images/icons', // Toggler icons
        'media/images/themes',
        'media/jquery-ui', // jQuery UI Datapicker
        'media/js/jquery.fileupload',
        'media/js/jquery.iframe-transport.js', // fileupload
        'media/themes/dev',
        'media/tickets',
        'newsletter.php',
        'uploader.php',
        'UploadHandler.php',
        'vendor',
        'edd'
    );

    protected $filter = array(
        'readme.txt',
        'pwebcontact.php',
        'site.php',
        'admin.php',
        'tmpl/admin_edit.php',
        'tmpl/admin_edit_check.php',
        'tmpl/admin_edit_email.php',
        'tmpl/admin_edit_fields.php',
        'tmpl/admin_edit_theme.php',
        'tmpl/admin_list.php',
        'tmpl/admin_settings_advanced.php',
        'tmpl/default.php',
        'media/js/jquery.pwebcontact.min.js'
    );

    protected function filterFileContents( &$contents = null ) {

        if ($this->is_pro) {
            $contents = preg_replace('/([^\n\r]*<!-- FREE START -->).*?(<!-- FREE END -->[^\n]*\n)/s', '', $contents);
            $contents = preg_replace('/([^\n\r]*\/\*\*\* FREE START \*\*\*\/).*?(\/\*\*\* FREE END \*\*\*\/[^\n]*\n)/s', '', $contents);

            $contents = preg_replace('/\n[^\n\r]*<!-- PRO (START|END) -->.*/', '', $contents);
            $contents = preg_replace('/\n[^\n\r]*\/\*\*\* PRO (START|END) \*\*\*\/.*/', '', $contents);

            $contents = preg_replace('/Plugin Name: [^\n\r]+/i', '\\0 Pro', $contents);
        }
        else {
            $contents = preg_replace('/([^\n\r]*<!-- PRO START -->).*?(<!-- PRO END -->[^\n]*\n)/s', '', $contents);
            $contents = preg_replace('/([^\n\r]*\/\*\*\* PRO START \*\*\*\/).*?(\/\*\*\* PRO END \*\*\*\/[^\n]*\n)/s', '', $contents);

            $contents = preg_replace('/\n[^\n\r]*<!-- FREE (START|END) -->.*/', '', $contents);
            $contents = preg_replace('/\n[^\n\r]*\/\*\*\* FREE (START|END) \*\*\*\/.*/', '', $contents);
        }
    }

    protected function addFilesToZip($directory = null) {

        if ($directory) {
            $directory = ltrim($directory, '/');
        }

        $iterator = new DirectoryIterator( $this->path . $directory );

        foreach ( $iterator as $info ) {

            if (!$info->isDot()) {

                $filename = $info->getFilename();

                foreach ($this->exclude_regex as $regex) {
                    if (preg_match($regex, $directory . $filename)) {
                        continue 2;
                    }
                }
                foreach ($this->exclude as $exclude) {
                    if (strpos($directory . $filename, $exclude) === 0) {
                        continue 2;
                    }
                }

                if ($this->is_pro === false) {
                    foreach ($this->exclude_free as $exclude) {
                        if (strpos($directory . $filename, $exclude) === 0) {
                            foreach ($this->include_free as $include) {
                                if (strpos($directory . $filename, $include) === 0) {
                                    break 2;
                                }
                            }
                            continue 2;
                        }
                    }
                }
                else {
                    foreach ($this->exclude_pro as $exclude) {
                        if (strpos($directory . $filename, $exclude) === 0) {
                            foreach ($this->include_pro as $include) {
                                if (strpos($directory . $filename, $include) === 0) {
                                    break 2;
                                }
                            }
                            continue 2;
                        }
                    }
                }

                if ($info->isFile()) {
                    if (in_array($directory . $filename, $this->filter)) {
                        $contents = file_get_contents( $this->path . $directory . $filename );
                        $this->filterFileContents( $contents );
                        $this->zip->addFromString( $this->zip_base_path . $directory . $filename, $contents );
                    }
                    else {
                        $this->zip->addFile( $this->path . $directory . $filename,  $this->zip_base_path . $directory . $filename );
                    }
                }
                elseif ($info->isDir()) {
                    $this->zip->addEmptyDir( $this->zip_base_path . $directory . $filename );
                    $this->addFilesToZip( $directory . $filename . '/' );
                }
            }
        }
    }

    protected function getVersion() {

        $contents = file_get_contents($this->path.'pwebcontact.php');

        if (preg_match('/Version: (\d+(\.\d+)*)+/i', $contents, $match)) {
            return $match[1];
        }

        return '2.0.0';
    }

    public function build() {

        $options = getopt('', array('pro', 'ver'));

        $this->path = dirname(__DIR__).'/';

        $version = $this->getVersion();
        $this->is_pro = isset($options['pro']);

        if (isset($options['ver'])) {
            echo $version;
            die;
        }

        $zip_path = sprintf(
            '%s/build/%s/GatorForms%s-v%s.zip',
            $this->path,
            $this->is_pro ? 'pro' : 'free',
            $this->is_pro ? 'Pro' : '',
            $version
        );

        if (is_file($zip_path)) {
            unlink($zip_path);
        }

        $this->zip = new ZipArchive;
		if (true === $this->zip->open($zip_path, ZipArchive::CREATE)) {

            $this->zip_base_path = 'pwebcontact/';
            $this->zip->addEmptyDir( $this->zip_base_path );

            $this->addFilesToZip();
            $this->zip->close();

            echo "OK\r\n";
		}
        else {
			echo $this->zip->getStatusString()."\r\n";
		}
    }
}

$complier = new PWebCompiler;
$complier->build();
