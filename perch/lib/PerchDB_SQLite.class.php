<?php
/*
    This class is new and currently non-functional.
*/
class PerchDB_SQLite
{
    private $link = false;
	private $errored = false;
	
	private $file;
	
	static public $queries    = 0;
	
    function __construct($file)
    {
        $this->file = $file;
    }
    
	function __destruct() 
	{
		$this->close_link();
	}
	
	private function open_link() 
	{	
		$this->link = sqlite_open($this->file);
		
		if (!$this->link) {
		    switch(PERCH_ERROR_MODE) 
		    {
		        case 'SILENT':
		            break;
		            
		        case 'ECHO':
		            if (!$this->errored) {
		                echo 'Could not connect to the database. Please check that the username and password are correct.';
		                $this->errored = true;
		            }
		            break;
		            
		        default:
		            PerchUtil::redirect(PERCH_LOGINPATH.'/error/db.php');
		            break;
		    }

			PerchUtil::debug("Could not create DB link!", 'error');
			return false;
		}else{
			sqlite_select_db(PERCH_DB_DATABASE);
		}
		
	}
	
	private function close_link() 
	{
		if ($this->link) {
			sqlite_close($this->link);
			unset($this->link);
			$this->link  = false;
		}
	}
	
	private function get_link() 
	{
	    if ($this->link && !@sqlite_ping($this->link)) {
            $this->link = false;
        }
	    
		if (!$this->link) {
			$this->open_link();
		}
		
		return $this->link;
	}
	
	public function execute($sql) 
	{
		PerchUtil::debug($sql, 'db');
		
		$link = $this->get_link();
	    if (!$link) return false;
		
		$result = sqlite_query($sql, $link);
		self::$queries++;
		
		if (sqlite_error()) {
			PerchUtil::debug("Invalid query: " . sqlite_error(), 'error');
			return false;
		}
		
		$newid	= sqlite_insert_id();
		
		if (!$newid) {
		    self::$queries++;
			return sqlite_affected_rows($link);
		}
		
		return $newid;
		
	}
	
	
	public function get_rows($sql) 
	{
		
		PerchUtil::debug($sql, 'db');
		
		$link = $this->get_link();
	    if (!$link) return false;
		
		$result = sqlite_query($sql, $link);
		self::$queries++;
		
		if ($result) {
			
			if (sqlite_num_rows($result) > 0) {
				$r = array();
				while ($a = sqlite_fetch_array($result, MYSQL_ASSOC)) {
					$r[] = $a;
				}
			}else{
				$r = false;
			}
			sqlite_free_result($result);
			return $r;
			
		}else{
			
			PerchUtil::debug("Invalid query: " . sqlite_error(), 'error');
			return false;
		}
		
		
	}
	
	
	public function get_row($sql) 
	{
		PerchUtil::debug($sql, 'db');
		
		$link = $this->get_link();
	    if (!$link) return false;
		
		$result = sqlite_query($sql, $link);
		self::$queries++;
		
		if ($result) {			
			if (sqlite_num_rows($result) > 0) {
				$r	= sqlite_fetch_array($result, MYSQL_ASSOC);
			}else{
				$r = false;
			}
			sqlite_free_result($result);
			return $r;
			
		}else{
			
			PerchUtil::debug("Invalid query: " . sqlite_error(), 'error');
			return false;
		}
		
		
	}
	
	public function get_value($sql) 
	{
		
		$result = $this->get_row($sql);
		
		if (is_array($result)) {
			foreach($result as $val) {
				return $val;
			}
		}
		
		return false;
		
	}
	
	public function get_count($sql)
	{
	    $result = $this->get_value($sql);
	    return intval($result);
	}
	
	public function insert($table, $data) 
	{
		
		$cols	= array();
		$vals	= array();
		
		foreach($data as $key => $value) {
			$cols[] = $key;
			$vals[] = $this->pdb($value);
		}
		
		$sql = 'INSERT INTO ' . $table . '(' . implode(',', $cols) . ') VALUES(' . implode(',', $vals) . ')';
		
		return $this->execute($sql);
		
	}
	
	public function update($table, $data, $id_column, $id) 
	{
		
		$sql = 'UPDATE ' . $table . ' SET ';
		
		$items = array();
		
		foreach($data as $key => $value) {
			$items[] =  $key . ' = ' . $this->pdb($value);
		}
		
		$sql .= implode(', ', $items);
		
		$sql .= ' WHERE ' . $id_column . ' = ' . $this->pdb($id);
		
		return $this->execute($sql);
		
		
	}
	
	public function delete($table, $id_column, $id, $limit=false) 
	{
		
		$sql = 'DELETE FROM ' . $table . ' WHERE ' . $id_column . ' = ' . $this->pdb($id);
		
		if ($limit) {
			$sql .= ' LIMIT ' . $limit;
		}
		
		
		return $this->execute($sql);
		
	}
	
	
	public function pdb($value)
	{
		// Stripslashes
		if (get_magic_quotes_runtime()) {
			$value = stripslashes($value);
		}
		
		$link = $this->get_link();
	    if (!$link) return false;

		// Quote
		switch(gettype($value)) {
			case 'integer':
			case 'double':
				$escape = $value;
				break;
			case 'string':
				$escape = "'" . sqlite_real_escape_string($value, $link) . "'";
				break;
			case 'NULL':
				$escape = 'NULL';
				break;
			default:
				$escape = "'" . sqlite_real_escape_string($value, $link) . "'";
		}

		return $escape;
	}
	
	public function get_table_meta($table)
	{
		$sql	= 'SELECT * FROM ' . $table . ' LIMIT 1';
		
		$result = sqlite_query($sql, $this->get_link());
		self::$queries++;
		
		if ($result) {			
			$r	= array();
			$i 	= 0;
			while ($i < sqlite_num_fields($result)) {
			    $r[] = sqlite_fetch_field($result, $i);
				$i++;
			}
			sqlite_free_result($result);
			return $r;
		}else{
			
			PerchUtil::debug("Invalid query: " . sqlite_error(), 'error');
			return false;
		}
		
	}
	
	
}

?>
