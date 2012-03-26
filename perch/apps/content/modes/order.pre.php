<?php

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
    }


    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }
    
    
    /* --------- Order Form ----------- */
    
    if ($ContentItem->contentMultiple()=='1') {
        $Form = new PerchForm('order');
        
        if ($Form->posted() && $Form->validate()) {
            $postvars = array('contentAddToTop');
        	$data = $Form->receive($postvars);
        	if (!isset($data['contentAddToTop'])) {
        	    $data['contentAddToTop'] = 0;
        	}
        	
            $ContentItem->update($data);
            
            $Alert->set('success', PerchLang::get('Successfully updated'));
            
            PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content/edit/?id='.$id);
        }
    }




    
?>