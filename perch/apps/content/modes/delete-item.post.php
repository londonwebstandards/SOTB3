<div id="h1">
    <h1><?php echo PerchLang::get('Content'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p>
        <?php echo PerchLang::get("Delete this item of content from this page."); ?>
    </p>
</div>

<div id="main-panel">
    <p><?php 
        $display_page = PerchUtil::html(($ContentItem->contentPage() == '*' ? PerchLang::get('all pages.') : $ContentItem->contentPage()));
        printf(PerchLang::get('Are you sure you wish to delete this item in the %s region from %s?'), '<strong>'. PerchUtil::html($ContentItem->contentKey()). '</strong>', $display_page); ?>
    </p>

    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="sectioned">

        <p class="submit">
            <?php echo $Form->submit('btnsubmit', 'Delete', 'button'), ' ', PerchLang::get('or'), ' <a href="',PERCH_LOGINPATH . '/apps/content/edit/?id='.$ContentItem->id(), '">', PerchLang::get('Cancel'), '</a>'; ?>
        </p>
        
    </form>
    
    <div class="clear"></div>
</div>