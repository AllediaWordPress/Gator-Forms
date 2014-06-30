<?php
/**
 * @version 1.0.0
 * @package Perfect Easy & Powerful Contact Form
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 */

class PWebCompiler {

    protected $path = null;
    protected $zip = null;
    protected $is_pro = false;
    protected $excludeRegExps = array(
        '/^\./',
        '/^media\/cache\/.+\.((?!html).)+$/i',
        '/^media\/js\/jquery\.pwebcontact\.js$/i',
        '/^assets/i',
        '/^build/i'
    );
    protected $excludeFreeFiles = array(
        'uploader.php',
        'UploadHandler.php',
        'media/css/animations.css', // Modal window animations
        'media/css/uploader.css',
        'media/css/uploader-rtl.css',
        'media/jquery-ui', // jQuery UI Datapicker
        'media/js/jquery.cookie.js', // Auto-open counter
        'media/js/jquery.cookie.min.js', // Auto-open counter
        'media/js/jquery.fileupload.js',
        'media/js/jquery.fileupload.min.js',
        'media/js/jquery.fileupload-process.js',
        'media/js/jquery.fileupload-ui.js',
        'media/js/jquery.fileupload-validate.js',
        'media/js/jquery.iframe-transport.js', // fileupload
        'media/tickets'
    );
    protected $filterFiles = array(
        'pwebcontact.php',
        'tmpl/default.php'
    );
    
    protected function filterFileContents( &$contents = null ) {
        
        if ($this->is_pro) {
            $contents = preg_replace('/([^\n]*<!-- FREE START -->).*?(<!-- FREE END -->[^\n]*\n)/s', '', $contents);
            $contents = preg_replace('/([^\n]*\/\*\*\* FREE START \*\*\*\/).*?(\/\*\*\* FREE END \*\*\*\/[^\n]*\n)/s', '', $contents);
            
            $contents = preg_replace('/\n[^\n]*<!-- PRO (START|END) -->.*/', '', $contents);
            $contents = preg_replace('/\n[^\n]*\/\*\*\* PRO (START|END) \*\*\*\/.*/', '', $contents);
        }
        else {
            $contents = preg_replace('/([^\n]*<!-- PRO START -->).*?(<!-- PRO END -->[^\n]*\n)/s', '', $contents);
            $contents = preg_replace('/([^\n]*\/\*\*\* PRO START \*\*\*\/).*?(\/\*\*\* PRO END \*\*\*\/[^\n]*\n)/s', '', $contents);
            
            $contents = preg_replace('/\n[^\n]*<!-- FREE (START|END) -->.*/', '', $contents);
            $contents = preg_replace('/\n[^\n]*\/\*\*\* FREE (START|END) \*\*\*\/.*/', '', $contents);
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
                
                foreach ($this->$excludeRegExps as $regexp) {
                    if (preg_match($regexp, $directory . $filename)) {
                        continue 2;
                    }
                }
                
                if (in_array($directory . $filename, $this->excludeFreeFiles)) {
                    continue;
                }
                
                if ($info->isFile()) {
                    if (in_array($directory . $filename, $this->filterFiles)) {
                        $contents = file_get_contents( $this->path . $directory . $filename );
                        $this->filterFileContents( $contents );
                        $this->zip->addFromString( $directory . $filename, $contents );
                    }
                    else {
                        $this->zip->addFile( $this->path . $directory . $filename,  $directory . $filename );
                    }
                }
                elseif ($info->isDir()) {
                    $this->zip->addEmptyDir( $directory . $filename );
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
        
        return '1.0.0';
    }
    
    public function build() {
        
        $options = getopt('', array('pro'));
        
        $this->path = dirname(__DIR__).'/';
        
        $version = $this->getVersion();
        $this->is_pro = isset($options['pro']);
        
        $zip_path = $this->path . 'build/wp_pwebcontact_'.$version.'_'.($this->is_pro ? 'pro' : 'free').'.zip';
		
        if (is_file($zip_path)) {
            unlink($zip_path);
        }
        
        $this->zip = new ZipArchive;
		if (true === $this->zip->open($zip_path, ZipArchive::CREATE)) {
            
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