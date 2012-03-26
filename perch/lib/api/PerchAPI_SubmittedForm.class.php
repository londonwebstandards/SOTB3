<?php

class PerchAPI_SubmittedForm
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    public $data = array(); 
    public $files = array(); 
    public $antispam = false;
    
    public $id;
    
    public $formID;    
    public $templatePath;
    private $templateContent = false;
    
    private $filetypes = array();
    
    public $mimetypes = array();
    
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id  = $app_id;
        $this->version = $version;
        $this->Lang    = $Lang;
    }
    
    public function populate($formID, $templatePath, $data, $files)
    {
        $this->formID       = $formID;
        $this->id           = $formID;
        $this->templatePath = $templatePath;
        
        if (PerchUtil::count($data)) {
            foreach($data as &$datum) {
                $datum = stripslashes($datum);
            }
        }
        
        $this->data  = $data;
        $this->files = $files;
    }
    
    public function validate()
    {
        $valid = true;
        
        if (file_exists(PERCH_PATH.$this->templatePath)){
			$template = file_get_contents(PERCH_PATH.$this->templatePath);
			$TemplatedForm = new PerchTemplatedForm($template);
			
			$TemplatedForm->refine($this->formID);
			$fields = $TemplatedForm->get_fields();
			
			if (PerchUtil::count($fields)) {
			    $Perch = Perch::fetch();
			    
			    $check_format = function_exists('filter_var');
			    
			    if (PerchUtil::count($_FILES)) {
			        $this->filetypes = $this->_parse_filetypes_file();
			    }
			    
			    foreach($fields as $Tag) {
			        
			        $incoming_attr = $Tag->id();
		            if ($Tag->name()) $incoming_attr = $Tag->name();
			        
			        // Required
			        if ($Tag->required()) {
			            if (!isset($_POST[$incoming_attr]) || $_POST[$incoming_attr]=='') {
			                if (!isset($_GET[$incoming_attr]) || $_GET[$incoming_attr]=='') {
			                    if (!isset($_FILES[$incoming_attr]) || $_FILES[$incoming_attr]=='' || (isset($_FILES[$incoming_attr]['size']) && $_FILES[$incoming_attr]['size']==0)) {
    			                    $valid = false;
    			                    $Perch->log_form_error($this->formID, $Tag->id(), 'required');
    			                }
    			            }
			            }
			        }
			        
			        // Format
			        if ($check_format) {
			            $val = '';
			            
			            if (isset($_POST[$incoming_attr]) && $_POST[$incoming_attr]!='') {
			                $val = trim($_POST[$incoming_attr]);
			            }else{
			                if (isset($_GET[$incoming_attr]) && $_GET[$incoming_attr]!='') {
			                    $val = trim($_GET[$incoming_attr]);
			                }
			            }
			            
			            if ($val != '') {
        			        switch ($Tag->type()) {
        			            case 'email':
    			                    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
    			                        $valid = false;
    			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
    			                    }
        			                break;
			            
        			            case 'url':
			                        if (!filter_var($val, FILTER_VALIDATE_URL)) {
    			                        $valid = false;
    			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
    			                    }
        			                break;
        			                
        			            case 'number':
        			            case 'range':
        			                if (filter_var($val, FILTER_VALIDATE_FLOAT)) {
        			                    $val = (float)$val;
        			                    
        			                    // min
    			                        if ($Tag->min() && $val<(float)$Tag->min()) {
    			                            $valid = false;
        			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
    			                        }
        			                    
        			                    // max 
    			                        if ($Tag->max() && $val>(float)$Tag->max()) {
    			                            $valid = false;
        			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
    			                        }

                                        // step
                                        $min = 0;
                                        if ($Tag->min()) $min = (float)$Tag->min();
                                        if ($Tag->step() && strtolower($Tag->step())!='any' && ($val-$min)%(float)$Tag->step()>0) {
                                            $valid = false;
        			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
                                        }

        			                }else{
        			                    $valid = false;
    			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
        			                }
        			                break;
        			            
        			            case 'color':
            			            if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^#[0-9a-fA-F]{6}$/")))) {
    			                        $valid = false;
    			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
    			                    }
        			                break;
        			                
                                case 'week':
                                    $pattern = '/^[0-9]{4}-W[0-9]{1,2}$/';
                                    if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$pattern)))) {
                                        $valid = false;
                                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
                                    }
                                    break;
                                    
                                case 'month':
                                    $pattern = '/^[0-9]{4}-[0-9]{1,2}$/';
                                    if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$pattern)))) {
                                        $valid = false;
                                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
                                    }
                                    break;
                                    
                                case 'date':
                                    $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
                                    if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$pattern)))) {
                                        $valid = false;
                                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
                                    }
                                    break;
        			                
                                case 'datetime':
                                    $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{1,2}:[0-9]{2}:{0,1}[0-9]{0,2}$/';
                                    if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$pattern)))) {
                                        $valid = false;
                                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
                                    }
                                    break;
                                    
                                case 'time':
                                    $pattern = '/^[0-9]{1,2}:[0-9]{2}:{0,1}[0-9]{0,2}$/';
                                    if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$pattern)))) {
                                        $valid = false;
                                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
                                    }
                                    break;
        			        }
        			        
        			        
        			        // Pattern
        			        if ($Tag->pattern()) {
        			            if (!filter_var($val, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>'/^'.$Tag->pattern().'$/')))) {
			                        $valid = false;
			                        $Perch->log_form_error($this->formID, $Tag->id(), 'format');
			                    }
        			        }
        			    }
    			    }
			    
			        // Files - mime check
			        if ($Tag->type()=='image') {
			            $accept = 'image';
			        }else{
			            $accept = $Tag->accept();
			        }
			        if ($accept && isset($_FILES[$incoming_attr]) && $_FILES[$incoming_attr]['size']>0 && $_FILES[$incoming_attr]['error']==0) {
			            $mime_type = $this->_get_mime_type($_FILES[$incoming_attr]['tmp_name']);
			            $this->mimetypes[$incoming_attr] = $mime_type;
			            $parts = explode('/', $mime_type);
			            $mime_type_wildcarded = $parts[0].'/*';
			            $arr_accept = explode(' ', $accept);
			            $found = false;
			            if (PerchUtil::count($arr_accept)) {
			                foreach($arr_accept as $type) {
			                    if (isset($this->filetypes[$type])) {
			                        if (in_array($mime_type, $this->filetypes[$type]) || in_array($mime_type_wildcarded, $this->filetypes[$type])) {
			                            $found = true;
			                            break;
			                        }
			                    }
			                }
			            }
			            if (!$found) {
			                $valid = false;
                            $Perch->log_form_error($this->formID, $Tag->id(), 'filetype');
			            }
			        }
			        
			        // Files - upload error check
			        if (isset($_FILES[$incoming_attr]) && $_FILES[$incoming_attr]['error']>0 && $_FILES[$incoming_attr]['error']!=UPLOAD_ERR_NO_FILE) {
			            $valid = false;
                        $Perch->log_form_error($this->formID, $Tag->id(), 'fileupload');
			        }
			    }
			}
			
		}
        return $valid;
    }
    
    public function get_antispam_values()
    {
        if ($this->antispam!==false) {
            return $this->antispam;
        }
        
        $antispam = array();
        
        if (file_exists(PERCH_PATH.$this->templatePath)){
			$template = file_get_contents(PERCH_PATH.$this->templatePath);
			$TemplatedForm = new PerchTemplatedForm($template);
			
			$TemplatedForm->refine($this->formID);
			$fields = $TemplatedForm->get_fields();
			
			if (PerchUtil::count($fields)) {
			    foreach($fields as $Tag) {
			        if ($Tag->antispam()) {
			            $key = $Tag->antispam();
			            
			            $incoming_attr = $Tag->id();
    		            if ($Tag->name()) $incoming_attr = $Tag->name();
			            
			            if (isset($this->data[$incoming_attr])) {
			                if (isset($antispam[$key])) {
    			                $antispam[$key] .= ' '.$this->data[$incoming_attr];
    			            }else{
    			                $antispam[$key] = $this->data[$incoming_attr];
    			            }
			            }
			            
			        }
			    }
			}
		}
		
		$this->antispam  = $antispam;
		
		return $antispam;
    }
    
    public function get_template_attributes($fieldID)
    {
        $template = $this->_get_template_content();
        $s = '/(<perch:input[^>]*id="'.$fieldID.'"[^>]*>)/s';
        $count = preg_match($s, $template, $match);
        if ($count) {
            return new PerchXMLTag($match[0]);
        }
        
        // if ID doesn't work, try name
        $s = '/(<perch:input[^>]*name="'.$fieldID.'"[^>]*>)/s';
        $count = preg_match($s, $template, $match);
        if ($count) {
            return new PerchXMLTag($match[0]);
        }
        
        return false;
    }
    
    public function get_form_attributes()
    {
        $template = $this->_get_template_content();
        $s = '/(<perch:form[^>]*id="'.$this->formID.'"[^>]*>)/s';
        $count = preg_match($s, $template, $match);
        if ($count) {
            return new PerchXMLTag($match[0]);
        }
    }
    
    private function _get_template_content()
    {
        if ($this->templateContent === false) {
            $this->templateContent = file_get_contents(PERCH_PATH.$this->templatePath);
        }
        
        return $this->templateContent;
    }
    
    private function _get_mime_type($file)
    {
        $mimetype = false;
        
        $use_finfo_class        = true;
        $use_finfo_function     = true;
        $use_getimagesize       = true;
        $use_mime_content_type  = true;
        
        if ($use_finfo_class && class_exists('finfo')) {
            $finfo  = new finfo(FILEINFO_MIME, null);
            $result = $finfo->file($file);
            
            if ($result && strpos($result, ';')) {
                $parts = explode(';', $result);
                $mimetype = $parts[0];
            }
        }
        if ($use_finfo_function && function_exists('finfo_open')) {
            $finfo  = finfo_open(FILEINFO_MIME, null);
            $result = finfo_file($finfo, $file);
            finfo_close($finfo);
            
            if ($result && strpos($result, ';')) {
                $parts = explode(';', $result);
                $mimetype = $parts[0];
            }
        }
        
        if ($mimetype==false && $use_getimagesize && function_exists('getimagesize')) {
            $result = getimagesize($file);
            if (is_array($result)) $mimetype = $result['mime'];
        }
            
        if ($mimetype==false && $use_mime_content_type && function_exists('mime_content_type')) {
            $mimetype = mime_content_type($file);
        }

        return $mimetype;
    }
    
    private function _parse_filetypes_file()
    {
        $file = PERCH_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'filetypes.ini';
        if (!file_exists($file)) {
            PerchUtil::debug('Missing filetypes.ini file!', 'error');
            return array();
        }
        
        $out = array();
        $contents = file_get_contents($file);
        if ($contents) {
            $lines = explode(PHP_EOL, $contents);
            $key = 'undefined';
            foreach($lines as $line) {
                if (trim($line)=='') continue;
                
                if (strpos($line, '[')!==false) {
                    $key =  str_replace(array('[', ']'), '', trim($line));
                    continue;
                }
                
                if ($key) $out[$key][] = trim($line);
            }
        }
        
        return $out;
    }
}


?>