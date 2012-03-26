<?php

class PerchSystem
{
    private static $search_handlers = array();
    
    public static function set_page($page)
    {
        $Perch = Perch::fetch();
        $Perch->set_page($page);
        $Content = PerchContent::fetch();
        $Content->clear_cache();
    }
    
    public static function register_search_handler($className)
    {
        self::$search_handlers[] = $className;
        return true;
    }
    
    public static function get_registered_search_handlers()
    {
        return self::$search_handlers;
    }
}


?>