<div id="h1">
    <h1><?php echo PerchLang::get('Content'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p>
        <?php echo PerchLang::get("Delete the region from this page. Note that unless the tag is also removed from your page, the option to edit this region will reappear."); ?>
    </p>
</div>

<div id="main-panel">
      
    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="sectioned">

        <p><?php 
            $display_page = PerchUtil::html(($ContentItem->contentPage() == '*' ? PerchLang::get('all pages.') : $ContentItem->contentPage()));
            printf(PerchLang::get('Are you sure you wish to delete the region %s from %s?'), '<strong>'. PerchUtil::html($ContentItem->contentKey()). '</strong>', $display_page); ?>
        </p>
        
        
        
        <p class="submit">
            <?php echo $Form->submit('btnsubmit', 'Delete', 'button'), ' ', PerchLang::get('or'), ' <a href="',PERCH_LOGINPATH . '/apps/content', '">', PerchLang::get('Cancel'), '</a>'; ?>
        </p>
        
    </form>
    
    <div class="clear"></div>
</div>