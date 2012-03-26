<?php

class PerchFactory
{
    
    protected $db;
    protected $cache = false;

    
    function __construct() 
    {
        $this->db       = PerchDB::fetch();
        
        if (defined('PERCH_DB_PREFIX')) {
            $this->table    = PERCH_DB_PREFIX.$this->table;
        }
        
    }

    public function find($id)
    {
        $sql    = 'SELECT * 
                    FROM ' . $this->table . '
                    WHERE ' . $this->pk . '='. $this->db->pdb($id) .'
                    LIMIT 1';
                    
        $result = $this->db->get_row($sql);
        
        if (is_array($result)) {
            return new $this->singular_classname($result);
        }
        
        return false;
    }
    
    public function all($Paging=false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }
        
        $sql .= ' * 
                FROM ' . $this->table;
                
        if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' ASC';
        }
        
        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }
        
        $results = $this->db->get_rows($sql);
        
        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }
        
        return $this->return_instances($results);
    }
    
    public function create($data)
    {
        
        $newID  = $this->db->insert($this->table, $data);
        
        if ($newID) {
            $sql    = 'SELECT *
                        FROM ' . $this->table . ' 
                        WHERE ' .$this->pk . '='. $this->db->pdb($newID) .'
                        LIMIT 1';
            $result = $this->db->get_row($sql);
            
            if ($result) {
                return new $this->singular_classname($result);
            }
        }
    }
    
    protected function return_instances($rows)
    {
        if (is_array($rows) && PerchUtil::count($rows) > 0) {
            $out    = array();
            foreach($rows as $row) {
                $out[]  = new $this->singular_classname($row);
            }
            return $out;
        }
        
        return false;
    }
    
    
    protected function return_instance($row)
    {
        if (is_array($row) && PerchUtil::count($row) > 0) {
            return new $this->singular_classname($row);
        }
        
        return false;
    }
    
}

?>