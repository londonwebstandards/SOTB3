<?php
    $ContentItem = false;
    
    $place_token_on_main = false;
    
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $ContentItem = $PerchContent->find($id);
    }


    if (!$ContentItem || !is_object($ContentItem)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }
    
    // test to see if the item is hidden from editors
    if ($CurrentUser->userRole() == 'Editor' && $ContentItem->get_option('adminOnly')==true) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/apps/content');
    }
    
    // test to see if image folder is writable
    $image_folder_writable = is_writable(PERCH_RESFILEPATH);

    // find the number of items
    list($details, $items, $history) = $ContentItem->refresh_details();

    // set the current user
	$ContentItem->set_current_user($CurrentUser->id());    

    $options = $ContentItem->get_options();

    $template_help_html = '';
    $mapcount = 0;
    $has_map = false;
    
    

    /* --------- Template Form ----------- */
    
    if ($ContentItem->contentTemplate() == '') {
        
        $fTemplate = new PerchForm('template');
        
        $req = array();
        $req['contentTemplate'] = "Required";
        $fTemplate->set_required($req);
        
        if ($fTemplate->posted() && $fTemplate->validate()) {
        	$postvars = array('contentTemplate', 'contentMultiple');
        	$data = $fTemplate->receive($postvars);
        	
        	if (!isset($data['contentMultiple'])) {
        	    $data['contentMultiple'] = 0;
        	}
        	
        	$data['contentNew'] = 0;
        	
        	$ContentItem->update($data);
        	

        }   
    }
    
    
    /* --------- Undo Form ----------- */
    
    if ($ContentItem->contentTemplate() != '') {
        
        $fUndo = new PerchForm('undo');

        if ($fUndo->posted()) {
        	if ($ContentItem->revert_most_recent()) {
        	    
                list($details, $items, $history) = $ContentItem->refresh_details();
        	    
        	    $Alert->set('success', PerchLang::get('Your most recent change has been reverted.'));
        	}else{
        	    $Alert->set('failure', PerchLang::get('There was nothing to undo.'));
        	}
            
        }   
    }
    
    
    /* --------- Reorder Form (ajax) ----------- */
    
    if ($ContentItem->contentTemplate() != '') {
        
        $rReorder = new PerchForm('reorder');

        if ($rReorder->posted() && $rReorder->submitted_via_ajax) {
            $postvars = array('new_order');
        	$pdata = $rReorder->receive($postvars);
        	$new_order = explode(',', $pdata['new_order']);
        	
        	if (PerchUtil::count($new_order)) {
        	    $itemsjson = $ContentItem->pull_from_history();
            	$new_items = array();
            	
            	foreach($new_order as $idx) {
            	    $new_items[] = $itemsjson[$idx];
            	}
            	
                if (PerchUtil::count($new_items) < PerchUtil::count($itemsjson)) {
                    die('false'); // no data loss.
                }
                
                $opts = $ContentItem->get_options();
                $opts['sortField'] = '';
                $ContentItem->set_options($opts);
                $ContentItem->republish($new_items);
                list($details, $items, $history) = $ContentItem->refresh_details();
                
                $Conf->debug=false;
                $place_token_on_main = $rReorder;
        	}
        	
        	
        	
        	
        }   
    }
    

    /* --------- Edit Form ----------- */
    
    
    if ($ContentItem->contentTemplate() != '') {

        $Template = new PerchTemplate('/templates/content/'.$ContentItem->contentTemplate(), 'content');

        $tags   = $Template->find_all_tags('content');
        $template_help_html = $Template->find_help();
        
        $Form = new PerchForm('edit');
        
        $req = array();
        
        // Check for required content
        if (is_array($tags)) {
            for($i=0; $i<$items; $i++) {
                $seen_tags = array();
                $postitems = $Form->find_items('perch_'.$i.'_');
                
                foreach($tags as $tag) {
                    $item_id = 'perch_'.$i.'_'.$tag->id();
                    if (!in_array($tag->id(), $seen_tags)) {
                        if (PerchUtil::bool_val($tag->required())) {
                            if ($tag->type() == 'date') {
                                if ($tag->time()) {
                                    $req[$item_id.'_minute'] = "Required";
                                }else{
                                    $req[$item_id.'_year'] = "Required";
                                }
                            }else{
                                $req[$item_id] = "Required";
                            }
                        
                        }
                    
                        $seen_tags[] = $tag->id();
                    }
                }
            }
        }
        
        $Form->set_required($req);
        
        
        if ($Form->posted() && $Form->validate()) {
        	$form_vars      = array();
        	$processed_vars = array();
        	$file_paths     = array();

            if (is_array($tags)) {
                
                for($i=0; $i<$items; $i++) {
                    
                    $seen_tags = array();
                    $postitems = $Form->find_items('perch_'.$i.'_');
                    
                    foreach($tags as $tag) {
                        $item_id = 'perch_'.$i.'_'.$tag->id();
                        
                        if (!in_array($tag->id(), $seen_tags)) {
                            $var = false;
                            switch($tag->type()) {
                                case 'date' :
                                    $var = $Form->get_date($tag->id(), $postitems);
                                    break;
                                    
                                case 'slug' :
                                    if (isset($postitems[$tag->for()])) {
                                        $var = PerchUtil::urlify(trim($postitems[$tag->for()]));
                                    }
                                    break;
                                
                                case 'image' :
                                case 'file' :
                                    if ($image_folder_writable && isset($_FILES[$item_id]) && (int) $_FILES[$item_id]['size'] > 0) {
                                        $filename = PerchUtil::tidy_file_name($_FILES[$item_id]['name']);
                                        if (strpos($filename, '.php')!==false) $filename .= '.txt'; // diffuse PHP files
                                        $target = PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.$filename;
                                        if (file_exists($target)) {                                        
                                            $dot = strrpos($filename, '.');
                                            $filename_a = substr($filename, 0, $dot);
                                            $filename_b = substr($filename, $dot);

                                            $count = 1;
                                            while (file_exists(PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.PerchUtil::tidy_file_name($filename_a.'-'.$count.$filename_b))) {
                                                $count++;
                                            }

                                            $filename = PerchUtil::tidy_file_name($filename_a . '-' . $count . $filename_b);
                                            $target = PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.$filename;
                                            
                                        }
                                                                    
                                        PerchUtil::move_uploaded_file($_FILES[$item_id]['tmp_name'], $target);
                                        $file_paths[$tag->id()] = $target;     
                                                                                
                                        $var = PERCH_RESPATH.'/'.$filename;
                                        
                                        $ContentItem->log_resource($var);
                                        
                                        // thumbnail
                                        $PerchImage = new PerchImage;
                                        $PerchImage->resize_image($target, 150, 150, false, 'thumb');
                                    }

                                    if (!isset($_FILES[$item_id]) || (int) $_FILES[$item_id]['size'] == 0) {
                                        if (isset($_POST[$item_id.'_remove'])) {
                                            $var = false;
                                        }else{
                                            if (isset($details[$i][$tag->id()])){
                                                $var = $details[$i][$tag->id()];   
                                            }
                                        }                                
                                    }
                                    
                                    break;
                                    
                                case 'map':
                                    if (isset($postitems[$tag->id().'_adr']) && $postitems[$tag->id().'_adr']!='') {
                                        $tmp = array();
                                        $tmp['adr'] = trim($postitems[$tag->id().'_adr']);
                                        
                                        $map_fields = array('lat', 'lng', 'clat', 'clng', 'type', 'zoom');
                                        foreach($map_fields as $map_field) {
                                            if (isset($postitems[$tag->id().'_'.$map_field]) && $postitems[$tag->id().'_'.$map_field]!=''){
                                                $tmp[$map_field] = $postitems[$tag->id().'_'.$map_field];
                                            }
                                        }
                                                                                
                                        $var = $ContentItem->pre_process_map($ContentItem->id().'-'.$mapcount, $tag, $tmp);
                                        $mapcount++;
                                    }
                                    break;
                            
                                default: 
                                    if (isset($postitems[$tag->id()])) {
                                        $var = trim($postitems[$tag->id()]);
                                    }
                            }
                    
                    
                            if ($var || (is_string($var) && strlen($var))) {
                                if (!is_array($var)) $var = stripslashes($var);
                                $form_vars[$i][$tag->id()] = $var;
                                $processed_vars[$i][$tag->id()] = $ContentItem->post_process_field($tag, $var);
                                
                                // title
                                if ($tag->title()) {
                                    $title_var = $var;
                                    
                                    if (is_array($var) && isset($var['_title'])) {
                                        $title_var = $var['_title'];
                                    }
                                    
                                    if (isset($form_vars[$i]['_title'])) {
                                        $form_vars[$i]['_title'] .= ' '.$title_var;
                                        $processed_vars[$i]['_title'] = ' '.$title_var;
                                    }else{
                                        $form_vars[$i]['_title'] = $title_var;
                                        $processed_vars[$i]['_title'] = $title_var;
                                    }
                                    
                                }
                            }
                            $seen_tags[] = $tag->id();
                        }
                    }
                    
                    
                    // _id
                    if (isset($postitems['_id'])) {
                        $var = trim($postitems['_id']);
                        if ($var || (is_string($var) && strlen($var))) {
                            if ($var == '__new__') $var = $ContentItem->get_next_id();
                            $form_vars[$i]['_id'] = $var;
                            $processed_vars[$i]['_id'] = $var;
                        }
                    }
                    
                    // process images            
                    foreach ($tags as $tag) {
                        if ($tag->type()=='image' && ($tag->width() || $tag->height()) && isset($file_paths[$tag->id()])) {
                            $PerchImage = new PerchImage;
                            if ($tag->quality()) $PerchImage->set_quality($tag->quality());
                            $PerchImage->resize_image($file_paths[$tag->id()], $tag->width(), $tag->height(), $tag->crop());
                        }
                    }
                }
            }
            
            // Post process region (options etc)
            list($form_vars, $processed_vars) = $ContentItem->post_process_region($form_vars, $processed_vars, $options);
            
            
            // Update
        	$json = PerchUtil::json_safe_encode($form_vars);
            
            $data = array();
            
            $data['contentNew']     = 0;
            $data['contentHistory'] = $ContentItem->push_to_history($form_vars); // $json before encoding
            
            if (isset($_POST['save_as_draft'])) {
                $ContentItem->set_options(array('draft'=>'true'));   
                
                $Alert->set('success', PerchLang::get('Draft successfully updated'));     
            }else{
                $ContentItem->set_options(array('draft'=>'false'));
                $data['contentHTML']    = $Template->render_group($processed_vars, true);
                $data['contentJSON']    = $json;
                
                $Alert->set('success', PerchLang::get('Content successfully updated'));
            }
            
            $ContentItem->update($data);    	
        	
        	if ($ContentItem->contentMultiple()=='1' && isset($_POST['add_another'])) {
        	    $ContentItem->add_item();
                list($details, $items, $history) = $ContentItem->refresh_details();
                
                // Clear $_POST, as field numbers have all changed.
                $_POST = array();
                $Form->reset();
        	}
        	
        	if (array_key_exists('sortField', $options) && $options['sortField']!='') {
        	    // Clear $_POST, as field numbers have all changed.
                $_POST = array();
                $Form->reset();
        	}
        	
        	
        	if ($_FILES) { 
        	    foreach($_FILES as $file) {
        	        if ($file['error']!=UPLOAD_ERR_NO_FILE && $file['error']!=UPLOAD_ERR_OK) {
        	            $Alert->set('failure', PerchLang::get('File failed to upload'));
        	        }
        	    }
        	}
        	
        	$ContentItem->clean_up_resources();
        	
        }
        
        list($details, $items, $history) = $ContentItem->refresh_details();
        
        
        if (PerchUtil::count($details)) {
            $details_flat = array();
            $i = 0;
            foreach($details as $detail) {
                if (PerchUtil::count($detail)) {
                    foreach($detail as $key=>$val) {
                        $details_flat['perch_'.$i.'_'.$key] = $val;
                    }
                    $i++;
                }
            }
            $details = $details_flat;
        }
    }
    
    if (!$image_folder_writable) {
        $Alert->set('failure', PerchLang::get('Your resources folder is not writable. Make this folder (') . PerchUtil::html(PERCH_RESPATH) . PerchLang::get(') writable if you want to upload files and images.'));
    }
    
    // is it a draft?
    if ($ContentItem->get_option('draft')) {
        $draft = true;
        
        if ($ContentItem->contentPage() == '*') {
            $Alert->set('draft', PerchLang::get('You are editing a draft.'));
        }else{
            $path = rtrim($Settings->get('siteURL')->settingValue(), '/');
            $Alert->set('draft', PerchLang::get('You are editing a draft.') . ' <a href="'.PerchUtil::html($path.$ContentItem->contentPage()).'?preview=all" class="draft-preview">'.PerchLang::get('Preview').'</a>');
        }
        
        
    }else{
        $draft = false;
    }
    
    
    /* ---------- EDITOR PLUGINS ----------- */
    
    if ($ContentItem->contentTemplate() && is_array($tags)) {
        $seen_editors = array();
        foreach($tags as $tag) {
            if ($tag->editor() && !in_array($tag->editor(), $seen_editors)) {
                $dir = PERCH_PATH.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'editors'.DIRECTORY_SEPARATOR.$tag->editor();
                if (is_dir($dir) && is_file($dir.DIRECTORY_SEPARATOR.'_config.inc')) {
                    $Perch->add_head_content(str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents($dir.DIRECTORY_SEPARATOR.'_config.inc')));
                    $seen_editors[] = $tag->editor();
                }else{
                    $Alert->set('failure', PerchLang::get('Editor requested, but not installed: '.$tag->editor()));
                }
            }
        }
    }


    $Perch->add_javascript(PERCH_LOGINPATH.'/assets/js/maps.js');
?>