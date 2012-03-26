<?php
    include(dirname(__FILE__) . '/../../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');

    
    
    $Perch->page_title = PerchLang::get('Manage Content');
    
    include('PerchContent.class.php');
    include('PerchContentItem.class.php');
    
    $PerchContent = new PerchContent;
    
    include('modes/list.pre.php');
    
    include(PERCH_PATH . '/inc/top.php');

    include('modes/list.post.php');

    include(PERCH_PATH . '/inc/btm.php');

?>
