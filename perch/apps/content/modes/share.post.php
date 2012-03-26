<div id="h1">
    <h1><?php echo PerchLang::get('Content'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p>
        <?php echo PerchLang::get("Share this region to use it within multiple pages."); ?>
    </p>
</div>

<div id="main-panel">
    
    <?php echo PerchLang::get("When you share this region, the content becomes available on any page that uses a region with the same name. Would you like to share the region?"); ?>
    
    <form method="post" action="<?php echo PerchUtil::html($fGlobalise->action()); ?>" class="sectioned">

        <p class="submit">
            <?php echo $fGlobalise->submit('btnsubmit', 'Share this region', 'button'), ' ', PerchLang::get('or'), ' <a href="',PERCH_LOGINPATH . '/apps/content/edit/?id='.PerchUtil::html($id).'', '">', PerchLang::get('Cancel'), '</a>'; ?>
        </p>
        
    </form>
    
    <div class="clear"></div>
</div>