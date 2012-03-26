<?php

class PerchBase
{
    
    protected $db;
    protected $details;
    protected $uses_images = false;
    
    
    function __construct($details) 
    {        
        $this->db       = PerchDB::fetch();
        $this->details  = $details;

        if (defined('PERCH_DB_PREFIX')) {
            $this->table    = PERCH_DB_PREFIX . $this->table;
        }
    }
    
    function __call($method, $arguments)
	{
		if (isset($this->details[$method])) {
			return $this->details[$method];
		}else{
		    PerchUtil::debug('Looking up missing property ' . $method, 'notice');
		    if (isset($this->details[$this->pk])){
		        $sql    = 'SELECT ' . $method . ' FROM ' . $this->table . ' WHERE ' . $this->pk . '='. $this->db->pdb($this->details[$this->pk]);
		        $this->details[$method] = $this->db->get_value($sql);
		        return $this->details[$method];
		    }
		}
		
		return false;
	}
    
    public function to_array()
    {
        return $this->details;
    }
    
    public function id()
    {
        return $this->details[$this->pk];
    }
    
    public function update($data)
    {
        $r = $this->db->update($this->table, $data, $this->pk, (int) $this->details[$this->pk]);
        $this->details = array_merge($this->details, $data);
        
        return $r;
    }
    
    public function delete()
    {
        $this->db->delete($this->table, $this->pk, $this->details[$this->pk]);
    }
    
    public function squirrel($key, $val)
    {
        // non-persistant store
        
        $this->details[$key] = $val;
    }
    
    public function set_details($details)
    {
        if (is_array($details)) {
            foreach($details as $key=>$val) {
                $this->details[$key]=$val;
            }
            
            $this->details[$this->pk] = (int) $this->details[$this->pk];
        }
    }

}

?>