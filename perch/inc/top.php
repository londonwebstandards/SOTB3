<?php
    $Settings->get('headerColour')->settingValue();

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php 
	    echo PerchUtil::html($Perch->page_title);
	    
	    if (!$Settings->get('hideBranding')->settingValue()) {
	        echo PerchUtil::html(' - ' . PerchLang::get('Perch')); 
	    }
	?></title>
	<link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/css/reset.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" />
<?php
    if ($CurrentUser->logged_in()) {
?>
	<link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/css/default.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" />
	<!--[if IE 6]><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/css/ie6.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/css/ie7.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" /><![endif]-->
	
<?php
    }else{
?>
	<link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/css/login.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" />   
<?php
    }
    if (PERCH_DEBUG) {
?>
    <link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/css/debug.css" type="text/css" />
<?php
    }
?>    
	<style type="text/css" media="screen">
	    /* Custom settings */
	   #hd { 
	       background-color: <?php echo PerchUtil::html(rtrim($Settings->get('headerColour')->settingValue(), ';')); ?>;
	   }
	   #hd ul#nav li a:link, #hd ul#nav li a:visited,
	   #hd ul#metanav li a:link, #hd ul#metanav li a:visited  {
	       color: <?php echo PerchUtil::html(rtrim($Settings->get('linkColour')->settingValue(), ';')); ?>;
	   }
	   <?php
	        $val = strtolower($Settings->get('headerColour')->settingValue());
	        if ($val!='#fff' && $val!='#ffffff' && $val!='white') {
	            echo "#login .bd form, #content #h1 { 
	                border-top: none; 
	            }"; 
	        }
	   ?>
	</style>
	<script src="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/js/jquery-ui.js" type="text/javascript"></script>
	<script src="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/assets/js/perch.js?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/javascript"></script>
<?php
    if ($CurrentUser->logged_in()) {
        $javascript = $Perch->get_javascript();
        foreach($javascript as $js) {
            echo "\t".'<script type="text/javascript" src="'.PerchUtil::html($js).'"></script>'."\n";
        }
        
        $stylesheets = $Perch->get_css();
        foreach($stylesheets as $css) {
            echo "\t".'<link rel="stylesheet" href="'.PerchUtil::html($css).'" type="text/css" />'."\n";
        }
        
        echo $Perch->get_head_content();
    }
    
    
    if (file_exists(PERCH_PATH.'/plugins/ui/_config.inc')) {
        include PERCH_PATH.'/plugins/ui/_config.inc';
    }
?>

</head>


<?php
    if ($CurrentUser->logged_in()) {
?>
<body class="role-<?php echo PerchUtil::html(strtolower($CurrentUser->userRole())); ?>">
    <div id="hd">
        <a id="skip" href="#main-panel">Skip to main content</a>
		<ul id="metanav">
		    <?php
		        if ($CurrentUser->userRole() == 'Admin') {
		    ?>
		    <li>
		        <a href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/settings" class="<?php if ($Perch->get_section()=='settings') echo 'selected'; ?>"><?php echo PerchLang::get('Settings'); ?></a>
		    </li>
		    <?php
	            }
		    ?>
			<li class="hybrid <?php 
				if ($Perch->get_section()=='account') echo 'selected '; 
				if (defined('PERCH_AUTH_PLUGIN') && PERCH_AUTH_PLUGIN) echo 'plugin';
				?>">
				<?php if (!defined('PERCH_AUTH_PLUGIN') || !PERCH_AUTH_PLUGIN) { ?>
			    <a href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/account" class="account"><?php echo PerchLang::get('My Account'); ?></a>
				<?php }// auth plugin ?>
			    <a href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>?logout=1" class="logout"><?php echo PerchLang::get('Log out'); ?></a>
			</li>
		</ul>

		
		
		
    <?php
        if ($CurrentUser->logged_in()) {
                        
            $nav   = $Perch->get_apps();
            
            echo '<a id="logo" href="'.$nav[0]['path'] . '"><img src="'.PerchUtil::html($Settings->get('logoPath')->settingValue()) .'" alt="Logo" /></a>';
            
            $section   = $Perch->get_section();
            
            if (is_array($nav)) {
                echo '<ul id="nav">';
                
                // content app - special status
                if ($nav[0]['section']=='apps/content') {
                    $item = $nav[0];
                    echo ($item['section'] == $section ? '<li class="selected">' : '<li>');
                    echo '<a href="'.PerchUtil::html($item['path']).'">'.PerchUtil::html($item['label']).'</a></li>';
                    array_shift($nav);
                }
                             
                // others    
                echo '<li id="appmenu" class="apps">';
                    echo '<ul class="appmenu">';
                            foreach($nav as $item) {
                                echo ($item['section'] == $section ? '<li class="selected">' : '<li>');
                                echo '<a href="'.PerchUtil::html($item['path']).'">'.PerchUtil::html($item['label']).'</a></li>';
                            }
                    echo '</ul>';
                echo '</li>';
                
                // users
                if ($CurrentUser->userRole() == 'Admin' && !PERCH_AUTH_PLUGIN) {
                    $item = array('path'=>PERCH_LOGINPATH.'/users', 'label'=>'Users', 'section'=>'users');
                    echo ($item['section'] == $section ? '<li class="selected">' : '<li>');
                    echo '<a href="'.PerchUtil::html($item['path']).'">'.PerchUtil::html(PerchLang::get($item['label'])).'</a></li>';
                }
                
                echo '</ul>';
                echo '<script type="text/javascript">document.getElementById(\'nav\').style.display=\'none\';</script>';
            }
            
         
         
            // Help markup as used by apps etc
            $help_html = '';
            
            if ($Settings->get('siteURL')->settingValue()) {
                $path = $Settings->get('siteURL')->settingValue();
            }else{
                $path = '/';
            }
            
            $help_html .= '<a id="view-site" class="assist" href="'.PerchUtil::html($path).'"><span>'.PerchLang::get('My Site').'</span></a> ';
            
            if ($Settings->get('helpURL')->settingValue()) {
                $help_html  .= '<a id="help" href="'.PerchUtil::html($Settings->get('helpURL')->settingValue()).'"><span>'.PerchLang::get('Help').'</span></a>';
            }else{
                $help_html  .= '<a id="help" href="'.PERCH_LOGINPATH.'/help"><span>'.PerchLang::get('Help').'</span></a>';
            }
            
            $Perch->help_html = $help_html;
            
            
        }
    
    ?>
        <span class="clear"> </span>
    </div>
<?php
    }else{
?>
<body class="login">
<?php        
    }
?>
    <div id="content">