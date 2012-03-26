<?php

class PerchAPI_Template
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    private $Template = false;
    
    private $namespace = false;
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id  = $app_id;
        $this->version = $version;
        $this->Lang    = $Lang;
        
    }

    public function set($file, $namespace)
    {    
        $this->namespace = $namespace;
        
        $local_folder = DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.$this->app_id.DIRECTORY_SEPARATOR.'templates';
        $user_path = DIRECTORY_SEPARATOR.'templates';

        if (file_exists(PERCH_PATH.$user_path.DIRECTORY_SEPARATOR.$file)) {
            $template_file= $user_path.DIRECTORY_SEPARATOR.$file;
        }else{
            $template_file = $local_folder.DIRECTORY_SEPARATOR.$file;
        }

        $this->Template = new PerchTemplate($template_file, $namespace);    
        $this->Template->enable_encoding();
        $this->Template->apply_post_processing = true;
    }
    
    public function render($data)
    {
        return $this->Template->render($data);
    }

    public function render_group($data, $implode=true)
    {
        return $this->Template->render_group($data, $implode);
    }
    
    public function find_all_tags($namespace=false)
    {
        if ($namespace==false) {
            $namespace = $this->namespace;
        }
        
        return $this->Template->find_all_tags($namespace);
    }
    
    public function find_tag($tag)
	{
		return $this->Template->find_tag($tag);
	}
    
    public function find_help()
    {
        return $this->Template->find_help();
    }
    
    public function apply_runtime_post_processing($html)
    {
        if (!$this->Template) {
            $this->Template = new PerchTemplate(); 
        }
        
        return $this->Template->apply_runtime_post_processing($html);
    }

}

?>