<div id="h1">
    <h1><?php echo PerchLang::get('Users'); ?></h1>
    <?php echo $help_html; ?>

</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    <p><?php echo PerchLang::get('Are you sure you wish to delete this user?'); ?></p>
</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>

    <p><?php printf(PerchLang::get('Are you sure you wish to delete %s?'), PerchUtil::html($User->userGivenName() . ' ' . $User->userFamilyName())); ?></p>

    <form action="<?php echo PerchUtil::html($Form->action()); ?>" method="post" class="sectioned">
		
		<p class="submit">
			<?php 		
				echo $Form->submit('submit', 'Delete user', 'button');
			
			    echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'/users">' . PerchLang::get('Cancel'). '</a>'; 
			?>
			
		</p>
	</form>

    <div class="clear"></div>
</div>