<?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
    }

    
    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }

    // Check permission to delete
    if ($CurrentUser->userRole() == 'Editor' && !$Settings->get('editorMayDeleteRegions')->settingValue()) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }



    /* --------- Delete Form ----------- */
    
    $Form = new PerchForm('delete');
    
    if ($Form->posted() && $Form->validate()) {
    	$ContentItem->delete();
    	
    	if ($Form->submitted_via_ajax) {
    	    echo PERCH_LOGINPATH . '/apps/content/';
    	    exit;
    	}else{
    	    PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content/');
    	}
    	    	
    }

    

?>