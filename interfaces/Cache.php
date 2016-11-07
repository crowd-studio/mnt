<?php


/**
 * Singleton class
 *
 */
final class Cache
{
    /**
     * Cache instance
     * @var null
     */
	protected static $inst = null;

    /**
     * Full cache path
     * @var null
     */
    protected static $path = null;

    /**
     * Cache extension
     * @var string
     */
    protected static $ext = 'mntCache';

    /**
     * Instance 
     * @param string $path full cache path
     * @return  Cache instance
     */
    public static function Instance($path = false)
    {
        if (self::$inst === null) {
            self::$inst = new Cache();
            self::$path = $path;
        }

        return self::$inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __construct(){}

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __clone(){}

    /**
     * Stores data to specific cache file
     * @param  string $filename [description]
     * @param         $data     [description]
     */
    public function store($filename, $data){
        try {
            file_put_contents($this->_filePath($filename), $this->_encrypt($data));
        } catch (Exception $e) {
            // throw new Exception "An error occured while creating your directory";
        }
    }

    /**
     * retrieve data from cached file
     * @param  string $filename 
     * @return JSON           
     */
    public function retrieve($filename){
        $filename = $this->_filePath($filename);
        return (file_exists($filename)) ? $this->_decrypt(file_get_contents($filename)) : false;
    }

    /**
     * Remove specific file in cache folder
     * @param  string $filename File to be removed
     */
    public function remove($filename){
        unlink($this->_filePath($filename));
    }

    /**
     * Remove all files in cache folder
     * @param  string $key filter
     */
    public function removeAll($key = ''){
        $files = glob($this->path . $key . '*.*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    /**
     * PRIVATE parse the file path
     * @param  String $filename 
     * @return String          complete path
     */
    private function _filePath($filename){
        return $this->path . $filename . $this->ext;
    }

    /**
     * Encrypt cached data
     * @param  String $input    file content
     * @param  String $filename file name
     * @return base64           Encrypted data in base64 format
     */
    private function _encrypt ($input, $filename) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($filename), json_encode($input), MCRYPT_MODE_CBC, md5(md5($filename))));
    }
 
    /**
     * Decrypt cached data
     * @param  String $input    file content
     * @param  String $filename file name
     * @return JSON             Decrypted data in JSON format
     */
    private function _decrypt ($input, $filename) {
        $output = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($filename), base64_decode($input), MCRYPT_MODE_CBC, md5(md5($filename))), "\0");
        return json_decode($output, true);
    }
}