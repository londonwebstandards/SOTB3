<div id="h1">
    <h1><?php echo PerchLang::get('Content') . ' / ' . PerchLang::get('Options'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p>
        <?php printf(PerchLang::get("Set options for the region here, or %s return to editing your content.%s"), '<a href="'.PERCH_LOGINPATH.'/apps/content/edit/?id='.PerchUtil::html($id).'">', '</a>'); ?>
    </p>

    <h4><span><?php echo PerchLang::get('Search result URL'); ?></span></h4>
    <p>
        <?php echo PerchLang::get('It\'s sometimes useful to use a different URL in search results.'); ?>
    </p>
    <p>
        <?php printf(PerchLang::get('If you need this, enter the root-relative URL using %sbraces%s around any dynamic fields. e.g.'), '{', '}'); ?>
    </p>
    <p>
        <code><?php  printf(PerchLang::get('/news-article.php?s=%sslug%s'), '{','}'); ?></code>
    </p>

</div>

<div id="main-panel">

    <?php echo $Alert->output(); ?>

    <p><?php echo PerchLang::get('You can set options for this region, including whether to allow one or multiple items, and the sort order.'); ?></p>
    
        
    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="">
        
        
        <div class="field">
            <?php echo $Form->label('contentShared', 'Share across all pages'); ?>
            <?php
                if ($ContentItem->contentPage() == '*') {
                    $tmp = array('contentShared'=>'1');
                }else{
                    $tmp = array('contentShared'=>'0');
                }
                echo $Form->checkbox('contentShared', '1', $Form->get($tmp, 'contentShared', 0)); ?>
        </div>
        
        <div class="field">
            <?php echo $Form->label('contentMultiple', 'Allow multiple items'); ?>
            <?php echo $Form->checkbox('contentMultiple', '1', $Form->get(array('contentMultiple'=>$ContentItem->contentMultiple()), 'contentMultiple', 0)); ?>
        </div>

    <?php if ($ContentItem->contentMultiple()=='1') { ?>
        <div class="field">
            <?php echo $Form->label('contentAddToTop', 'New items are'); ?>
            <?php
                $opts = array();
                $opts[] = array('label'=>PerchLang::get('Added to the top'), 'value'=>1);
                $opts[] = array('label'=>PerchLang::get('Added to the bottom'), 'value'=>0);
                echo $Form->select('contentAddToTop', $opts, $Form->get(array('contentAddToTop'=>$ContentItem->contentAddToTop()), 'contentAddToTop', 0));
            ?>
        </div>


        <div class="field">
            <?php echo $Form->label('sortField', 'Sort by'); ?>
            <?php
                $Template = new PerchTemplate('/templates/content/'.$ContentItem->contentTemplate(), 'content');
                $tags   = $Template->find_all_tags('content');
                $seen_tags = array();
                $opts = array();
                $opts[] = array('label'=>PerchLang::get('Default order'), 'value'=>'');
                if (PerchUtil::count($tags)) {
                    foreach($tags as $Tag) {
                        if (!in_array($Tag->id(), $seen_tags)) {
                            $opts[] = array('label'=>$Tag->label(), 'value'=>$Tag->id());
                            $seen_tags[] = $Tag->id();
                        }
                        
                    }
                }
                echo $Form->select('sortField', $opts, $Form->get($options, 'sortField'));
            
            ?>
        </div>
        
        <div class="field">
                <?php echo $Form->label('sortOrder', 'Sort order'); ?>
                <?php
                    $opts = array();
                    $opts[] = array('label'=>PerchLang::get('Ascending (A-Z, oldest to newest)'), 'value'=>'ASC');
                    $opts[] = array('label'=>PerchLang::get('Descending (Z-A, newest to oldest)'), 'value'=>'DESC');
                    echo $Form->select('sortOrder', $opts, $Form->get($options, 'sortOrder'));
                ?>
        </div>


        <div class="field">
                <?php echo $Form->label('limit', 'Number of items to display'); ?>
                <?php
                    echo $Form->text('limit', $Form->get($options, 'limit'), 'small');
                    echo $Form->hint(PerchLang::get('Leave blank to display all items'));
                ?>
        </div>
    <?php } ?>
    
        <div class="field">
            <?php echo $Form->label('adminOnly', 'Hide region from Editors'); ?>
            <?php echo $Form->checkbox('adminOnly', '1', $Form->get($options, 'adminOnly', '0')); ?>
        </div>

        <div class="field">
            <?php echo $Form->label('contentSearchable', 'Include in search results'); ?>
            <?php
                $tmp = array('contentSearchable'=>$ContentItem->contentSearchable());
                echo $Form->checkbox('contentSearchable', '1', $Form->get($tmp, 'contentSearchable', 1)); ?>
        </div>

        <div class="field">
            <?php echo $Form->label('searchURL', 'URL for search results'); ?>
            <?php echo $Form->text('searchURL', $Form->get($options, 'searchURL', '')); ?>
        </div>


        <p class="submit">
            <?php echo $Form->submit('btnsubmit', 'Save', 'button'), ' ', PerchLang::get('or'), ' <a href="',PERCH_LOGINPATH . '/apps/content/edit/?id='.PerchUtil::html($id).'', '">', PerchLang::get('Cancel'), '</a>'; ?>
        </p>
    </form>
    
    <div class="clear"></div>
</div>