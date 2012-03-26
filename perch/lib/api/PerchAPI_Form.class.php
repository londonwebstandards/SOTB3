<?php

class PerchAPI_Form extends PerchForm
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    private $defaults = array();
    
    public $last = false;

    private $hint = false;
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id = $app_id;
        $this->version = $version;
        $this->Lang = $Lang;
        
        // Include editor plugin
        $dir = PERCH_PATH.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'editors'.DIRECTORY_SEPARATOR.PERCH_APPS_EDITOR_PLUGIN;
        if (is_dir($dir) && is_file($dir.DIRECTORY_SEPARATOR.'_config.inc')) {
            $Perch = Perch::fetch();
            $Perch->add_head_content(str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents($dir.DIRECTORY_SEPARATOR.'_config.inc')));
        }
        
        parent::__construct($app_id);
    }
    
    public function form_start($id=false, $class='sectioned')
    {
        $r = '<form method="post" action="'.$this->encode($this->action()).'" ' . $this->enctype();
        
        if ($id)    $r .= ' id="'.$this->encode($id).'"';
        if ($class) $r .= ' class="app '.$this->encode($class).'"';
                
        $r .= '>';
        
        return $r;
    }
    
    public function form_end()
    {
        return '</form>';
    }
    
    public function receive($postvars)
	{
	    $data = array();
	    foreach($postvars as $val){
	        if (isset($_POST[$val])) {
	            if (!is_array($_POST[$val])){
	                $data[$val]	= trim(stripslashes($_POST[$val]));
	            }else{
	                $data[$val]	= $_POST[$val];
	            }
	        }
	    } 
	    
	    return $data;
	}
    
    public function require_field($id, $message)
    {
        $this->required[$id] = $message;
    }
    
    public function submitted()
    {
        return $this->posted() && $this->validate();
    }
    
    public function text_field($id, $label, $value='', $class='', $limit=false)
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->text($id, $this->get_value($id, $value), $class, $limit);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    public function textarea_field($id, $label, $value='', $class='', $use_editor_or_template_tag=true)
    {
        $data_atrs = array();
        
        if (is_object($use_editor_or_template_tag)) {
            $tag = $use_editor_or_template_tag;
            
            $class .= ' large ';
            if ($tag->editor()) $class .= $tag->editor();
            if ($tag->textile()) $class .= ' textile';
            if ($tag->markdown()) $class .= ' markdown';
            if ($tag->size()) $class .= ' '.$tag->size();
            if (!$tag->textile() && !$tag->markdown() && $tag->html()) $class .= ' html';
            
            if ($tag->imagewidth()) $data_atrs['width'] = $tag->imagewidth();
            if ($tag->imageheight()) $data_atrs['height'] = $tag->imageheight();
            if ($tag->imagecrop()) $data_atrs['crop'] = $tag->imagecrop();
            if ($tag->imageclasses()) $data_atrs['classes'] = $tag->imageclasses();
        }

        if ($use_editor_or_template_tag && !is_object($use_editor_or_template_tag)) {
            $class .= ' large '.PERCH_APPS_EDITOR_PLUGIN.' '.PERCH_APPS_EDITOR_MARKUP_LANGUAGE;
        }
        
        $out = $this->field_start($id); 
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->textarea($id, $this->get_value($id, $value), $class, $data_atrs);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    public function date_field($id, $label, $value='', $time=false)
    {    
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        if ($time) {
            $out .= $this->datetimepicker($id, $this->get_value($id, $value));
        }else{
            $out .= $this->datepicker($id, $this->get_value($id, $value));
        }
        
        $out .= $this->field_end($id);

        return $out;
    }
    
    public function image_field($id, $label, $value='', $basePath='', $class='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->image($id, $value, $basePath, $class);
        if ($value!='') {
            $out .= '<img class="preview" src="'.PerchUtil::html($value).'" alt="'.PerchLang::get('Preview').'" />';
            $out .= '<div class="remove">';
            $out .= $this->checkbox($id.'_remove', '1', 0).' '.$this->label($id.'_remove', PerchLang::get('Remove image'), 'inline');
            $out .= '</div>';
        }
		$out .= $this->field_end($id);

        return $out;
    }
    
    public function file_field($id, $label, $value='', $basePath='', $class='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->image($id, $value, $basePath, $class);
        if ($value!='') {
            $out .= '<div class="file">'.PerchUtil::html(str_replace(PERCH_RESPATH.'/', '', $value)).'</div>';
            $out .= '<div class="remove">';
            $out .= $this->checkbox($id.'_remove', '1', 0).' '.$this->label($id.'_remove', PerchLang::get('Remove file'), 'inline');
            $out .= '</div>';
        }
		$out .= $this->field_end($id);

        return $out;
    }
    
    public function select_field($id, $label, $options, $value='', $class='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->select($id, $options, $this->get_value($id, $value), $class);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    
    public function checkbox_field($id, $label, $checked_value='1', $value='', $class='', $limit=false)
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->checkbox($id, $checked_value, $this->get_value($id, $value), $class, $limit);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    
    public function checkbox_set($id, $label, $options, $values=false, $class='', $limit=false)
    {
        $out = $this->field_start($id);
        
        $out .= '<fieldset class="checkboxes"><legend>'.PerchUtil::html($this->Lang->get($label)).'</legend>';
        $i = 0;
        
        foreach($options as $option) {
            $boxid = $id.'_'.$i;
            $checked_value = false;
            if (in_array($option['value'], $values)){
                $checked_value = $option['value'];
            }
            if (PerchUtil::count($_POST)) {
                $checked_value = false;
                if (isset($_POST[$id]) && is_array($_POST[$id])) {
                    if (in_array($option['value'], $_POST[$id])) {
                        $checked_value = $option['value'];
                    }
                }
            }
            
            $out .= '<div class="checkbox">';
            $out .= $this->checkbox($boxid, $option['value'], $checked_value, $class, $id);
            $out .= $this->label($boxid, $option['label'], '', $colon=false, $translate=false);
            $out .= '</div>';
            $i++;
        }
        
        
        $out .= '</fieldset>';
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    
    public function submit_field($id='btnSubmit', $value="Save", $cancel_url=false, $class='button')
    {
        $out = $this->submit_start();
				
		$out .= $this->submit($id, $this->Lang->get($value), $class, $translate=false);
		
		if ($cancel_url) {
		    $out .= ' ' . $this->Lang->get('or') . ' <a href="'.$this->encode($cancel_url).'">' . $this->Lang->get('Cancel'). '</a>'; 
		}		
		        
        $out .= $this->submit_end();
        
        return $out;
    }
        
    public function field_start($id)
    {
        $r = '<div class="field '. $this->error($id, false). ($this->last ? ' last' : '').'">';
        $this->last = false;
        return $r;
    }
    
    public function field_end($id)
    {
        $r = '';
        
        if ($this->hint) $r .= parent::hint($this->hint);
        
        $r .= '</div>';
        
        $this->hint = false;
        
        return $r;
    }

    public function hint($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        $this->hint = $string;
    }
    
    public function field_help($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return parent::hint($string);
    }
    
    public function submit_start()
    {
        $s = '<p class="submit';
        if (defined('PERCH_NONSTICK_BUTTONS') && PERCH_NONSTICK_BUTTONS) {
            $s .= ' nonstick';
        }
        
        $s .= '">';
        return $s;
    }
    
    public function submit_end()
    {
        return '</p>';
    }
    
    public function encode($string)
    {
        return PerchUtil::html($string);
    }
    
    public function set_defaults($defaults)
    {
        $this->defaults = $defaults;
    }
    
    public function get_value($id, $value, $array=false)
    {
        if (!$array) $array = $this->defaults;
        
        return $this->get($array, $id, $value);
    }
    
    public function set_required_fields_from_template($Template)
    {   
        $tags       = $Template->find_all_tags();

        $seen_tags = array();
        if (is_array($tags)) {
            foreach($tags as $tag) {
                $item_id = 'perch_'.$tag->id();
                if (!in_array($tag->id(), $seen_tags)) {
                    if (PerchUtil::bool_val($tag->required())) {
                        if ($tag->type() == 'date') {
                            if ($tag->time()) {
                                $this->require_field($item_id.'_minute', "Required");
                            }else{
                                $this->require_field($item_id.'_year', "Required");
                            }
                        }else{
                            $this->require_field($item_id, "Required");
                        }
                    }
                    $seen_tags[] = $tag->id();
                }
            }
        }
    }
    
    public function fields_from_template($Template, $details=array(), $seen_tags=array())
    {    
        $tags   = $Template->find_all_tags();
        
        $Form = $this;
        
        $out = '';        
        
        if (PerchUtil::count($tags)) {
            foreach($tags as $tag) {
            
                $item_id = 'perch_'.$tag->id();
                $raw_id = 'perch_'.$tag->id().'_raw';
            
                if (!in_array($tag->id(), $seen_tags) && $tag->type()!='hidden' && $tag->type()!='slug') {
                    $out .= '<div class="field '.$Form->error($item_id, false).'">';
                
                    $label_text  = PerchUtil::html($tag->label());
                    if ($tag->type() == 'textarea') {
                        if (PerchUtil::bool_val($tag->textile()) == true) {
                            $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/help/textile" class="assist">Textile</a></span>';
                        }
                        if (PerchUtil::bool_val($tag->markdown()) == true) {
                            $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/help/markdown" class="assist">Markdown</a></span>';
                        }
                    }
                    $Form->disable_html_encoding();
                    $out .= $Form->label($item_id, $label_text, '', false, false);
                    $Form->enable_html_encoding();
        
                        switch ($tag->type()) {
                            case 'text':
                                $out .= $Form->text($item_id, $Form->get($details, $raw_id, $tag->default()), false, $tag->maxlength());
                                break;
                        
                            case 'url':
                                $out .= $Form->url($item_id, $Form->get($details, $raw_id, $tag->default()), false, $tag->maxlength());
                                break;
                        
                            case 'email':
                                $out .= $Form->email($item_id, $Form->get($details, $raw_id, $tag->default()), false, $tag->maxlength());
                                break;
                
                            case 'textarea':
                                $classname = 'large ';
                                if ($tag->editor()) $classname .= $tag->editor();
                                if ($tag->textile()) $classname .= ' textile';
                                if ($tag->markdown()) $classname .= ' markdown';
                                if ($tag->size()) $classname .= ' '.$tag->size();
                                if (!$tag->textile() && !$tag->markdown() && $tag->html()) $classname .= ' html';
                                
                                $data_atrs = array();
                                if ($tag->imagewidth()) $data_atrs['width'] = $tag->imagewidth();
                                if ($tag->imageheight()) $data_atrs['height'] = $tag->imageheight();
                                if ($tag->imagecrop()) $data_atrs['crop'] = $tag->imagecrop();
                                if ($tag->imageclasses()) $data_atrs['classes'] = $tag->imageclasses();
                        
                                $out .= $Form->textarea($item_id, $Form->get($details, $raw_id, $tag->default()), $classname, $data_atrs);
                                $out .= '<div class="clear"></div>';
                                break;
                            
                            case 'checkbox':
                                $val = ($tag->value() ? $tag->value() : '1');
                                $out .= $Form->checkbox($item_id, $val, $Form->get($details, $raw_id, $tag->default()));
                                break;
                    
                            case 'date':
                                if ($tag->time()) {
                                    $out .= $Form->datetimepicker($item_id, $Form->get($details, $raw_id, $tag->default()));
                                }else{
                                    $out .= $Form->datepicker($item_id, $Form->get($details, $raw_id, $tag->default()));
                                }
                                break;
                    
                            case 'select':
                                $options = explode(',', $tag->options());
                                $opts = array();
                                if (PerchUtil::bool_val($tag->allowempty())== true) {
                                    $opts[] = array('label'=>'', 'value'=>'');
                                }
                                if (PerchUtil::count($options) > 0) {
                                    foreach($options as $option) {
                                        $val = trim($option);
                                        $label = $val;
                                        if (strpos($val, '|')!==false) {
                                            $parts = explode('|', $val);
                                            $label = $parts[0];
                                            $val   = $parts[1];
                                        }
                                        $opts[] = array('label'=>$label, 'value'=>$val);
                                    }
                                }
                                $out .= $Form->select($item_id, $opts, $Form->get($details, $raw_id, $tag->default()));
                                break;
                    
                            case 'radio':
                                $options = explode(',', $tag->options());
                                if (PerchUtil::count($options) > 0) {
                                    $k = 0;
                                    foreach($options as $option) {
                                        $val    = trim($option);
                                        $label  = $val;
                                        if (strpos($val, '|')!==false) {
                                            $parts = explode('|', $val);
                                            $label = $parts[0];
                                            $val   = $parts[1];
                                        }
                                        $id  = $item_id . $k;
                                        $out .= '<span class="radio">';
                                        $out .= $Form->radio($id, $item_id, $val, $Form->get($details, $raw_id, $tag->default()));
                                        $Form->disable_html_encoding();
                                        $out .= $Form->label($id, $label, 'radio', false, false);
                                        $Form->enable_html_encoding();
                                        $out .= '</span>';
                                        $k++;
                                    }
                                }
                            
                                break;
                        
                            case 'image':
                                $PerchImage = new PerchImage;
                                $out .= $Form->image($item_id);
                                if (isset($details[$item_id]) && $details[$item_id]!='') {
                                    $image_src = $PerchImage->get_resized_filename($details[$item_id], 150, 150, 'thumb');
                                    $image_path = str_replace(PERCH_RESPATH, PERCH_RESFILEPATH, $image_src);
                                    if (file_exists($image_path)) {
                                        $out .= '<img class="preview" src="'.PerchUtil::html($image_src).'" alt="Preview" />';
                                        $out .= '<div class="remove">';
                                        $out .= $Form->checkbox($item_id.'_remove', '1', 0).' '.$Form->label($item_id.'_remove', PerchLang::get('Remove image'), 'inline');
                                        $out .= '</div>';
                                    }
                                }
                                break;
                            case 'file':
                                $out .= $Form->image($item_id);
                                if (isset($details[$item_id]) && $details[$item_id]!='') {
                                    $out .= '<div class="file">'.PerchUtil::html(str_replace(PERCH_RESPATH.'/', '', $details[$item_id])).'</div>';
                                    $out .= '<div class="remove">';
                                    $out .= $Form->checkbox($item_id.'_remove', '1', 0).' '.$Form->label($item_id.'_remove', PerchLang::get('Remove file'), 'inline');
                                    $out .= '</div>';
                                }
                                break;
                            
                            case 'map':
                                $out .= $Form->text($item_id.'_adr', $Form->get((isset($details[$item_id])? $details[$item_id] : array()), 'adr', $tag->default()), 'map_adr');                            
                                $out .= '<div class="map" data-btn-label="'.PerchLang::get('Find').'" data-mapid="'.PerchUtil::html($item_id).'" data-width="'.($tag->width() ? $tag->width() : '460').'" data-height="'.($tag->height() ? $tag->height() : '320').'">';
                                    if (isset($details[$item_id]['admin_html'])) {
                                        $out .= $details[$item_id]['admin_html'];
                                        $out .= $Form->hidden($item_id.'_lat', $details[$item_id]['lat']);
                                        $out .= $Form->hidden($item_id.'_lng', $details[$item_id]['lng']);
                                        $out .= $Form->hidden($item_id.'_clat', $details[$item_id]['clat']);
                                        $out .= $Form->hidden($item_id.'_clng', $details[$item_id]['clng']);
                                        $out .= $Form->hidden($item_id.'_type', $details[$item_id]['type']);
                                        $out .= $Form->hidden($item_id.'_zoom', $details[$item_id]['zoom']);
                                    }
                                $out .= '</div>';

                            
                            
                                $has_map = true;
                                break;
                    
                            default:
                                $out .= $Form->text($item_id, $Form->get($details, $item_id, $tag->default()));
                                break;
                        }
                    
                    if ($tag->help()) {
                        $out .= $Form->field_help($tag->help());
                    }
                
        
                    $out .= '</div>';
        
                    $seen_tags[] = $tag->id();
                }
            }

        }
        
        return $out;
    }
    
    public function receive_from_template_fields($Template, $previous_values)
    {
        $tags   = $Template->find_all_tags();
        
        $Form = $this;
        
        $form_vars = array();
        
        $image_folder_writable = is_writable(PERCH_RESFILEPATH);
        
        if (is_array($tags)) {
            
                
            $seen_tags = array();
            $postitems = $Form->find_items('perch_');
            
            foreach($tags as $tag) {
                $item_id = 'perch_'.$tag->id();
                
                if (!in_array($tag->id(), $seen_tags)) {
                    $var = false;
                    switch($tag->type()) {
                        case 'date' :
                            $var = $Form->get_date($tag->id(), $postitems);
                            break;
                            
                        case 'slug' :
                            if (isset($postitems[$tag->for()])) {
                                $var = PerchUtil::urlify(trim($postitems[$tag->for()]));
                            }
                            break;
                        
                        case 'image' :
                        case 'file' :
                            if ($image_folder_writable && isset($_FILES[$item_id]) && (int) $_FILES[$item_id]['size'] > 0) {
                                $filename = PerchUtil::tidy_file_name($_FILES[$item_id]['name']);
                                if (strpos($filename, '.php')!==false) $filename .= '.txt'; // diffuse PHP files
                                $target = PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.$filename;
                                if (file_exists($target)) {
                                    $filename = PerchUtil::tidy_file_name(time().'-'.$filename);
                                    $target = PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.$filename;
                                }
                                                            
                                PerchUtil::move_uploaded_file($_FILES[$item_id]['tmp_name'], $target);
                                $file_paths[$tag->id()] = $target;     
                                                                        
                                $var = PERCH_RESPATH.'/'.$filename;
                                
                                // thumbnail
                                $PerchImage = new PerchImage;
                                $PerchImage->resize_image($target, 150, 150, false, 'thumb');
                            }

                            if (!isset($_FILES[$item_id]) || (int) $_FILES[$item_id]['size'] == 0) {
                                if (isset($_POST[$item_id.'_remove'])) {
                                    $var = false;
                                }else{
                                    if (isset($previous_values[$tag->id()])){
                                        $var = $previous_values[$tag->id()];   
                                    }
                                }                                
                            }
                            
                            break;

                    
                        default: 
                            if (isset($postitems[$tag->id()])) {
                                $var = trim($postitems[$tag->id()]);
                            }
                    }
            
            
                    if ($var) {
                        if (!is_array($var)) $var = stripslashes($var);
                        //$form_vars[$tag->id()] = $var;
                        $arprocessed = $this->post_process_field($tag, $var);
                        $form_vars = array_merge($form_vars, $arprocessed);
                        
                        // title
                        if ($tag->title()) {
                            $title_var = $var;
                            
                            if (is_array($var) && isset($var['_title'])) {
                                $title_var = $var['_title'];
                            }
                            
                            if (isset($form_vars[$i]['_title'])) {
                                $form_vars[$i]['_title'] .= ' '.$title_var;
                                $processed_vars[$i]['_title'] = ' '.$title_var;
                            }else{
                                $form_vars[$i]['_title'] = $title_var;
                                $processed_vars[$i]['_title'] = $title_var;
                            }
                            
                        }
                    }
                    $seen_tags[] = $tag->id();
                }
            }
            
                                            
            // process images            
            foreach ($tags as $tag) {
                if ($tag->type()=='image' && ($tag->width() || $tag->height()) && isset($file_paths[$tag->id()])) {
                    $PerchImage = new PerchImage;
                    if ($tag->quality()) $PerchImage->set_quality($tag->quality());
                    $PerchImage->resize_image($file_paths[$tag->id()], $tag->width(), $tag->height(), $tag->crop());
                }
            }
        }
        
        return $form_vars;
    }
    
    public function post_process_field($tag, $value)
    {
        $out = array();
        $out[$tag->id().'_raw'] = $value;
        
        $formatting_language_used = false;
        
        // Strip HTML by default
        if (!is_array($value) && PerchUtil::bool_val($tag->html()) == false) {
            $value = PerchUtil::html($value);
            $value = strip_tags($value);
        }
                
        // Textile
        if (!$formatting_language_used && PerchUtil::bool_val($tag->textile()) == true) {
            $Textile = new Textile;
            $value  =  $Textile->TextileThis($value);
            
            if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
    		    $value = str_replace(' />', '>', $value);
    		}
    		            
            $formatting_language_used = true;
        }
        
        // Markdown
        if (!$formatting_language_used && PerchUtil::bool_val($tag->markdown()) == true) {
            $Markdown = new Markdown_Parser;
            $value = $Markdown->transform($value);
            $formatting_language_used = true;
        }
        
        $out[$tag->id()] = $value;
        
        return $out;
    }

}
?>
