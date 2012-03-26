<?php
    include(dirname(__FILE__) . '/../../../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');

    $Alert = new PerchAlert;

    
    
    $Perch->page_title = PerchLang::get('Manage Content');
    
    include(dirname(__FILE__) . '/../PerchContent.class.php');
    include(dirname(__FILE__) . '/../PerchContentItem.class.php');
    
    $PerchContent = new PerchContent;
    
    include(dirname(__FILE__) . '/../modes/order.pre.php');
    
    include(PERCH_PATH . '/inc/top.php');

    include(dirname(__FILE__) . '/../modes/order.post.php');

    include(PERCH_PATH . '/inc/btm.php');

?>
