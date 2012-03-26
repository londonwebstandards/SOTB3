<?php

class PerchSettings extends PerchFactory
{
    protected $singular_classname = 'PerchSetting';
    protected $table    = 'settings';
    protected $pk       = 'settingID';
    protected $default_sort_column = 'settingID';
 
    static protected $instance;
    
    public static function fetch()
	{
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}
 
    public function set($settingID, $settingValue)
    {
        $this->db->delete($this->table, $this->pk, $settingID);
        
        $data   = array();
        $data['settingID'] = $settingID;
        $data['settingValue'] = $settingValue;
        $this->db->insert($this->table, $data);
    }
    
    public function get($settingID)
    {
        if ($this->cache === false) {
            $sql = 'SELECT settingID, settingValue FROM ' . $this->table;
            $rows = $this->db->get_rows($sql);
            $this->cache = array();
            if (PerchUtil::count($rows) > 0) {
                foreach($rows as $row) {
                    $this->cache[$row['settingID']] = $row;
                }
            }
        }
        
        if ($this->cache !== false){
            if (isset($this->cache[$settingID])) {
                return $this->return_instance($this->cache[$settingID]);
            }
        }
        
        // always return something, even if it's just an empty object.
        return $this->return_instance(array('settingID'=>$settingID, 'settingValue'=>''));
    }
    
    public function get_as_array()
    {
        $sql = 'SELECT settingID, settingValue FROM ' . $this->table;
        $rows = $this->db->get_rows($sql);
        $out = array();
        if (PerchUtil::count($rows) > 0) {
            foreach($rows as $row) {
                $out[$row['settingID']] = $row['settingValue'];
            }
        }
        
        return $out;
    }
    
    public function reload()
    {
        PerchUtil::debug('Reloading setting data');
        $this->cache = false;
    }
}

?>