<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Singleton class
 *
 */
final class Trans
{
    /**
     * Trans class static instance
     * @var null
     */
	protected static $inst = null;

    /**
     * Array of trans strings
     * @var Array of strings
     */
    protected static $trans = [];

    /**
     * Navigation language
     * @var string
     */
    protected $lang = null;

    /**
     * Call this method to get singleton, instance the trans strings array
     * @param string $lang
     * @return Trans
     */
    public static function Instance($langs = false)
    {
        if (self::$inst === null) {
            self::$inst = new Trans();

            if(self::$trans === null){

                $yaml = new Parser();
                foreach ($langs as $lang) {
                    $path = __DIR__ . '/../app/content/' . $lang . '.yml';
                    try {
                        self::$trans[$lang] = Yaml::parse(file_get_contents($path));
                    } catch (ParseException $e) {
                        throw new \Exception('Unable to parse the YML file');
                    }
                }


                
            }
        }
        return self::$inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __construct()
    {

    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __clone()
    {

    }

    /**
     * set Navigation Language
     * @param String $lang
     */
    public function setLang($lang){
        $this->lang = $lang;
    }

    /**
     * get all trans strings
     * @return Array All trans strings
     */
    public function getTrans(){
        return $this->trans[$this->lang];
    }

    /**
     * Returns the provided key of the trans strings array 
     * @param  String $key 
     * @return String      
     */
    public function trans($key){
        return $this->trans[$this->lang][$key];
    }
}