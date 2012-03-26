<?php

class PerchAPI_Factory extends PerchFactory
{
    
    protected $api = false;
    public $static_fields = array();
    
    function __construct($api=false)
    {
        if ($api) $this->api = $api;
        
        parent::__construct();
    }
    
    
    public function attempt_install()
    {
        PerchUtil::debug('Attempting app installation: '.$this->api->app_id);

        $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
        $result = $this->db->get_value($sql);
        
        if ($result==false) {
            $activation_file = PERCH_PATH.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.$this->api->app_id.DIRECTORY_SEPARATOR.'activate.php';
            if (file_exists($activation_file)) {
                return (include ($activation_file));
            }
        }
        
        return false; 
    }
    
}

?>
