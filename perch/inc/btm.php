<?php
    if ($CurrentUser->logged_in()) {
        echo '</div>';
    }
?>
    <div id="footer">
        
        <?php 
            if (!$CurrentUser->logged_in()) { 
    	        echo '<div class="reset"><a href="'.PERCH_LOGINPATH.'/reset/">'.PerchLang::get('Reset password').'</a></div>';
    	    } 
    	?>
        
		<div class="credit">
            <?php
                if (!$Settings->get('hideBranding')->settingValue()) {
            ?>
			<p><a href="http://grabaperch.com"><img src="<?php echo PERCH_LOGINPATH; ?>/assets/img/perch.gif" width="35" height="12" alt="Perch" /></a>
			<?php echo PerchUtil::html(PerchLang::get('by')); ?> <a href="http://edgeofmyseat.com">edgeofmyseat.com</a></p>
            <?php
                }else{
                    echo '&nbsp;';
                }
        	?>
		</div>
    <?php  if ($CurrentUser->logged_in()) { ?>	
		<div class="version">
		    <?php
		        if (($CurrentUser->userRole() == 'Admin') && ($Perch->version < $Settings->get('latest_version')->settingValue())) {
		            echo '<a href="http://grabaperch.com/update">' . sprintf(PerchLang::get('You are running version %s - a newer version is available.'), $Perch->version) . '</a>';
		        }
		    
		    ?>
		</div>
	<?php  } ?>

	</div>
<?php
    if (!$CurrentUser->logged_in()) {
        echo '</div>';
    }
?>	
    <script type="text/javascript">
        Perch.Lang.init({
            'Apps':'<?php echo PerchLang::get('Apps'); ?>',
            'Save':'<?php echo PerchLang::get('Save'); ?>',
            'Undo':'<?php echo PerchLang::get('Undo'); ?>',
            'Image title':'<?php echo PerchLang::get('Image title'); ?>',
            'File title':'<?php echo PerchLang::get('File title'); ?>',
            'File to upload':'<?php echo PerchLang::get('File to upload'); ?>',
            'Style':'<?php echo PerchLang::get('Style'); ?>',
            'Upload':'<?php echo PerchLang::get('Upload'); ?>',
            'or':'<?php echo PerchLang::get('or'); ?>',
            'Cancel':'<?php echo PerchLang::get('Cancel'); ?>',
            'New':'<?php echo PerchLang::get('New'); ?>',
            'Mixed':'<?php echo PerchLang::get('Mixed'); ?>',
            'Delete this item?':'<?php echo PerchLang::get('Delete this item?'); ?>'
        });
        Perch.token = '<?php $CSRFForm = new PerchForm('csrf'); echo $CSRFForm->get_token(); ?>';
        Perch.path = '<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>';
    </script>
    <?php if (PERCH_DEBUG) PerchUtil::output_debug(); ?>
</body>
</html>
