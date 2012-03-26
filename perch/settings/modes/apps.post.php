<div id="h1">
    <h1><?php echo PerchLang::get('Settings'), ' / ', PerchLang::get('Apps'); ?></h1>
    <?php echo $help_html; ?>
</div>

<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    

</div>

<div id="main-panel">
    <table class="d">
        <thead>
            <tr>
                <th class="first"><?php echo PerchLang::get('App'); ?></th>
                <th><?php echo PerchLang::get('Description'); ?></th>
                <th class="last">
            </tr>
        </thead>
        <tbody>
<?php
        $apps = $Perch->get_apps();

        if (PerchUtil::count($apps)) {
            foreach($apps as $item) {
                echo '<tr class="'.PerchUtil::flip('odd').($item['active'] ? '' : ' disabled').'">';
                    echo '<td>' . PerchUtil::html($item['label']) . '</td>';
                    echo '<td>' . PerchUtil::html($item['desc']) . '</td>';
                    
                    if ($item['active']) {
                        echo '<td><a href="activate/?id=' . PerchUtil::html($item['id']) . '" class="active">'.PerchLang::get('Active').'</a></td>';
                    }else{
                        echo '<td><a href="deactivate/?id=' . PerchUtil::html($item['id']) . '" class="disabled">'.PerchLang::get('Disabled').'</a></td>';
                    }
                echo '</tr>';
            }
        }

?>            
            
        </tbody>
    </table>
    
    
</div>