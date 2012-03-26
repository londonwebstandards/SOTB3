<?php

class PerchAdmin extends Perch
{
    private $apps         = array();
    private $settings     = array();
    
    private $javascript   = array();
    private $css          = array();
    private $head_content = '';
    
    public $section       = '';

    public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}

    public function get_apps()
    {
        return $this->apps;
    }
    
    public function get_app($app_id)
    {
        if (PerchUtil::count($this->apps)) {
            foreach($this->apps as $app) {
                if ($app['id']==$app_id) {
                    return $app;
                }
            }
        }
        return false;
    }
    
    
    public function find_installed_apps($CurrentUser)
    {
        $this->apps = array();
        
        $a = array();
        if (is_dir(PERCH_PATH.'/apps')) {
            if ($dh = opendir(PERCH_PATH.'/apps')) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.') {
                        if (is_dir(PERCH_PATH.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR. $file)) {
                            $a[] = array('filename'=>$file, 'path'=>PERCH_PATH.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR. $file);
                        }
                    }
                }
                closedir($dh);
            }
        }
        
        if (is_array($a)) {            
            foreach($a as $app) {
                $file = $app['path'].DIRECTORY_SEPARATOR.'admin.php';
                if (file_exists($file)) {
                    include($file);
                }
            }
        }
        
        $this->apps = PerchUtil::array_sort($this->apps, 'priority');
    }
    
    public function get_section()
    {
        $page = $this->get_page();        
        $page = trim(str_replace(PERCH_LOGINPATH.'/', '/', $page), '/');
        
        $parts  = explode('/', $page);
        
        if (is_array($parts)) {
            if ($parts[0] == 'apps') {
                return $parts[0].'/'.$parts[1];
            }else{
                return $parts[0];
            }
        }
        
        return $page;
    }
    
    public function add_javascript($path)
    {
        if (!in_array($path, $this->javascript)) {
            $this->javascript[] = $path;
        }
    }
    
    public function get_javascript()
    {
        return $this->javascript;
    }
    
    public function add_css($path)
    {
        if (!in_array($path, $this->css)) {
            $this->css[] = $path;
        }
    }
    
    public function get_css()
    {
        return $this->css;
    }
    
    public function add_head_content($str)
    {
        $this->head_content .= $str;
    }
    
    public function get_head_content()
    {
        return $this->head_content;
    }
    
    
    
    private function register_app($app_id, $label, $priority=10, $desc='', $version=false)
    {
        if (strpos($app_id, '_')!==false) {
            $Lang = new PerchAPI_Lang(1, $app_id);
            $label = $Lang->get($label);
        }else{
            $label = PerchLang::get($label);
        }
        
        $app    = array();
        $app['id']      = $app_id;
        $app['version'] = $version;
        $app['label']   = $label;
        $app['path']    = PERCH_LOGINPATH . '/apps/' . $app_id;
        $app['priority']= $priority;
        $app['desc']    = $desc;
        $app['active']  = true;
        $app['section'] = 'apps/'.$app_id;
        
        $this->apps[]   = $app;
    }
    
    private function add_setting($settingID, $label, $type='text', $value=false, $opts=false, $hint=false)
    {
        $setting = array();
        $setting['type'] = $type;
        $setting['label'] = $label;
        $setting['default'] = $value;
        $setting['hint'] = false;
        $setting['app_id'] = $this->apps[count($this->apps)-1]['id'];
        
        if ($opts) $setting['opts'] = $opts;
        if ($hint) $setting['hint'] = $hint;
        
        $this->settings[$settingID] = $setting;
    }
    
    private function require_version($app_id, $version)
    {
        if ($this->version<$version) 
            die('App <em>'.$app_id.'</em> requires <strong>Perch '.$version.'</strong> to run. You have Perch '.$this->version.'.');
    }
    
    public function get_settings()
    {
        return $this->settings;
    }
    
}

?>
