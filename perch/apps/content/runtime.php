<?php

    require('PerchContent.class.php');
    require('PerchContentItem.class.php');

    perch_content_check_preview();

    function perch_content($key=false, $return=false)
    {
        if ($key === false) {
            echo 'You must pass in a <em>key</em> for the content. e.g. <code style="color: navy;background: white;">&lt;' . '?php perch_content(\'Phone number\'); ?' . '&gt;</code>'; 
        }
        
        $Content = PerchContent::fetch();
        $out = $Content->get($key);
        
        // Post processing - if there are still <perch:x /> tags
        if (strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
		        
        if ($return) return $out;
        echo $out;
    }
    
    
    function perch_content_custom($key=false, $opts=false, $return=false)
    {
        if ($key === false) return ' ';

        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $return = true; 
            $postpro = false;
        }else{
            $postpro = true;
        }
        
        $Content = PerchContent::fetch();
        
        $out = $Content->get_custom($key, $opts);
        
        // Post processing - if there are still <perch:x /> tags
        if ($postpro && !is_array($out) && strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
        
        if ($return) return $out;
        echo $out;
    }
    
    
    function perch_content_check_preview()
    {
        if (isset($_GET['preview'])) {
            if ($_GET['preview'] == 'all') {
                $contentID = 'all';
            }else{
                $contentID  = (int)$_GET['preview'];
            }
            
            $rev        = false;
            
            if (isset($_GET['rev']) && is_numeric($_GET['rev'])) {
                $rev = (int)$_GET['rev'];
            }
            
            $Users          = new PerchUsers;
            $CurrentUser    = $Users->get_current_user();
            
            if (is_object($CurrentUser) && $CurrentUser->logged_in()) {
                $Content = PerchContent::fetch();
                $Content->set_preview($contentID, $rev);
            }
        }
    }
    
    
    function perch_content_search($key=false, $opts=false, $return=false)
    {
        if ($key === false) return ' ';
        
        $key = trim(stripslashes($key));
        
        $Content = PerchContent::fetch();
        
        $defaults = array();
        $defaults['template'] = 'search-result.html';
        $defaults['count'] = 10;
        $defaults['excerpt_chars'] = 250;
        $defaults['from_path'] = '/';
        $defaults['hide_extensions'] = false;
        
        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }
        
        $out = $Content->search_content($key, $opts);
        
        
        // Post processing - if there are still <perch:x /> tags
        if (strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
        
        if ($return) return $out;
        echo $out;
    }
    

?>