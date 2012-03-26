<?php
    include(dirname(__FILE__) . '/../../config/config.php');
    include(PERCH_PATH . '/inc/loader.php');
    $Perch  = PerchAdmin::fetch();
    include(PERCH_PATH . '/inc/auth.php');


    
    
    $Perch->page_title = PerchLang::get('Perch Help');
    $Alert = new PerchAlert;
    
    
    include(PERCH_PATH . '/inc/top.php');
?>


    <div id="h1">
        <h1><?php echo PerchLang::get('Help / Markdown'); ?></h1>
    </div>


    <div id="side-panel">
        <?php include('../nav.php'); ?>
    </div>

    <div id="main-panel" class="help">
        <?php echo $Alert->output(); ?>

    <p>Markdown is a simple syntax to mark-up text in your pages. It is enabled on any field that displays the Markdown link.</p>

       <h3>Phrase modifiers:</h3>
	<p>
	<em>*emphasis*</em><br />
	<strong>**bold**</strong><br />
	
	</p>

	<h3>Block modifiers:</h3>
	<p>
	<b>#</b> Level 1 heading<br />
	<b>##</b> Level 2 heading<br />
	<b>###</b> Level 3 heading<br />
	<b>####</b> Level 4 heading<br />
	<b>&gt;</b> Blockquote<br />

	
	<b>-</b> Numeric list<br />
	<b>1.</b> Bulleted list<br />

	</p>

	<h3>Links:</h3>
	<p>
	[linktext]:(http://&#8230;)<br />
	</p>

	
        
        <div class="clear"></div>
    </div>



<?php
    include(PERCH_PATH . '/inc/btm.php');
?>
