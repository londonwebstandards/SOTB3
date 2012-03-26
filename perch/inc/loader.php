<?php

    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        die('Perch requires PHP5. This server is running version ' . PHP_VERSION);
    }


    function perch_autoload($class_name) {
        if (strpos($class_name, 'PerchAPI')!==false) {
            $file = PERCH_PATH . '/lib/api/' . $class_name . '.class.php';
        }else{
            $file = PERCH_PATH . '/lib/' . $class_name . '.class.php';
        }
        
        
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
    
    if (function_exists('spl_autoload_register')) {
        spl_autoload_register('perch_autoload');
    }else{
        function __autoload($class_name) {
            if (strpos($class_name, 'PerchAPI')!==false) {
                require PERCH_PATH . '/lib/api/' . $class_name . '.class.php';
            }else{
                require PERCH_PATH . '/lib/' . $class_name . '.class.php';
            }
        }
    }
    
        
    if (get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
    
    if (function_exists('date_default_timezone_set')) date_default_timezone_set('UTC');

    if (!defined('PERCH_ERROR_MODE')) define('PERCH_ERROR_MODE', 'DIE');
    
?>