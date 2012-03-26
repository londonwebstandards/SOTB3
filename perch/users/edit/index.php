<?php
    include(dirname(__FILE__) . '/../../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');

    if ($CurrentUser->userRole() != 'Admin') {
        PerchUtil::redirect(PERCH_LOGINPATH);
    }

    
    
    $Perch->page_title = PerchLang::get('Manage Users');
    $Alert = new PerchAlert;
    
    include(dirname(__FILE__) . '/../modes/edit.pre.php');
    
    include(PERCH_PATH . '/inc/top.php');

    include(dirname(__FILE__) . '/../modes/edit.post.php');

    include(PERCH_PATH . '/inc/btm.php');

?>
