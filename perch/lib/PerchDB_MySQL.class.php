<?php

class PerchDB_MySQL
{
    private $link = false;
	private $errored = false;
	
	static public $queries    = 0;
	
    
	function __destruct() 
	{
		$this->close_link();
	}
		
	private function open_link() 
	{
		$this->link = @mysql_connect(PERCH_DB_SERVER, PERCH_DB_USERNAME, PERCH_DB_PASSWORD, true);
		
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
			mysql_select_db(PERCH_DB_DATABASE);
		}
		
	}
	
	private function close_link() 
	{
		if ($this->link && @mysql_ping($this->link)) {
			mysql_close($this->link);
			unset($this->link);
			$this->link  = false;
		}
	}
	
	private function get_link() 
	{
	    if ($this->link && !@mysql_ping($this->link)) {
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
		
		$result = mysql_query($sql, $link);
		self::$queries++;
		
		if (mysql_error()) {
			PerchUtil::debug("Invalid query: " . mysql_error(), 'error');
			return false;
		}
		
		$newid	= mysql_insert_id();
		
		if (!$newid) {
		    self::$queries++;
			return mysql_affected_rows($link);
		}
		
		return $newid;
		
	}
	
	
	public function get_rows($sql) 
	{
		
		PerchUtil::debug($sql, 'db');
		
		$link = $this->get_link();
	    if (!$link) return false;
		
		$result = mysql_query($sql, $link);
		self::$queries++;
		
		if ($result) {
			
			if (mysql_num_rows($result) > 0) {
				$r = array();
				while ($a = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$r[] = $a;
				}
			}else{
				$r = false;
			}
			mysql_free_result($result);
			return $r;
			
		}else{
			
			PerchUtil::debug("Invalid query: " . mysql_error(), 'error');
			return false;
		}
		
		
	}
	
	
	public function get_row($sql) 
	{
		PerchUtil::debug($sql, 'db');
		
		$link = $this->get_link();
	    if (!$link) return false;
		
		$result = mysql_query($sql, $link);
		self::$queries++;
		
		if ($result) {			
			if (mysql_num_rows($result) > 0) {
				$r	= mysql_fetch_array($result, MYSQL_ASSOC);
			}else{
				$r = false;
			}
			mysql_free_result($result);
			return $r;
			
		}else{
			
			PerchUtil::debug("Invalid query: " . mysql_error(), 'error');
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
				$escape = "'" . mysql_real_escape_string($value, $link) . "'";
				break;
			case 'NULL':
				$escape = 'NULL';
				break;
			default:
				$escape = "'" . mysql_real_escape_string($value, $link) . "'";
		}

		return $escape;
	}
	
	public function get_table_meta($table)
	{
		$sql	= 'SELECT * FROM ' . $table . ' LIMIT 1';
		
		$result = mysql_query($sql, $this->get_link());
		self::$queries++;
		
		if ($result) {			
			$r	= array();
			$i 	= 0;
			while ($i < mysql_num_fields($result)) {
			    $r[] = mysql_fetch_field($result, $i);
				$i++;
			}
			mysql_free_result($result);
			return $r;
		}else{
			
			PerchUtil::debug("Invalid query: " . mysql_error(), 'error');
			return false;
		}
		
	}
	
	
}

?>