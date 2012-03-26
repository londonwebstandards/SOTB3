<?php
    $auth_page = true;

    include('config/config.php');

    if (!defined('PERCH_PATH')) {
        header('Location: setup');
        exit;
    }

    include(PERCH_PATH . '/inc/loader.php');

    $Perch  = new Perch;
    include('inc/auth.php');
    
    // Check for logout
    if ($CurrentUser->logged_in() && isset($_GET['logout']) && is_numeric($_GET['logout'])) {
        $CurrentUser->logout();
    }

    // If the user's logged in, send them to edit content
    if ($CurrentUser->logged_in()) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content/');
    }

    $Perch->page_title = PerchLang::get('Log in');
    
    include('inc/top.php');
?>
    
    <div id="login">
        <div id="hd">
            <img src="<?php echo PerchUtil::html($Settings->get('logoPath')->settingValue()); ?>" alt="Logo" />
        </div>
        <div class="bd">
            <form action="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/" method="post">
                
                <?php
                    if (isset($_POST['login']) && @$_POST['username']!='' && @$_POST['password']!='') {
                        if ($CurrentUser->activation_failed) {
                            echo '<p class="alert-failure">' . PerchLang::get('Sorry, your license key isn\'t valid for this domain.');
                            
                            if (!$Settings->get('hideBranding')->settingValue()) {
                                echo '<br />';
                                echo PerchLang::get('Log into your %sPerch account%s and add the following as your <em>live</em> or <em>testing</em> domain:', '<a href="https://grabaperch.com/account">', '</a>');
                                echo ' <code>'.PerchUtil::html($_SERVER['SERVER_NAME']).'</code>';
                            }
                            
                            echo '</p>';
                        }else{
                            echo '<p class="alert-failure">' . PerchLang::get('Sorry, those details are incorrect. Please try again.') . '</p>';
                        }
                    }
                ?>
                
                
                <div<?php if (isset($_POST['login']) && @$_POST['username']=='') echo ' class="error"'; ?>>
                    <label for="username"><?php echo PerchLang::get('Username'); ?></label>
                    <input type="text" name="username" value="<?php echo PerchUtil::html(@$_POST['username'],1); ?>" id="username" class="text" />
                    <?php if (isset($_POST['login']) && @$_POST['username']=='') echo '<span class="error">'.PerchLang::get('Required').'</span>'; ?>
                </div>
                
                <div<?php if (isset($_POST['login']) && @$_POST['password']=='') echo ' class="error"'; ?>>
                    <label for="password"><?php echo PerchLang::get('Password'); ?></label>
                    <input type="password" name="password" value="" id="password" class="text" />
                    <?php if (isset($_POST['login']) && @$_POST['password']=='') echo '<span class="error">'.PerchLang::get('Required').'</span>'; ?>
                </div>

                <p>
                    <input type="submit" class="button" value="<?php echo PerchLang::get('Log in'); ?>">
                    <input type="hidden" name="login" value="1" />
                </p>
            </form>
        </div>
        
    </div>


<?php
    include('inc/btm.php');

?>
