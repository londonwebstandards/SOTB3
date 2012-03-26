<?php
    include(dirname(__FILE__) . '/../../../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');

    
    
    $Perch->page_title = PerchLang::get('Delete Content');
    
    include(dirname(__FILE__) . '/../PerchContent.class.php');
    include(dirname(__FILE__) . '/../PerchContentItem.class.php');
    
    $PerchContent = new PerchContent;
    
    include(dirname(__FILE__) . '/../modes/delete-item.pre.php');
    
    include(PERCH_PATH . '/inc/top.php');

    include(dirname(__FILE__) . '/../modes/delete-item.post.php');

    include(PERCH_PATH . '/inc/btm.php');

?>
