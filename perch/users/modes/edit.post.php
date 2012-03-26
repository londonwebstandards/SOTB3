<div id="h1">
    <h1><?php echo PerchLang::get('Users'); ?></h1>
<?php echo $help_html; ?>
    
</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Reset password'); ?></span></h3>
	<p><?php 
	    if ($User->id() == $CurrentUser->id()){
	        echo PerchLang::get('You may send yourself a new password by email.');
	    }else{
	      echo PerchLang::get('You may send this user a new password by email.');  
	    }
	?></p>
	<form method="post" action="<?php echo PerchUtil::html($fReset->action()); ?>">
	    <p class="submit">
	        <?php echo $fReset->submit('btnSubmit', 'Reset Password', 'button'); ?>
	    </p>
	</form>
</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>

    <h2><?php echo PerchLang::get('User details'); ?></h2>

    

    <form action="<?php echo PerchUtil::html($Form->action()); ?>" method="post" class="sectioned">
		
        <div class="field <?php echo $Form->error('userUsername', false);?>">
            <?php echo $Form->label('userUsername', 'Username'); ?>
            <?php echo $Form->text('userUsername', $Form->get($details, 'userUsername'), ''); ?>
        </div>
        
        <div class="field <?php echo $Form->error('userGivenName', false);?>">
            <?php echo $Form->label('userGivenName', 'First name'); ?>
            <?php echo $Form->text('userGivenName', $Form->get($details, 'userGivenName'), ''); ?>
        </div>
		
		<div class="field <?php echo $Form->error('userFamilyName', false);?>">
			<?php echo $Form->label('userFamilyName', 'Last name'); ?>
			<?php echo $Form->text('userFamilyName', $Form->get($details, 'userFamilyName'), ''); ?>
		</div>
		
		<div class="field <?php echo $Form->error('userEmail', false);?>">
			<?php echo $Form->label('userEmail', 'Email'); ?>
			<?php echo $Form->email('userEmail', $Form->get($details, 'userEmail'), ''); ?>
		</div>
		
		<?php if ($User->id() != $CurrentUser->id()){ ?>		
		<div class="field <?php echo $Form->error('userRole', false);?>">
			<?php echo $Form->label('userRole', 'Role'); ?>
			<?php 
			    $opts = array();
			    $opts[] = array('label'=>PerchLang::get('Editor'), 'value'=>'Editor');
			    $opts[] = array('label'=>PerchLang::get('Admin'), 'value'=>'Admin');
			    echo $Form->select('userRole',$opts, $Form->get($details, 'userRole'), ''); ?>
		</div>
        <?php } ?>

		<p class="submit">
			<?php 		
				echo $Form->submit('submit', 'Save changes', 'button');
				echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'/users">' . PerchLang::get('Cancel'). '</a>'; 
				
			?>
		</p>
	</form>

    <div class="clear"></div>
</div>