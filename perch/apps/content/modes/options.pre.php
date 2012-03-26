<?php

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
        $options     = $ContentItem->get_options();
    }


    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }
    
    
    // set the current user
    $ContentItem->set_current_user($CurrentUser->id());
    
    /* --------- Options Form ----------- */
    

    $Form = new PerchForm('options');
    
    if ($Form->posted() && $Form->validate()) {
        $postvars = array('contentAddToTop', 'contentMultiple', 'contentSearchable');
    	$data = $Form->receive($postvars);
        if (!isset($data['contentAddToTop'])) {
            $data['contentAddToTop'] = 0;
        }
        if (!isset($data['contentMultiple'])) {
            $data['contentMultiple'] = 0;
            $ContentItem->truncate(1);
        }
        if (!isset($data['contentSearchable'])) {
            $data['contentSearchable'] = 0;
        }
    	
        $ContentItem->update($data);
        
        // sharing
        $postvars = array('contentShared');
        $data = $Form->receive($postvars);
        if (isset($data['contentShared'])) {
            if ($ContentItem->contentPage()!='*'){
                $ContentItem->globalise();
            }
        }else{
            $prev = $ContentItem->contentPage();
            $deglob = $ContentItem->deglobalise();
            if ($prev=='*' && $deglob==false) {
                $Alert->set('failure', PerchLang::get('Sorry, the region cannot be unshared as it was shared by an earlier software version.'));
            }
        }
        
        
        
        // opts
        
        $postvars = array('sortOrder', 'sortField', 'adminOnly', 'limit', 'searchURL');
    	$data = $Form->receive($postvars);
    	
    	if (!isset($data['adminOnly'])) {
    	    $data['adminOnly'] = 0;
    	}
    	
    	
    	if (!isset($data['limit'])) {
    	    $data['limit'] = false;
    	}
    	
    	$ContentItem->set_options($data);
    	
        
        $ContentItem->republish();
        
        $Alert->set('success', PerchLang::get('Successfully updated'));
        
    }




    
?>