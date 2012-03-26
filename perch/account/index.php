<?php
    include(dirname(__FILE__) . '/../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');

    
    
    $Perch->page_title = PerchLang::get('My Account');
    $Alert = new PerchAlert;
    
    include('modes/edit.pre.php');
    
    include(PERCH_PATH . '/inc/top.php');

    include('modes/edit.post.php');

    include(PERCH_PATH . '/inc/btm.php');

?>
