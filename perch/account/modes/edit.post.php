<div id="h1">
    <h1><?php echo PerchLang::get('My Account'); ?></h1>
<?php echo $help_html; ?>
    
</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    
    <p><?php echo PerchLang::get('You may update your personal details, email address and password here. If you do not wish to change your password, just leave those fields blank.'); ?></p>
    
    <?php
        if ($CurrentUser->userRole() == 'Admin') {
    ?>
    <h3><span><?php echo PerchLang::get('Administrators'); ?></span></h3>
    <p><?php echo PerchLang::get('You may manage all users and reset passwords via the Users section.'); ?></p>
    <?php
        }
    ?>
</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>

    <form action="<?php echo PerchUtil::html($Form->action()); ?>" method="post" class="sectioned">
		
		<h2 class="em"><?php echo PerchUtil::html(PerchLang::get('Details')); ?></h2>
		
        <fieldset>
            <legend><?php echo PerchUtil::html(PerchLang::get('Details')); ?></legend>
        
            <div class="field <?php echo $Form->error('userGivenName', false);?>">
                <?php echo $Form->label('userGivenName', 'First name'); ?>
                <?php echo $Form->text('userGivenName', $Form->get($details, 'userGivenName'), ''); ?>
            </div>

        	<div class="field <?php echo $Form->error('userFamilyName', false);?>">
        		<?php echo $Form->label('userFamilyName', 'Last name'); ?>
        		<?php echo $Form->text('userFamilyName', $Form->get($details, 'userFamilyName'), ''); ?>
        	</div>

        	<div class="field last<?php echo $Form->error('userEmail', false);?>">
        		<?php echo $Form->label('userEmail', 'Email'); ?>
        		<?php echo $Form->email('userEmail', $Form->get($details, 'userEmail'), ''); ?>
        	</div>
        </fieldset>
        
        <h2><?php echo PerchUtil::html(PerchLang::get('Change password')); ?></h2>
        
        <fieldset>
            <legend><?php echo PerchUtil::html(PerchLang::get('Change password')); ?></legend>
            
            <div class="field">
                <?php echo $Form->label('userPassword', 'New password'); ?>
                <?php echo $Form->password('userPassword', $Form->get(false, 'userPassword')); ?>
            </div>              
            <div class="field">
                <?php echo $Form->label('userPassword2', 'Repeat'); ?>
                <?php echo $Form->password('userPassword2', ''); ?>
            </div>      
        </fieldset>

		<p class="submit">
			<?php 		
				echo $Form->submit('submit', 'Save changes', 'button');
			    echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'">' . PerchLang::get('Cancel'). '</a>';
			?>
		</p>
	</form>

    <div class="clear"></div>
</div>