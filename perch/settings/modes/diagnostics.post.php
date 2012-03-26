<div id="h1">
    <h1><?php echo PerchLang::get('Settings'); ?></h1>
<?php echo $help_html; ?>
</div>


<div id="side-panel">
    <h3><span><?php echo PerchLang::get('Help'); ?></span></h3>
    
    <p><?php echo PerchLang::get('You may be asked for this information when requesting technical support.'); ?></p>

</div>

<div id="main-panel">
    
    <?php echo $Alert->output(); ?>
    
    <h2><?php echo PerchLang::get('Diagnostics report'); ?></h2>
    
    <div id="diagnostics">
        <ul>
            <li>Perch: <?php echo PerchUtil::html($Perch->version); ?></li>
            <?php
                $apps_list = $Perch->get_apps();
                $apps = array();
                echo '<li>Installed apps: ';
                if (PerchUtil::count($apps_list)) {
                    foreach($apps_list as $app) {
                        $apps[] = PerchUtil::html($app['id'].($app['version'] ? ' ('.$app['version'].')':''));
                    }
                    echo implode(', ', $apps);
                }else{
                    echo 'none.';
                }
                echo '</li>';
            
            ?>
            <li>PHP: <?php echo PerchUtil::html(phpversion()); ?></li>
            <li>Zend: <?php echo PerchUtil::html(zend_version()); ?></li>
            <li>OS: <?php echo PerchUtil::html(PHP_OS); ?></li>
            <li>SAPI: <?php echo PerchUtil::html(PHP_SAPI); ?></li>
            <li>Safe mode: <?php echo (ini_get('safe_mode') ? 'detected' : 'not detected'); ?></li>
            <li>MySQL client: <?php echo PerchUtil::html(mysql_get_client_info()); ?></li>
            <li>MySQL server: <?php echo PerchUtil::html(mysql_get_server_info()); ?></li>
            <li>Extensions: <?php echo PerchUtil::html(implode(', ', get_loaded_extensions())); ?></li>
            <li>GD: <?php echo PerchUtil::html((extension_loaded('gd')? 'Yes' : 'No')); ?></li>
            <li>ImageMagick: <?php echo PerchUtil::html((extension_loaded('imagick')? 'Yes' : 'No')); ?></li>
            <?php
                $max_upload   = (int)(ini_get('upload_max_filesize'));
                $max_post     = (int)(ini_get('post_max_size'));
                $memory_limit = (int)(ini_get('memory_limit'));
                $upload_mb    = min($max_upload, $max_post, $memory_limit);
            ?>
            <li>PHP max upload size: <?php echo $max_upload; ?>M</li>
            <li>PHP max form post size: <?php echo $max_post; ?>M</li>
            <li>PHP memory limit: <?php echo $memory_limit; ?>M</li>
            <li>Total max uploadable file size: <?php echo $upload_mb; ?>M</li>
            <li>Session timeout: <?php echo ini_get('session.gc_maxlifetime')/60; ?> minutes</li>
            <?php
                $constants = get_defined_constants(true);
                $ignore = array('PERCH_LICENSE_KEY', 'PERCH_DB_PASSWORD');
                if (PerchUtil::count($constants['user'])) {
                    foreach($constants['user'] as $key=>$val) {
                        if (!in_array($key, $ignore) && substr($key, 0, 5)=='PERCH') echo '<li>'.PerchUtil::html($key.': '.$val).'</li>';
                    }
                }
            ?>
            <?php
                $DB = PerchDB::fetch();
                $sql = 'SHOW TABLES';
                $rows = $DB->get_rows($sql);
                if (PerchUtil::count($rows)) {
                    $tables = array();
                    
                    foreach($rows as $row) {
                        foreach($row as $key=>$val) {
                            $tables[] =  PerchUtil::html($val);
                        }
                    }
                    echo '<li>DB tables: '.implode(', ', $tables).'</li>';
                }
            ?>
            <li>Resource folder writeable: <?php echo is_writable(PERCH_RESFILEPATH)?'Yes':'No'; ?></li>
            <li>Native JSON: <?php echo function_exists('json_encode')?'Yes':'No'; ?></li>
            <li>Filter functions: <?php echo function_exists('filter_var')?'Yes':'No (Required for form field type validation)'; ?></li>
            <li>Users: <?php echo PerchDB::fetch()->get_value('SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'users'); ?></li>        
            <li>H1: <?php echo PerchUtil::html(md5($_SERVER['SERVER_NAME'])); ?></li>
            <li>L1: <?php echo PerchUtil::html(md5(PERCH_LICENSE_KEY)); ?></li>
            <?php
                $settings = $Settings->get_as_array();
                if (PerchUtil::count($settings)) {
                    foreach($settings as $key=>$val) {
                        echo '<li>'.PerchUtil::html($key.': '.$val).'</li>';
                    }
                }
            
            ?>
            <?php
                foreach($_SERVER as $key=>$val) {
                    if ($key && $val)
                    echo '<li>' . PerchUtil::html($key) . ': ' . PerchUtil::html($val).'</li>';
                }
            ?>
            
        </ul>
        
    </div>
    

    <div class="clear"></div>
</div>