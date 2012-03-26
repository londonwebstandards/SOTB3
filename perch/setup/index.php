<?php

    // Double-check for PHP5.
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        die('Perch requires PHP 5 or greater to install. You have: PHP ' . PHP_VERSION);
    }
    
    if (file_exists(dirname(__FILE__) . '/../config/config.php')) {
        include_once(dirname(__FILE__) . '/../config/config.php');   
    }
    
    
    require_once dirname(__FILE__) . '/../lib/PerchUtil.class.php';
    require_once dirname(__FILE__) . '/../lib/PerchLang.class.php';
    require_once dirname(__FILE__) . '/../lib/Perch.class.php';
    require_once dirname(__FILE__) . '/../lib/PerchSession.class.php';
    require_once dirname(__FILE__) . '/../lib/PerchForm.class.php';
    
    
    $Perch  = new Perch;



    if (!defined('PERCH_PATH')) {
        define('PERCH_PATH', realpath('../'));
    }
    
    define('PERCH_ERROR_MODE', 'ECHO');


    include(PERCH_PATH . '/inc/loader.php');


    $mode = 'gather';
    
    if (isset($_GET['install'])) {
        $mode = 'install';
    }


    include('modes/'.$mode.'.pre.php');
    include('top.php');
    
    include('modes/'.$mode.'.post.php');    
    
    include('btm.php')


?>