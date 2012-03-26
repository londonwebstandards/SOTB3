<div id="h1">
    <h1><?php echo PerchLang::get('Users'); ?></h1>
    <?php echo $help_html; ?>

</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Add User'); ?></span></h3>
    
    <p><?php echo PerchLang::get('You can add user accounts with administrative or editor privileges.'); ?></p>
    
    <p><a href="<?php echo PERCH_LOGINPATH; ?>/users/add"><?php echo PerchLang::get('Add new user'); ?></a></p>
    
    <p><?php echo PerchLang::get('Note you cannot delete the Primary Admin user account, only edit it.'); ?></p>
	
</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>

    

    <table class="d">
        <thead>
            <tr>
                <th class="first"><?php echo PerchLang::get('Username'); ?></th>
                <th><?php echo PerchLang::get('Role'); ?></th>
                <th><?php echo PerchLang::get('Name'); ?></th>
                <th><?php echo PerchLang::get('Email'); ?></th>
                <th class="action last"></th>
            </tr>
        </thead>
        <tbody>
        <?php
            if (PerchUtil::count($users) > 0) {
                foreach($users as $item) {
                    echo '<tr class="'.PerchUtil::flip('odd').'">';
                        echo '<td class="' . PerchUtil::html(strtolower($item->userRole())) . '"><a href="edit/?id=' . PerchUtil::html($item->id()) . '">' . PerchUtil::html($item->userUsername()) . '</a></td>';
                        echo '<td>' . PerchUtil::html(PerchLang::get($item->userRole())) . '</td>';
                        echo '<td>' . PerchUtil::html($item->userGivenName().' '.$item->userFamilyName()) . '</td>';
                        
                        echo '<td><a href="mailto:' . PerchUtil::html($item->userEmail()) . '">' . PerchUtil::html($item->userEmail()) . '</a></td>';
                        if ($item->id()!=$CurrentUser->id()) {
                            echo '<td><a href="delete/?id=' . PerchUtil::html($item->id()) . '" class="delete inline-delete" data-item-name="user">'.PerchLang::get('Delete').'</a></td>';
                        }else{
                            echo '<td><span class="delete">'.PerchLang::get('Delete').'</span></td>';
                        }
                    echo '</tr>';
                }
            }
        
        ?>
        </tbody>
    </table>

    <div class="clear"></div>
</div>