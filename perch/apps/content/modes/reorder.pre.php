<?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
    }

    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content/');
    }

    // set the current user
    $ContentItem->set_current_user($CurrentUser->id());


    /* --------- Reorder Form ----------- */
    
    $Form = new PerchForm('reorder');
    
    if ($Form->posted()) {
        
        $action = false;
        
        
        if (isset($_POST['up'])) {
            $move_id = $_POST['up'];
            $action  = 'up';
        }
        
        if (isset($_POST['down'])) {
            $move_id = $_POST['down'];
            $action  = 'down';
        }
        
        if ($action) {
            $items = $ContentItem->pull_from_history();
            
            if (PerchUtil::count($items)) {
                            
                // remove the item from the array
                $new_items = array();
                for($i=0; $i<PerchUtil::count($items); $i++) {
                    if ($items[$i]['_id']==$move_id) {
                        $move_item = $items[$i];
                        if ($action=='up') {
                            $new_position = $i-1;
                        }else{
                            $new_position = $i+1;
                        }
                    }else{
                        $new_items[] = $items[$i];
                    }
                }
                $items = $new_items;
                
                $new_items = array();
                $readded = false;
                for($i=0; $i<PerchUtil::count($items); $i++) {
                    if ($i==$new_position) {
                        $new_items[] = $move_item;
                        $readded = true;
                    }
                    
                    $new_items[] = $items[$i];
                }
                if (!$readded) $new_items[] = $move_item;
                $items = $new_items;
                
                
                $opts = $ContentItem->get_options();
                $opts['sortField'] = '';
                $ContentItem->set_options($opts);
                $ContentItem->republish($items);
            }
        }
        
        
    	
    	
    }
    
    
    $items = $ContentItem->pull_from_history();

?>