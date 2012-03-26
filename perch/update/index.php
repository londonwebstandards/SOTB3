<?php
    $auth_page = true;
    
    include('../config/config.php');

    include(PERCH_PATH . '/inc/loader.php');

    $Perch  = PerchAdmin::fetch();

    include(PERCH_PATH . '/inc/auth.php');

    if ($CurrentUser->logged_in()) {
        $CurrentUser->logout();
    }


    $Perch->page_title = PerchLang::get('Update');
    
    include('../inc/top.php');
?>
    
    <div id="login">
        <div id="hd">
            <img src="<?php echo PerchUtil::html($Settings->get('logoPath')->settingValue()); ?>" alt="Logo" />
        </div>
        <div class="bd">
            <div class="cont">
                <h1><?php echo PerchLang::get('Perch will now update your database to bring it up-to-date with the latest version.')?></h1>
                
                <?php
                
                    $db = PerchDB::fetch();
                    $errors = false;
                    if ($Settings->get('latestUpdate')->settingValue() != $Perch->version) {
                        
                        $sql = '';
                        
                        $sql .= file_get_contents('update-1.2.sql');                
                        $sql .= file_get_contents('update-1.6.sql');
                        $sql .= file_get_contents('update-1.7.sql');
                        $sql .= file_get_contents('update-1.8.sql');
                        
                        $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
                        $sql = str_replace('__PERCH_LOGINPATH__', PERCH_LOGINPATH, $sql);
                        $queries = explode(';', $sql);
                        if (PerchUtil::count($queries) > 0) {
                            foreach($queries as $query) {
                                $query = trim($query);
                                if ($query != '') {
                                    $db->execute($query);
                                    if (mysql_errno() && (int)mysql_errno()!=1060 && (int)mysql_errno()!=1061) { // 1060 == duplicate column, 1061 == duplicate key
                                        echo '<p class="error">'.PerchUtil::html(PerchLang::get('The following error occurred:')) .'</p>';
                                        echo '<p><code class="sql">'.PerchUtil::html($query).'</code></p>';
                                        echo '<p><code>'.PerchUtil::html(mysql_error()).'</code></p>';
                                        $errors = true;
                                    }
                                }
                            }
                        }
                    }
                    
                
                    if (!$errors) {
                        $Settings->set('latestUpdate', $Perch->version);
                        echo '<p>'.PerchLang::get('Please now delete the <code>update</code> folder from your server.').'</p>';
                        echo '<p><a href="'.PERCH_LOGINPATH.'" class="button">'. PerchLang::get('All done, ready to log in&hellip;').'</a></p>';
                     
                    }else{
                        echo '<p>' . sprintf(PerchLang::get('The errors could be due to running update more than once. Otherwise, %scontact support%s and let us know what went wrong.'),'<a href="http://support.grabaperch.com">','</a>');
                    }
                ?>
                
            </div>
        </div>
        
    </div>


<?php
    include(PERCH_PATH . '/inc/btm.php');
?>