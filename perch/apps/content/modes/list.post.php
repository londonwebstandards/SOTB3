<div id="h1">
    <h1><?php echo PerchLang::get('Content'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3 class="em">
        <span>
            <?php echo PerchLang::get('Filter'); ?>
            <span class="filter">
                <a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/apps/content/'); ?>?by=all" class="filter-all <?php if ($filter=='all') echo 'selected'?>"><?php echo PerchUtil::html(PerchLang::get('All')); ?></a>
                <a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/apps/content/'); ?>?by=new" class="filter-new <?php if ($filter=='new') echo 'selected'?>"><?php echo PerchUtil::html(PerchLang::get('New')); ?></a>
            </span>
        </span>
    </h3>

    <h4><?php echo PerchLang::get('By page assignment'); ?></h4>
    <?php
        $pages = $PerchContent->get_pages();
        if (PerchUtil::count($pages) > 0) {
            $sorted_pages = array();
            foreach($pages as $page) {
                $sorted_pages[] = array('page'=>$page, 'label'=>PerchUtil::filename($page, true, true));
            }
            $sorted_pages = PerchUtil::array_sort($sorted_pages, 'label');
    ?>
    <ul>
        <?php
            foreach ($sorted_pages as $page) {
                $page = $page['page'];
                if ($page == '*') {
                    $label = PerchLang::get('Shared');
                }else{
                    $label = PerchUtil::filename($page);
                }
                echo '<li><a href="'.PerchUtil::html(PERCH_LOGINPATH.'/apps/content/?page='.urlencode($page)).'">' . PerchUtil::html($label) . '</a></li>';
            }
        ?>
    </ul>
    <?php
        }
    ?>

    <h4><?php echo PerchLang::get('By type'); ?></h4>
    <?php
        $templates = $PerchContent->get_templates();
        if (PerchUtil::count($templates) > 0) {
    ?>
    <ul>
        <?php
            foreach ($templates as $template) {
                echo '<li><a href="'.PerchUtil::html(PERCH_LOGINPATH.'/apps/content/?type='.urlencode(str_replace('.html','',$template['filename']))).'">' . PerchUtil::html($template['label']) . '</a></li>';
            }
        ?>
    </ul>
    <?php
        }
    ?>
</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>
    
    <?php
    if (PerchUtil::count($contentItems) > 0) {
    ?>
    <table class="d" id="content-list">
        <thead>
            <tr>
                <?php
                    if ($Settings->get('content_collapseList')->settingValue()) {
                        echo '<th class="toggle first"></th>';
                        echo '<th class="p">'. PerchLang::get('Page') . '</th>';
                    }else{
                        echo '<th class="first p">'. PerchLang::get('Page') . '</th>';
                    }
                ?>
                <th class="region"><?php echo PerchLang::get('Region'); ?></th>
                <th class="type"><?php echo PerchLang::get('Type'); ?></th>
                <th class="action"></th>
                <th class="action last"></th>
            </tr>
        </thead>
        <tbody>
        <?php
            if (PerchUtil::count($contentItems) > 0) {
                $prev = false;
                $prev_url = false;
                $level = 0;
                $prev_level = -1;
                foreach($contentItems as $item) {
                    
                    if ($CurrentUser->userRole() == 'Admin' || ($CurrentUser->userRole() == 'Editor' && $item->get_option('adminOnly')==false)) {
                        
                        $level = $item->displayLevel();
                        
                        if ($level>0) $level--;
                        
                        if ($prev != $item->formattedPage()) {
                            $ditto_page = false;
                            if (!PerchUtil::in_section($prev_url, $item->contentPage())) $prev_level = -1;
                        }
                        
                        
                        
                        # Display a row for any pages 'missing' from the tree. i.e. pages not managed in Perch
                        if ($level > $prev_level && ($level-$prev_level)>1) {
                            $diff = ($level-$prev_level-1);
                            $path_parts = explode('/', $item->contentPage());
                            $last = array_pop($path_parts);
                            if (substr($last, 0, 5)=='index') array_pop($path_parts);
                            $path_parts = array_slice($path_parts, -$diff);
                            
                            
                            
                            for($i=0; $i<$diff; $i++) {
                                echo '<tr class="'.PerchUtil::flip('odd').'" data-contentid="'.md5($path_parts[$i]).'">';
                                if ($Settings->get('content_collapseList')->settingValue()) echo '<td class="toggle"></td>';
                                echo '<td class="level'.($level-$diff+$i).' page inactive"><span>' . PerchUtil::html(PerchUtil::filename($path_parts[$i], false)) . '</span></td>';
                                echo '<td class="region"></td>';
                                echo '<td class="type"></td>';
                                echo '<td colspan="2"></td>';
                                echo '</tr>';
                            }
                        }
                        
                        
                        
                        echo '<tr class="p '.PerchUtil::flip('odd').'" data-contentid="'.PerchUtil::html($item->id()).'">';
                            if ($Settings->get('content_collapseList')->settingValue()) echo '<td class="toggle"></td>';
                            if ($prev != $item->formattedPage()) {

                                if ($item->get_option('draft')) {
                                    $draft = ' draft';
                                }else{
                                    $draft = '';
                                }
                                
                                if ($item->get_option('draft') || $PerchContent->page_has_drafts($item->contentPage(), $contentItems)) {
                                    $page_has_drafts = true;
                                }else{
                                    $page_has_drafts = false;
                                }
                                

                                if ($item->contentPage() == '*') {
                                    echo '<td class="level'.$level.' shared"><span>' . PerchLang::get('Shared') . '</span></td>';
                                }else{
                                    
                                    if ($page_has_drafts) {
                                        echo '<td class="level'.$level.' page draft"><span>' . PerchUtil::html(PerchUtil::filename($item->contentPage(), false)) . '</span></td>';
                                    }else{
                                        echo '<td class="level'.$level.' page"><span>' . PerchUtil::html(PerchUtil::filename($item->contentPage(), false)) . '</span></td>';
                                    }
                                    
                                    
                                }
  
                            }else{
                                echo '<td class="level'.($level+1).'"><span class="ditto">-</span></td>';
                            }
                            echo '<td class="region"><a href="'.PerchUtil::html(PERCH_LOGINPATH).'/apps/content/edit/?id=' . PerchUtil::html($item->id()) . '" class="edit">' . PerchUtil::html($item->contentKey()) . '</a>';
                            if ($item->get_option('draft')) echo '<span class="draft" title="'.PerchLang::get('This item is a draft.').'"></span>';
                            echo '</td>';       
                            echo '<td class="type">' . ($item->contentNew() ? '<span class="new">'.PerchLang::get('New').'</span>' : PerchUtil::html($PerchContent->template_display_name($item->contentTemplate()))) . '</td>';
                            
                            echo '<td>';
                                if ($page_has_drafts && !$ditto_page && $item->contentPage() != '*') {
                                    $path = rtrim($Settings->get('siteURL')->settingValue(), '/');
                                    echo '<a href="'.PerchUtil::html($path.$item->contentPage()).'?preview=all" class="draft-preview">'.PerchLang::get('Preview').'</a>';
                                }
                            echo '</td>';
                            
                            echo '<td>';
                            if ($CurrentUser->userRole() == 'Admin' || ($CurrentUser->userRole() == 'Editor' && $Settings->get('editorMayDeleteRegions')->settingValue())) {
                                echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH).'/apps/content/delete/?id=' . PerchUtil::html($item->id()) . '" class="delete inline-delete">'.PerchLang::get('Delete').'</a>';
                            }else{
                                echo '&nbsp;';
                            }
                            echo '</td>';
                        echo '</tr>';
                        $prev = $item->formattedPage();
                        $prev_url = $item->contentPage();
                        $prev_level = $level;
                        $ditto_page = true;
                    }
                }
                
            }
        
        ?>
        </tbody>
    </table>
    <?php
    }else{
    ?>
        <div class="info-panel">
        <?php if ($filter == 'all') { ?>
            <h2><?php echo PerchLang::get('No content yet?'); ?></h2>
            <p><?php echo PerchLang::get('Make sure you have added some Perch regions into your page, and then visited that page in your browser. Once you have, the regions should show up here.'); ?>
                <a href="http://grabaperch.com/go/gettingstarted"><?php echo PerchLang::get('Read the getting started guide to find out more'); ?>&hellip;</a>
            </p>
        <?php 
            } else {
        ?>
            <p class="alert-notice"><?php echo PerchLang::get('Sorry, there\'s currently no content available based on that filter'); ?> - <a href="?by=all"><?php echo PerchLang::get('View all'); ?></a></p>
        <?php
            }
        ?>
        </div>
    <?php    
    }
    ?>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    Perch.Apps.Content.settings = {
        'collapseList':<?php echo (PerchUtil::bool_val($do_list_collapse) ? 'true':'false'); ?>
    };
</script>