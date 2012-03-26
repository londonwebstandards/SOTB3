<?php
    include(dirname(__FILE__) . '/../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');

    if ($CurrentUser->userRole() != 'Admin') {
        PerchUtil::redirect(PERCH_LOGINPATH);
    }

    
    
    $Perch->page_title = PerchLang::get('Settings');
    $Alert = new PerchAlert;
    
    include('modes/basic.pre.php');
    
    $Perch->find_installed_apps($CurrentUser);
    
    include(PERCH_PATH . '/inc/top.php');

    include('modes/basic.post.php');

    include(PERCH_PATH . '/inc/btm.php');

?>
