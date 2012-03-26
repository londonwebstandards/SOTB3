<?php

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $User = $Users->find($id);
    }


    if (!$User || !is_object($User)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/users');
    }
    

    /* --------- Reset Password Form ----------- */

    $fReset 	= new PerchForm('reset', false);

    if ($fReset->posted() && $fReset->validate()) {

		$User->reset_pwd_and_notify();
		$Alert->set('success', PerchLang::get('A new password has been sent by email.'));

    }




    /* --------- Edit User Form ----------- */

    $Form 	= new PerchForm('user', false);

    $req = array();
    $req['userUsername']   = "Required";
    $req['userGivenName']  = "Required";
    $req['userFamilyName'] = "Required";
    $req['userEmail']      = "Required";
    
    if ($User->id() != $CurrentUser->id()){
        $req['userRole']       = "Required";
    }


    $Form->set_required($req);

    $validation = array();
    $validation['userUsername']	= array("username", PerchLang::get("Username not available, try another."), array('userID'=>$User->id()));
    $validation['userEmail']	= array("email", PerchLang::get("Email incomplete or already in use."), array('userID'=>$User->id()));

    $Form->set_validation($validation);

    if ($Form->posted() && $Form->validate()) {

		$data		= array();
		$postvars 	= array('userUsername', 'userGivenName', 'userFamilyName','userEmail','userRole');
		$data = $Form->receive($postvars);

		$User->update($data);
		$Alert->set('success', PerchLang::get('User successfully updated.'));

    }
    


    $details = $User->to_array();


?>