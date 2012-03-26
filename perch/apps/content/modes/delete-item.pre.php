<?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
        $idx = (int) $_GET['idx'];
    }

    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }


    // set the current user
    $ContentItem->set_current_user($CurrentUser->id());


    /* --------- Delete Form ----------- */
    
    $Form = new PerchForm('delete');
    
    if ($Form->posted() && $Form->validate() && isset($idx)) {
        
        $ContentItem->delete_item($idx);
        
        if ($Form->submitted_via_ajax) {
    	    echo PERCH_LOGINPATH . '/apps/content/edit/?id='.$ContentItem->id();
    	    exit;
    	}else{
    	    PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content/edit/?id='.$ContentItem->id());
    	}
        
    	
    	
    }

    

?>