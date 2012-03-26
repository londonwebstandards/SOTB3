<div id="h1">
    <h1><?php echo PerchLang::get('Content'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p>
        <?php echo PerchLang::get("Set the position new items are created."); ?>
    </p>
</div>

<div id="main-panel">
    
    <?php echo PerchLang::get("When new items are added to this region, they can go in at the top or at the bottom. Which would you prefer?"); ?>
    
    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="sectioned">

        <div class="field">
            <?php echo $Form->label('contentAddToTop', 'New items are'); ?>
            <?php
                $opts = array();
                $opts[] = array('label'=>PerchLang::get('Added to the top'), 'value'=>1);
                $opts[] = array('label'=>PerchLang::get('Added to the bottom'), 'value'=>0);
                echo $Form->select('contentAddToTop', $opts, $Form->get(array('contentAddToTop'=>$ContentItem->contentAddToTop()), 'contentAddToTop', 0));
            ?>
        </div>

        <p class="submit">
            <?php echo $Form->submit('btnsubmit', 'Save', 'button'), ' ', PerchLang::get('or'), ' <a href="',PERCH_LOGINPATH . '/apps/content/edit/?id='.PerchUtil::html($id).'', '">', PerchLang::get('Cancel'), '</a>'; ?>
        </p>
    </form>
    
    <div class="clear"></div>
</div>