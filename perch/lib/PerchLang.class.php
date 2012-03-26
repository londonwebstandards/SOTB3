<?php

class PerchLang 
{
    static protected $instance;
    public $lang_dir;
    public $lang_file;
    public $lang;
    
    private $translations = false;
    private $to_add = array();
    
    static protected $test;
    
    function __construct()
    {
        $Settings = PerchSettings::fetch();
        if (defined('PERCH_DB_DATABASE')) {
            $this->lang     = $Settings->get('lang')->settingValue();
        }else{
            $this->lang     = 'en-gb';
        }
        
        $this->lang_dir = PERCH_PATH . DIRECTORY_SEPARATOR . 'lang';
        $this->lang_file = $this->lang_dir . DIRECTORY_SEPARATOR . $this->lang . '.txt';
    }
    
    function __destruct()
    {
        if (PerchUtil::count($this->to_add)) {
            $this->write_to_lang_file($this->to_add);
        }
    }
    
    public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}
    
    /**
     * Get translated string. First param is the string. Second can be an array of printf 
     * args, or will accept any number of params as printf args.
     *
     * @param string $string 
     * @param string $values 
     * @return void
     * @author Drew McLellan
     */
    public static function get($string, $values=false)
    {
        $Lang = PerchLang::fetch();
        $string = $Lang->get_translated_string($string);
        
        if (func_num_args()>1) {
            if (is_array($values)) {
                $string = vsprintf($string, $values);
            }else{
                $args = func_get_args();
                array_shift($args);
                $string = vsprintf($string, $args);
            }
        }
        
        return $string;
    }

    public function get_translated_string($string)
    {
        if (!$this->translations) {
            $this->load_translations();
        }
        
        if (isset($this->translations[$string])) {
            return $this->translations[$string];
        }else{
            $this->add_translation($string);
        }
        return $string;
    }

    
    public static function get_lang_options()
    {
        $Lang = PerchLang::fetch();
        
        $lang_dir = $Lang->lang_dir;
        if (is_dir($lang_dir)) {
            $files = PerchUtil::get_dir_contents($lang_dir, false);
            if (is_array($files)) {
                $out = array();
                foreach($files as $file) {
                    $out[] = PerchUtil::strip_file_extension($file);
                }
                return $out;
            }
        }
        return false;
    }
    
    public function reload()
    {
        $Settings = PerchSettings::fetch();
        $this->lang     = $Settings->get('lang')->settingValue();
        $this->lang_file = $this->lang_dir . DIRECTORY_SEPARATOR . $this->lang . '.txt';
        $this->translations= false;
    }
    
    
    private function load_translations()
    {
        $out = false;
        
        if (file_exists($this->lang_file)) {
            $json = file_get_contents($this->lang_file);
            $out  = PerchUtil::json_safe_decode($json, true);
        }

        if (is_array($out)) {
            $this->translations = $out;
        }else{
            $json = file_get_contents($this->lang_dir . DIRECTORY_SEPARATOR . 'en-gb.txt');
            $this->translations = PerchUtil::json_safe_decode($json, true);
            PerchUtil::debug('Unable to load language file: '. $this->lang, 'error');
        }
    }
    
    private function add_translation($string)
    {
        PerchUtil::debug('adding: '.$string);
        $string = preg_replace("/\s+/", ' ', $string);
        $this->to_add[$string] = $string;
    }
    
    private function write_to_lang_file($items)
    {
        if (!is_array($this->translations)) {
            $this->translations = array('lang'=>$this->lang);
        }
        
        $out = array_merge($this->translations, $items);
        $json = PerchUtil::json_safe_encode($out);
        
        $json = PerchUtil::tidy_json($json);
        
        if (is_writable($this->lang_file)) {
            file_put_contents($this->lang_file, $json);
        }
    }

}

?>