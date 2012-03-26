<?php

class PerchAPI
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    function __construct($version=1.0, $app_id)
    {
        $this->app_id = $app_id;
        $this->version = $version;
        
        if (!defined('PERCH_APPS_EDITOR_PLUGIN')) define('PERCH_APPS_EDITOR_PLUGIN', 'markitup');
        if (!defined('PERCH_APPS_EDITOR_MARKUP_LANGUAGE')) define('PERCH_APPS_EDITOR_MARKUP_LANGUAGE', 'textile');
    }

    public function get($class)
    {
        $full_class_name = 'PerchAPI_'.$class;
        
        switch ($class) {
            case 'DB':
                return PerchDB::fetch();
                break;
                
            case 'Lang':
                if ($this->Lang === false) {
                    $this->Lang = new $full_class_name($this->version, $this->app_id);
                }
                return $this->Lang;
                break;
            
            default:
                return new $full_class_name($this->version, $this->app_id, $this->Lang);
                break;
        }
        
        return false;
    }
    
    public function app_path()
    {
        return PERCH_LOGINPATH.'/apps/'.$this->app_id;
    }
}

?>
