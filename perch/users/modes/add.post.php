<div id="h1">
    <h1><?php echo PerchLang::get('Users'); ?></h1>
    <?php echo $help_html; ?>

</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Add User'); ?></span></h3>
    
    <p><?php echo PerchLang::get('You can add user accounts with administrative or editor privileges.'); ?></p>
    
</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>

    <h2>User details</h2>

    <form action="<?php echo PerchUtil::html($fCreateUser->action()); ?>" method="post" class="sectioned">
		
        <div class="field <?php echo $fCreateUser->error('userUsername', false);?>">
            <?php echo $fCreateUser->label('userUsername', 'Username'); ?>
            <?php echo $fCreateUser->text('userUsername', $fCreateUser->get(false, 'userUsername'), ''); ?>
        </div>
        
        <div class="field <?php echo $fCreateUser->error('userGivenName', false);?>">
            <?php echo $fCreateUser->label('userGivenName', 'First name'); ?>
            <?php echo $fCreateUser->text('userGivenName', $fCreateUser->get(false, 'userGivenName'), ''); ?>
        </div>
		
		<div class="field <?php echo $fCreateUser->error('userFamilyName', false);?>">
			<?php echo $fCreateUser->label('userFamilyName', 'Last name'); ?>
			<?php echo $fCreateUser->text('userFamilyName', $fCreateUser->get(false, 'userFamilyName'), ''); ?>
		</div>
		
		<div class="field <?php echo $fCreateUser->error('userEmail', false);?>">
			<?php echo $fCreateUser->label('userEmail', 'Email'); ?>
			<?php echo $fCreateUser->email('userEmail', $fCreateUser->get(false, 'userEmail'), ''); ?>
		</div>
		
		<div class="field <?php echo $fCreateUser->error('userPassword', false);?>">
			<?php echo $fCreateUser->label('userPassword', 'Password'); ?>
			<?php echo $fCreateUser->password('userPassword', $fCreateUser->get(false, 'userPassword'), ''); ?>
		</div>
		
		<div class="field <?php echo $fCreateUser->error('userRole', false);?>">
			<?php echo $fCreateUser->label('userRole', 'Role'); ?>
			<?php 
			    $opts = array();
			    $opts[] = array('label'=>PerchLang::get('Editor'), 'value'=>'Editor');
			    $opts[] = array('label'=>PerchLang::get('Admin'), 'value'=>'Admin');
			    echo $fCreateUser->select('userRole',$opts, $fCreateUser->get(false, 'userRole'), ''); ?>
		</div>

        <div class="field">
			<?php echo $fCreateUser->label('sendEmail', 'Send welcome email'); ?>
			<?php echo $fCreateUser->checkbox('sendEmail', '1', '1'); ?>
		</div>

		<p class="submit">
			<?php 		
				echo $fCreateUser->submit('submit', 'Create user', 'button');
				echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'/users">' . PerchLang::get('Cancel'). '</a>'; 
			?>
		</p>
	</form>


    <div class="clear"></div>
</div>