<div id="h1">
    <h1><?php 
            echo PerchLang::get('Content') . ' / ';
            printf(PerchLang::get('Editing %s Region'),' &#8216;' . PerchUtil::html($ContentItem->contentKey()) . '&#8217; '); 
        ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">

    
    <h3 class="em"><span><?php echo PerchLang::get('About this region'); ?></span></h3>
    <p>
        <?php 
            if ($ContentItem->contentMultiple()=='1') {
                echo PerchLang::get("This region may contain one or more items.");
            }else{
                echo PerchLang::get("This region only has a single item.");
            }
            
            echo ' '. PerchLang::get("Required fields are marked with an asterisk *.");
        ?>
    </p>
    

<?php

    if (PerchUtil::count($history)>1) {
        echo '<form method="post" action="'.PerchUtil::debug($fUndo->action()).'">';
        echo '<p>'.$fUndo->submit('btnUndo', 'Undo', 'button undo', true, true).'</p>';
        echo '</form>';
    }


    if ($ContentItem->contentMultiple()=='1') {
        if ($items>1) echo '<div id="content-reorder" data-id="'.PerchUtil::html($ContentItem->id()).'">';
        echo '<h4>'  . PerchLang::get('Items');
            if ($items>1) echo '<span class="buttons"><a class="reorder" href="reorder/?id='.PerchUtil::html($ContentItem->id()).'">'.PerchLang::get('Reorder').'</a></span>';
        echo '</h4>';
        echo '<ul>';
        for($i=0; $i<$items; $i++) {
            if (isset($details['perch_'.$i.'__title'])) {
                echo '<li data-idx="'.$i.'"><a href="#item'.($i+1).'">' . PerchUtil::html($details['perch_'.$i.'__title']) . '</a></li>';
            }else{
                echo '<li data-idx="'.$i.'"><a href="#item'.($i+1).'">'.PerchLang::get('Item') . ' ' . ($i+1) . '</a></li>';
            }
        }
        echo '</ul>';
        
        if ($items>1) echo '</div>';
        
        if (array_key_exists('limit', $options) && $options['limit']!=false) {
            echo '<p>';
            printf(PerchLang::get('This region is configured to display the first <strong>%s items only</strong>.'), PerchUtil::html($options['limit']));
            echo '</p>';
        }

    }




    if ($ContentItem->contentTemplate() != '') {

        if ($CurrentUser->userRole()=='Admin') {
            echo '<h4>'.PerchLang::get('Options').'</h4>';
        }else{
            echo '<h4>' . PerchLang::get('Page assignment') . '</h4>';
        }

        if ($ContentItem->contentPage() == '*') {
            echo '<p>' . PerchLang::get('This region is shared across all pages.') . '</p>';
        }else{
            echo '<p>' . PerchLang::get('This region is only available within') . ':</p><p><code><a href="' . PerchUtil::html($ContentItem->contentPage()) . '">' . PerchUtil::html($ContentItem->contentPage()) . '</a></code></p>';
        }

        if ($CurrentUser->userRole()=='Admin') {
            echo '<p>';
            echo ' <a href="'.PERCH_LOGINPATH . '/apps/content/options/?id='.PerchUtil::html($id).'">' . PerchLang::get('Set your options for this region.') . '</a></p>';
        }
        
        
    }
    
    


    
    

?>
</div>

<div id="main-panel"<?php if ($place_token_on_main) echo 'data-token="'.PerchUtil::html($place_token_on_main->get_token()).'"'; ?>>
    <?php echo $Alert->output(); ?>

<?php
    
    /*  ------------------------------------ DEFINE TEMPLATE ----------------------------------  */

    if ($ContentItem->contentTemplate() == '') {
?>
        <p><?php echo PerchLang::get('Please choose a template for the content you wish to add to this region.'); ?></p>
        <p><?php echo PerchLang::get('If you would like to have multiple items of content in this region, select the <em>Allow multiple items</em> option.'); ?></p>
        

        <form method="post" action="<?php echo PerchUtil::html($fTemplate->action()); ?>">

                
                <div class="field">
                    <?php echo $fTemplate->label('contentTemplate', 'Template'); ?>
                    <?php
                        $opts = array();
                        $templates = $PerchContent->get_templates();
                       
                        if (is_array($templates)) {
                            foreach($templates as $template) {
                                $opts[] = array('label'=>$template['label'], 'value'=>$template['filename']);
                            }
                        }
                        
                        echo $fTemplate->select('contentTemplate', $opts, $fTemplate->get('contentTemplate', @false));
                    ?>
                </div>
            
                <div class="field">
                    <?php echo $fTemplate->label('contentMultiple', 'Allow multiple items'); ?>
                    <?php echo $fTemplate->checkbox('contentMultiple', '1', '0'); ?>
                </div>
            

            <p class="submit">
                <?php echo $fTemplate->submit('btnsubmit', 'Submit', 'button'); ?>
            </p>
                
        </form>




<?php
    }else{
        
    
    
    
    
    /*  ------------------------------------ EDIT CONTENT ----------------------------------  */
 
 
    if ($template_help_html) {
        echo '<h2><span>' . PerchLang::get('Help') .'</span></h2>';
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }

    
    
?>
    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" <?php echo $Form->enctype(); ?> id="content-edit" class="sectioned">
        <div class="items">
<?php

        if (is_array($tags)) {
            
            // loop through each item (usually one, sometimes more)
            for($i=0; $i<$items; $i++) {
                
                echo '<div class="edititem">';
                if ($ContentItem->contentMultiple()) {
                    echo '<div class="h2" id="item'.($i+1).'">';
                        if (isset($details['perch_'.$i.'__title'])) {
                            echo '<h2 class="em">'. PerchUtil::html($details['perch_'.$i.'__title']) .'</h2>';
                        }else{
                            echo '<h2 class="em">'. PerchLang::get('Item'). ' ' . ($i+1) .'</h2>';
                        }
                        
                        echo '<a href="'.PERCH_LOGINPATH.'/apps/content/delete-item/?id='.PerchUtil::html($ContentItem->id()).'&amp;idx='.$i.'" class="delete inline-delete">'.PerchLang::get('Delete').'</a>';
                    echo '</div>';
                }else{
                    echo '<h2 class="em">'. PerchUtil::html($ContentItem->contentKey()).'</h2>';
                }
                $seen_tags = array();
            
                foreach($tags as $tag) {
                    
                    $item_id = 'perch_'.$i.'_'.$tag->id();
                    
                    if (!in_array($tag->id(), $seen_tags) && $tag->type()!='hidden' && $tag->type()!='slug') {
                        echo '<div class="field '.$Form->error($item_id, false).'">';
                        
                        $label_text  = PerchUtil::html($tag->label());
                        if ($tag->type() == 'textarea') {
                            if (PerchUtil::bool_val($tag->textile()) == true) {
                                $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/help/textile" class="assist">Textile</a></span>';
                            }
                            if (PerchUtil::bool_val($tag->markdown()) == true) {
                                $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/help/markdown" class="assist">Markdown</a></span>';
                            }
                        }
                        $Form->disable_html_encoding();
                        echo $Form->label($item_id, $label_text, '', false, false);
                        $Form->enable_html_encoding();
                
                            switch ($tag->type()) {
                                case 'text':
                                    echo $Form->text($item_id, $Form->get($details, $item_id, $tag->default()), false, $tag->maxlength());
                                    break;
                                
                                case 'url':
                                    echo $Form->url($item_id, $Form->get($details, $item_id, $tag->default()), false, $tag->maxlength());
                                    break;
                                
                                case 'email':
                                    echo $Form->email($item_id, $Form->get($details, $item_id, $tag->default()), false, $tag->maxlength());
                                    break;
                        
                                case 'textarea':
                                    $classname = 'large ';
                                    if ($tag->editor()) $classname .= $tag->editor();
                                    if ($tag->textile()) $classname .= ' textile';
                                    if ($tag->markdown()) $classname .= ' markdown';
                                    if ($tag->size()) $classname .= ' '.$tag->size();
                                    if (!$tag->textile() && !$tag->markdown() && $tag->html()) $classname .= ' html';
                                    
                                    $data_atrs = array();
                                    if ($tag->imagewidth()) $data_atrs['width'] = $tag->imagewidth();
                                    if ($tag->imageheight()) $data_atrs['height'] = $tag->imageheight();
                                    if ($tag->imagecrop()) $data_atrs['crop'] = $tag->imagecrop();
                                    if ($tag->imageclasses()) $data_atrs['classes'] = $tag->imageclasses();
                                
                                    echo $Form->textarea($item_id, $Form->get($details, $item_id, $tag->default()), $classname, $data_atrs);
                                    echo '<div class="clear"></div>';
                                    break;
                                    
                                case 'checkbox':
                                    $val = ($tag->value() ? $tag->value() : '1');
                                    echo $Form->checkbox($item_id, $val, $Form->get($details, $item_id, $tag->default()));
                                    break;
                            
                                case 'date':
                                    if ($tag->time()) {
                                        echo $Form->datetimepicker($item_id, $Form->get($details, $item_id, $tag->default()));
                                    }else{
                                        echo $Form->datepicker($item_id, $Form->get($details, $item_id, $tag->default()));
                                    }
                                    break;
                            
                                case 'select':
                                    $options = explode(',', $tag->options());
                                    $opts = array();
                                    if (PerchUtil::bool_val($tag->allowempty())== true) {
                                        $opts[] = array('label'=>'', 'value'=>'');
                                    }
                                    if (PerchUtil::count($options) > 0) {
                                        foreach($options as $option) {
                                            $val = trim($option);
                                            $label = $val;
                                            if (strpos($val, '|')!==false) {
                                                $parts = explode('|', $val);
                                                $label = $parts[0];
                                                $val   = $parts[1];
                                            }
                                            $opts[] = array('label'=>$label, 'value'=>$val);
                                        }
                                    }
                                    echo $Form->select($item_id, $opts, $Form->get($details, $item_id, $tag->default()));
                                    break;
                            
                                case 'radio':
                                    $options = explode(',', $tag->options());
                                    if (PerchUtil::count($options) > 0) {
                                        $k = 0;
                                        foreach($options as $option) {
                                            $val    = trim($option);
                                            $label  = $val;
                                            if (strpos($val, '|')!==false) {
                                                $parts = explode('|', $val);
                                                $label = $parts[0];
                                                $val   = $parts[1];
                                            }
                                            $id  = $item_id . $k;
                                            echo '<span class="radio">';
                                            echo $Form->radio($id, $item_id, $val, $Form->get($details, $item_id, $tag->default()));
                                            $Form->disable_html_encoding();
                                            echo $Form->label($id, $label, 'radio', false, false);
                                            $Form->enable_html_encoding();
                                            echo '</span>';
                                            $k++;
                                        }
                                    }
                                    
                                    break;
                                
                                case 'image':
                                    $PerchImage = new PerchImage;
                                    echo $Form->image($item_id);
                                    if (isset($details[$item_id]) && $details[$item_id]!='') {
                                        $image_src = $PerchImage->get_resized_filename($details[$item_id], 150, 150, 'thumb');
                                        $image_path = str_replace(PERCH_RESPATH, PERCH_RESFILEPATH, $image_src);
                                        if (file_exists($image_path)) {
                                            echo '<img class="preview" src="'.PerchUtil::html($image_src).'" alt="Preview" />';
                                            echo '<div class="remove">';
                                            echo $Form->checkbox($item_id.'_remove', '1', 0).' '.$Form->label($item_id.'_remove', PerchLang::get('Remove image'), 'inline');
                                            echo '</div>';
                                        }
                                    }
                                    break;
                                case 'file':
                                    echo $Form->image($item_id);
                                    if (isset($details[$item_id]) && $details[$item_id]!='') {
                                        echo '<div class="file">'.PerchUtil::html(str_replace(PERCH_RESPATH.'/', '', $details[$item_id])).'</div>';
                                        echo '<div class="remove">';
                                        echo $Form->checkbox($item_id.'_remove', '1', 0).' '.$Form->label($item_id.'_remove', PerchLang::get('Remove file'), 'inline');
                                        echo '</div>';
                                    }
                                    break;
                                    
                                case 'map':
                                    echo $Form->text($item_id.'_adr', $Form->get((isset($details[$item_id])? $details[$item_id] : array()), 'adr', $tag->default()), 'map_adr');                            
                                    echo '<div class="map" data-btn-label="'.PerchLang::get('Find').'" data-mapid="'.PerchUtil::html($item_id).'" data-width="'.($tag->width() ? $tag->width() : '460').'" data-height="'.($tag->height() ? $tag->height() : '320').'">';
                                        if (isset($details[$item_id]['admin_html'])) {
                                            echo $details[$item_id]['admin_html'];
                                            echo $Form->hidden($item_id.'_lat', $details[$item_id]['lat']);
                                            echo $Form->hidden($item_id.'_lng', $details[$item_id]['lng']);
                                            echo $Form->hidden($item_id.'_clat', $details[$item_id]['clat']);
                                            echo $Form->hidden($item_id.'_clng', $details[$item_id]['clng']);
                                            echo $Form->hidden($item_id.'_type', $details[$item_id]['type']);
                                            echo $Form->hidden($item_id.'_zoom', $details[$item_id]['zoom']);
                                        }
                                    echo '</div>';

                                    
                                    
                                    $has_map = true;
                                    break;
                            
                                default:
                                    echo $Form->text($item_id, $Form->get($details, $item_id, $tag->default()));
                                    break;
                            }
                            
                        if ($tag->help()) {
                            echo $Form->hint($tag->help());
                        }
                        
                        
                
                        echo '</div>';
                
                        $seen_tags[] = $tag->id();
                    }
                }
                
                if (isset($details['perch_'.$i.'__id'])) {
                    $_id = $details['perch_'.$i.'__id'];
                }else{
                    $_id = '__new__';
                }
                echo $Form->hidden('perch_'.$i.'__id', $_id);
                
                echo '</div>';
            }
        }
?>        
        </div>
        <p class="submit<?php if (defined('PERCH_NONSTICK_BUTTONS') && PERCH_NONSTICK_BUTTONS) echo ' nonstick'; ?><?php if ($Form->error) echo ' error'; ?>">
            <?php 
                echo $Form->submit('btnsubmit', 'Save', 'button'); 
                
                if ($ContentItem->contentMultiple()=='1') {
                    echo '<input type="submit" name="add_another" value="'.PerchUtil::html(PerchLang::get('Save & Add another')).'" id="add_another" class="button" />';
                }
                
                echo '<label class="save-as-draft" for="save_as_draft"><input type="checkbox" name="save_as_draft" value="1" id="save_as_draft" '.($draft?'checked="checked"':'').'  /> '.PerchUtil::html(PerchLang::get('Save as Draft')).'</label>';
                
                echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'/apps/content">' . PerchLang::get('Cancel'). '</a>'; 
                
                
            ?>
        </p>
        
    </form>

<?php
        
        
        
        
    }

?>    
    
    
    
    <div class="clear"></div>
</div>
<?php if ($has_map) { ?><script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script><?php } ?>