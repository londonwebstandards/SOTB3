<?php

    $Form = new PerchForm('setup');
    $Form->translate_errors = false;
    
    $req = array();
    $req['licenseKey']     = "Required";
    $req['userGivenName']  = "Required";
    $req['userFamilyName'] = "Required";
    $req['userEmail']      = "Required";
    $req['userUsername']   = "Required";
    $req['userPassword']   = "Required";
    $req['loginpath']      = "Required";
    $req['db_server']      = "Required";
    $req['db_database']    = "Required";
    $req['db_username']    = "Required";
    $req['db_password']    = "Required";

    $Form->set_required($req);
    
    $validation = array();
    $validation['userPassword']	= array("password", "Your passwords must match");

    $Form->set_validation($validation);
    
    if ($Form->posted() && $Form->validate()) {
        
        $postvars = array('userGivenName', 'userFamilyName', 'userEmail', 'userUsername', 'userPassword');
        $user = $Form->receive($postvars);
        PerchSession::set('user', $user);
        
        $postvars = array('loginpath', 'db_server', 'db_database', 'db_username', 'db_password', 'licenseKey');
        $conf = $Form->receive($postvars);
        
        $conf['loginpath'] = rtrim($conf['loginpath'], '/');

        $config_file = file_get_contents('config.sample.php');
        $config_file = preg_replace_callback('/\$(\w+)/', "substitute_vars", $config_file);
        
        $mode = 'configfile';

    }
    
    function substitute_vars($matches)
    {
        global $user, $conf;
    	if (isset($user[$matches[1]])){
    		return $user[$matches[1]];
    	}if (isset($conf[$matches[1]])){
        	return $conf[$matches[1]];
        }else{
    		return '$'.$matches[1];
    	}
    }
    

?>