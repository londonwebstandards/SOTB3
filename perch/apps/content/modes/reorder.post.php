<div id="h1">
    <h1><?php 
            echo PerchLang::get('Content') . ' / ';
            printf(PerchLang::get('Reorder %s Region'),' &#8216;' . PerchUtil::html($ContentItem->contentKey()) . '&#8217; '); 
        ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p>
        <?php echo PerchLang::get("Change the order by moving items up or down"); ?>
    </p>
    <p>
        <?php printf(PerchLang::get("%sReturn to editing your content%s."), '<a href="'.PERCH_LOGINPATH.'/apps/content/edit/?id='.PerchUtil::html($ContentItem->id()).'">', '</a>'); ?>
    </p>
</div>

<div id="main-panel">
    
    <?php
        if (PerchUtil::count($items)) {
    ?>
        <table class="d">
            <thead>
                <tr>
                    <th class="first"><?php echo PerchLang::get('Title'); ?></th>
                    <th class="action"></th>
                    <th class="action last"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i = 0;
                    $last = PerchUtil::count($items)-1;
                    foreach($items as $item) {
                        
                        echo '<tr>';
                            if (isset($item['_title'])) {
                                echo '<th>'.$item['_title'].'</th>';
                            }else{
                                echo '<th>'.PerchLang::get('Item').' '.($i+1).' (ID: '.$item['_id'].')'.'</th>';
                            }
                        
                            
                            if ($i==0) {
                                echo '<td></td>';
                            }else{
                                echo '<td>';
                                echo '<form method="post" action="'.PerchUtil::html($Form->action()).'" class="sectioned">'.$Form->hidden('up', $item['_id'], true).$Form->submit('btnup'.$item['_id'], 'Move up', 'reorderbtn').'</form>';
                                echo '</td>';
                            }
                            
                            if ($i==$last) {
                                echo '<td></td>';
                            }else{
                                echo '<td>';
                                echo '<form method="post" action="'.PerchUtil::html($Form->action()).'" class="sectioned">'.$Form->hidden('down', $item['_id'], true).$Form->submit('btndown'.$item['_id'], 'Move down', 'reorderbtn').'</form>';
                                echo '</td>';
                            }
                            
                            
                        echo '</tr>';
                        
                        $i++;
                    }
                ?>
            </tbody>
        </table>
    <?php
            
        }
    
    ?>
    
    <div class="clear"></div>
</div>