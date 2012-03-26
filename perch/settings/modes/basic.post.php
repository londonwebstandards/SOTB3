<div id="h1">
    <h1><?php echo PerchLang::get('Settings'); ?></h1>
<?php echo $help_html; ?>
</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    
    
    <p><?php echo PerchLang::get('Colours can be set to any colour value acceptable in CSS, such as <code>#FFFFFF</code> or <code>white</code>.'); ?></p>
    <p><?php echo PerchLang::get('The default colour is white for the header, #333 for the links.'); ?></p>
    
    <p><?php echo PerchLang::get('The maximum recommended width for logos is 200px.'); ?></p>

    <h4><?php echo PerchLang::get('Diagnostics'); ?></h4>
    
    <p><?php printf(PerchLang::get('You can get a %s diagnostics report %s which can be useful if you require technical support.'), '<a href="'.PERCH_LOGINPATH.'/settings/diagnostics">', '</a>'); ?></p>

    <?php if (false && $CurrentUser->userRole() == 'Admin') {  ?>
    <h4><?php echo PerchLang::get('Apps'); ?></h4>
    
    <p><?php echo PerchLang::get('Apps are extensions to Perch that can be turned on and off as required.'); ?></p>
    
    <p><?php echo '<a href="'.PERCH_LOGINPATH.'/settings/apps">', PerchLang::get('Add/remove apps'), '</a>'; ?></p>
    <?php } ?>
</div>

<div id="main-panel">
    
    <?php
        if (!$image_folder_writable) {
            echo '<p class="alert-failure">' . PerchLang::get('Your resources folder is not writable. Make this folder (') . PerchUtil::html(PERCH_RESPATH) . PerchLang::get(') writable if you want to upload a custom logo.') . '</p>';
        }
    ?>
    
    <?php echo $Alert->output(); ?>
    
    <p><?php echo PerchLang::get('You can customise the look of the Perch admin area by uploading your own logo, that of your client and/or customising the application colours.'); ?></p>

    <form action="<?php echo PerchUtil::html($Form->action()); ?>" method="post" enctype="multipart/form-data">
	    
        <div class="field">
            <?php echo $Form->label('lang', 'Language'); ?>
            <?php 
                $langs = PerchLang::get_lang_options();
                $opts = array();
                if (PerchUtil::count($langs)) {
                    foreach($langs as $lang) {
                        $opts[] = array('label'=>$lang, 'value'=>$lang);
                    }
                }
                echo $Form->select('lang', $opts, $Form->get(@$details, 'lang', 'en-gb'));
            ?>
        </div>
        
        <div class="field">
            <?php echo $Form->label('logo', 'Upload a logo'); ?>
            <?php echo $Form->image('logo'); ?>
        </div>
        
        <div class="field <?php echo $Form->error('headerColour', false);?>">
            <?php echo $Form->label('headerColour', 'Header colour'); ?>
            <?php echo $Form->color('headerColour', $Form->get(@$details, 'headerColour', '#FFFFFF'), 'colour'); ?>
        </div>
        
        <div class="field <?php echo $Form->error('linkColour', false);?>">
            <?php echo $Form->label('linkColour', 'Header link colour'); ?>
            <?php echo $Form->color('linkColour', $Form->get(@$details, 'linkColour', '#333333'), 'colour'); ?>
        </div>

        
        <div class="field <?php echo $Form->error('siteURL', false);?>">
            <?php echo $Form->label('siteURL', 'Website URL'); ?>
            <?php echo $Form->text('siteURL', $Form->get(@$details, 'siteURL', '/')); ?>
        </div>

        
        <div class="field <?php echo $Form->error('helpURL', false);?>">
            <?php echo $Form->label('helpURL', 'Help button URL'); ?>
            <?php echo $Form->text('helpURL', $Form->get(@$details, 'helpURL')); ?>
        </div>

        <?php
            $app_settings   = $Perch->get_settings();
            if (PerchUtil::count($app_settings)) {
                $c = ' last';
            }else{
                $c = '';
            }
        ?>
        
        <div class="field<?php echo $c; ?>">
            <?php echo $Form->label('hideBranding', 'Hide Perch branding'); ?>
            <?php echo $Form->checkbox('hideBranding', '1',  $Form->get(@$details, 'hideBranding', '0')); ?>
        </div>
        
        <?php include('_app_settings.post.php'); ?>
        
        <p class="submit">
			<?php 		
				echo $Form->submit('submit', 'Save changes', 'button');
			
			    echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'">' . PerchLang::get('Cancel'). '</a>'; 
			?>
		</p>
		
	</form>

    <div class="clear"></div>
</div>
