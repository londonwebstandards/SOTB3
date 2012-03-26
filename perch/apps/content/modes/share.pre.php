<?php

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
    }


    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }
    
    
    /* --------- Globalise Form ----------- */
    
    if ($ContentItem->contentTemplate() != '') {
        $fGlobalise = new PerchForm('globalise');
        
        if ($fGlobalise->posted() && $fGlobalise->validate()) {
            $ContentItem->globalise();
            
            $Alert->set('success', PerchLang::get('Successfully shared'));
            
            PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content/edit/?id='.$id);
        }
    }

    
?>