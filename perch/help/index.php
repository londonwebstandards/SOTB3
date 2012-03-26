<?php
    include(dirname(__FILE__) . '/../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');


    
    
    $Perch->page_title = PerchLang::get('Perch Help');
    $Alert = new PerchAlert;
    
    
    include(PERCH_PATH . '/inc/top.php');
?>


    <div id="h1">
        <h1><?php echo PerchLang::get('Help') . ' / ' . PerchLang::get('Introduction'); ?></h1>
    </div>


    <div id="side-panel">
        <?php include('nav.php'); ?>
    </div>

    <div id="main-panel" class="help">
        <?php echo $Alert->output(); ?>

<?php
    if ($CurrentUser->userRole() == 'Admin') {
        echo '<p class="alert-notice">For help configuring Perch and writing templates, visit the <a href="http://docs.grabaperch.com/">online documentation</a>.</p>';
    }

?>
        <p>Welcome to the help page for your content administration panel.</p>

        <h3 id="getstarted">Editing Content on your website</h3>
        <p>
            To get started editing content click the Content link in the header of the administration section. This page displays all of the available editable regions on pages across your website. If this list is too long you can use the options in the sidebar to only display content for one page or to display content by type - for example displaying only text blocks across the site.
        </p>
        
        <h3 id="regions">Edit a region</h3>
        
        <p>To edit any region click the region name in the main content list. You can then complete the form changing the content as required. Click the Save button to make the change and the content on your website will be immediately updated.</p>
        
        <h3 id="multiple">Regions that allow multiple blocks of content</h3>
        <p>Some regions will allow multiple blocks of content. These can be ordered with the newest block posted at the top or at the bottom of the content. Blocks may also be deleted.</p>
        
        <div class="clear"></div>
    </div>



<?php
    include(PERCH_PATH . '/inc/btm.php');
?>

