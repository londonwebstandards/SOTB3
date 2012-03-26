<?php

class PerchDB {
	
	static private $instance;
	
	public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            if (substr(PERCH_DB_DATABASE, 0, 9)=='sqlite://') {
                $c = 'PerchDB_SQLite';
                $file = substr(PERCH_DB_DATABASE, 9);
                self::$instance = new $c($file);
            }else{
                $c = 'PerchDB_MySQL';
                self::$instance = new $c;
            }
        }

        return self::$instance;
	}
	
}
?>