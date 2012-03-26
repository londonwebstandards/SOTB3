<?php

class PerchContentItem extends PerchBase
{
    protected $table  = 'contentItems';
    protected $pk     = 'contentID';
    
    private $history_items = 6; // Always number of undos +1. Overridden by PERCH_UNDO_BUFFER
    private $current_userID = false;
    
    private $options  = false;
    
    private $clean_resources = true;

    function __construct($details) 
    {        
        if (defined('PERCH_UNDO_BUFFER')) $this->history_items = (int)PERCH_UNDO_BUFFER+1;
        if (defined('PERCH_CLEAN_RESOURCES')) $this->clean_resources = PERCH_CLEAN_RESOURCES;
        return parent::__construct($details);
    }
    
    public function delete()
    {
        // clear out the JSON so we can run a resource cleanup before delete.
        $data = array();
        $data['contentJSON'] = '[]';
        $data['contentHistory'] = '[]';
        $this->update($data);
        $this->clean_up_resources();
        
        parent::delete();
    }

    public function post_process_field($tag, $value)
    {
        $formatting_language_used = false;

        // Strip HTML by default
        if (!is_array($value) && PerchUtil::bool_val($tag->html()) == false) {
            $value = PerchUtil::html($value);
            $value = strip_tags($value);
        }

        // Trim by chars
        if ($tag->chars()) {
            if (strlen($value) > (int)$tag->chars()) {
                $value = PerchUtil::excerpt_char($value, (int)$tag->chars(), false, true);
            }
        }

        // Trim by words
        if ($tag->words()) {
            $value = PerchUtil::excerpt($value, (int)$tag->words(), false, true);
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
        
        // Maps
        if ($tag->type() == 'map') {
            $value = $value['html'];
        }
        return $value;
    }
    
    public function post_process_region($clean_vars, $processed_vars, $options=false)
    {
        if ($options === false) {
            $options = $this->get_options();
        }
        
        // Sort order
        if (array_key_exists('sortField', $options) && $options['sortField']!='') {
            $desc = false;
            if (array_key_exists('sortOrder', $options) && $options['sortOrder']=='DESC') {
                $desc = true;
            }
            
            $clean_vars = PerchUtil::array_sort($clean_vars, $options['sortField'], $desc);
            $processed_vars = PerchUtil::array_sort($processed_vars, $options['sortField'], $desc);
            
            
        } 
        
        // Limit
        if (array_key_exists('limit', $options) && $options['limit']!=false) {
            $limit = (int) $options['limit'];
            
            $new_processed_vars = array();
            for($i=0; $i<$limit; $i++) {
                if (isset($processed_vars[$i])) $new_processed_vars[] = $processed_vars[$i];
            }
            $processed_vars = $new_processed_vars; 
        }
        
        
        return array($clean_vars, $processed_vars);
        
    }
    
    public function globalise()
    {
        $this->set_options(array('original_contentPage'=>$this->contentPage()));
        
        $data = array();
    	$data['contentPage'] = '*';
    	$this->update($data);
    	
    	$ContentItems = new PerchContent;
    	$ContentItems->delete_with_key($this->contentKey(), true);
    	
    }
    
    public function deglobalise()
    {
        $orig_path = $this->get_option('original_contentPage');
        
        if ($orig_path) {
            $data = array();
        	$data['contentPage'] = $orig_path;
        	$this->update($data);
        	
        	return true;
        }
        
        return false;
    }
    
    public function add_item()
    {
        $items      = $this->pull_from_history();
        $new_item   = array('_id'=>'__new__');
        
        if ($this->contentAddToTop()=='1') {
            array_unshift($items, $new_item);
        }else{
            $items[] = $new_item;
        }
        
        $data = array();
        $data['contentHistory'] = $this->push_to_history($items);
        
        $this->update($data);
    }
    
    public function delete_item($index)
    {
        $items = $this->pull_from_history();
        
        if (isset($items[$index])) {
             array_splice($items, $index, 1);
        }
        
        if ($this->get_option('draft')) {
            $data = array();
        }else{
            $data = $this->_render_html($items);
        }
        
        $data['contentHistory'] = $this->push_to_history($items);
        
        $this->update($data);
        $this->clean_up_resources();
    }
    
    public function truncate($total=1)
    {
        $items = $this->pull_from_history();
        
        $new_items = array();
        for($i=0; $i<$total; $i++) {
            $new_items[] = $items[$i];
        }
        
        $items = $new_items;
        
        if ($this->get_option('draft')) {
            $data = array();
        }else{
            $data = $this->_render_html($items);
        }
        
        $data['contentHistory'] = $this->push_to_history($items);
        
        $this->update($data);
    }
    
    public function republish($items=false)
    {
        if (!$items) $items = $this->pull_from_history();
            
        if ($this->get_option('draft')) {
            $data = array();
        }else{
            $data = $this->_render_html($items);
        }
        
        $data['contentHistory'] = $this->push_to_history($items);
        
        $this->update($data);
    }
    
    public function revert_most_recent()
    {
        $history = PerchUtil::json_safe_decode($this->contentHistory(), true);
        if (!is_array($history)) {
            return false;
        }
        
        // shift the current 'mistake' from the array
        array_shift($history);
        if (PerchUtil::count($history)) {
            $changeset = $history[0];
            $items   = $changeset['_content'];
        
            if ($this->get_option('draft')) {
                $data = array();
            }else{
                $data = $this->_render_html($items);
            }

            $data['contentHistory'] = PerchUtil::json_safe_encode($history);
        
            $this->update($data);
            
            $this->clean_up_resources();
        
            return true;
        }
        
        return false;
    }
    
    
    public function get_options()
    {
        if (is_array($this->options)) return $this->options;
        $arr = PerchUtil::json_safe_decode($this->contentOptions(), true);
        if (!is_array($arr)) $arr = array();
        $this->options = $arr;
        return $arr;
    }
    
    public function get_option($optKey)
    {
        $options = $this->get_options();
        if (array_key_exists($optKey, $options)) {
            $opt = $options[$optKey];
            if ($opt === 'false') return false;
            return $opt;
        }
        return false;
    }
    
    public function set_options($options)
    {
        $existing = $this->get_options();
        if (!is_array($existing)) $existing = array();
        
        $opts = array_merge($existing, $options);
        
        $data = array();
        $data['contentOptions'] = PerchUtil::json_safe_encode($opts);
        $this->update($data);
        
        // clear cache
        $this->options = false;
    }
    

    public function get_next_id()
    {
        $opts = $this->get_options();
        if (!isset($opts['id'])) {
            $id = 1;
        }else{
            $id = intval($opts['id'])+1;
        }
        
        $this->set_options(array('id'=>$id));
        
        return $id;
    }

    public function log_resource($path)
    {
        $resources = $this->get_option('resources');
        
        if (!$resources) $resources = array();
        
        $resources[] = $path;
        
        $this->set_options(array('resources'=>$resources));
    }
    
    public function remove_resource($path)
    {
        $resources = $this->get_option('resources');
        
        $key = array_search($path, $resources);
        if ($key) unset($resources[$key]);
        
        $this->set_options(array('resources'=>$resources));
    }

    public function clean_up_resources()
    {
        if ($this->clean_resources==false) return;
        
        PerchUtil::debug('Cleaning up unused resources');
        
        $resources = $this->get_option('resources');
        
        if (PerchUtil::count($resources)) {
            // get resources
            $resfiles = PerchUtil::get_files_in_folder(PERCH_RESFILEPATH);
            if (PerchUtil::count($resfiles)) {
            
                // get JSON as an unencoded string for simple searching
                $contentHistory = print_r(PerchUtil::json_safe_decode($this->contentHistory()), true);
                $to_be_deleted = array();
        
                foreach($resources as $web_path) {            
                    // check if the web path is in the stored content or history anywhere
                    if (strpos($contentHistory, $web_path)===false) {
                        // the file has gone                    
            
                        $file_path = str_replace(PERCH_RESPATH, PERCH_RESFILEPATH, $web_path);
                        $file_name = trim(str_replace(PERCH_RESPATH, '', $web_path), '/');
                        $extension = PerchUtil::file_extension($file_name);
                        $bare      = PerchUtil::strip_file_extension($file_name);
                            
                        // basic file
                        if (in_array($file_name, $resfiles)) {
                            $to_be_deleted[] = $file_path;
                        }
                
                        // thumb
                        foreach($resfiles as $resfile) {
                            if ($resfile == $bare.'-thumb'.'.'.$extension) {
                                $to_be_deleted[] = PERCH_RESFILEPATH . DIRECTORY_SEPARATOR . $resfile;
                            }
                        }
                        
                        // resized images
                        $pattern = '/'.preg_quote($bare,'/').'-(w[0-9]{1,4}){0,1}(h[0-9]{1,4}){0,1}\.'.preg_quote($extension,'/').'/';
                        PerchUtil::debug($pattern);
                        foreach($resfiles as $resfile) {
                            if (preg_match($pattern, $resfile)) {
                                $to_be_deleted[] = PERCH_RESFILEPATH . DIRECTORY_SEPARATOR . $resfile;
                            }
                        }
                        
                        
                        $this->remove_resource($web_path);
                    }
                }
                
                if (PerchUtil::count($to_be_deleted)) {
                    foreach($to_be_deleted as $file) {
                        if (file_exists($file)) {
                            unlink($file);
                        }
                    }
                }
                
            }
        }
    }
    
    public function push_to_history($items)
    {
        $history = PerchUtil::json_safe_decode($this->contentHistory(), true);
        if (!is_array($history)) {
            $history = array();
        }

        $new_item = array();
        $new_item['_rev']       = time();
        $new_item['_user_id']   = $this->current_userID; 
        $new_item['_content']   = $items;

        // prepend to the array
        array_unshift($history, $new_item);
        
        // trim the array
        $new_history = array_slice($history, 0, $this->history_items);
        
        return PerchUtil::json_safe_encode($new_history);
    }
    
    public function pull_from_history($field='_content')
    {
        $history = PerchUtil::json_safe_decode($this->contentHistory(), true);
        if (!is_array($history)) {
            return false;
        }
        
        if (PerchUtil::count($history)) {
            $changeset = $history[0];
            return $changeset[$field];
        }
        
        return false;
    }
    
    
    public function refresh_details()
    {
        $details = $this->pull_from_history();
        
        if (!is_array($details)) {
            $details = PerchUtil::json_safe_decode($this->contentJSON(), true);
        }
        
        
        $out = array();
        // details
        $out[] = $details;
        // items
        $out[] = (PerchUtil::count($details) > 0 ? PerchUtil::count($details) : 1);
        // history
        $out[] = PerchUtil::json_safe_decode($this->contentHistory(), true);
        
        return $out;
    }

    public function set_current_user($userID)
    {
        $this->current_userID = $userID;
    }
    
    public function get_revision_number()
    {
        return $this->pull_from_history('_rev');
    }
    
    public function render_revision($rev)
    {
        $items = $this->get_revision($rev);
                
        if (is_array($items)) {
            $data = $this->_render_html($items);
            return $data['contentHTML'];
        }
        
        return false;
    }
    
    public function get_revision($rev)
    {
        $history = PerchUtil::json_safe_decode($this->contentHistory(), true);
        if (!is_array($history)) {
            return false;
        }
        
        $items = false;
        
        if (PerchUtil::count($history)) {
            foreach($history as $revision) {    
                // if rev is false, use the first revision
                if (($rev == false) || intval($revision['_rev']) == intval($rev)) {
                    $items = $revision['_content'];
                    break;
                }
            }
        }
        
        return $items;
    }

    public function pre_process_map($id, $tag, $value)
    {
        $out = array();

        
        if (isset($value['adr'])) {
            
            $out['adr']     = $value['adr'];
            $out['_title']  = $value['adr'];
            
            if (!isset($value['lat'])) {
            
                $lat = false;
                $lng = false;
            
                $path = '/maps/api/geocode/json?address='.urlencode($value['adr']).'&sensor=false';
                $result = PerchUtil::http_get_request('http://', 'maps.googleapis.com', $path);
                if ($result) {
                    $result = PerchUtil::json_safe_decode($result, true);
                    PerchUtil::debug($result);
                    if ($result['status']=='OK') {
                        if (isset($result['results'][0]['geometry']['location']['lat'])) {
                            $lat = $result['results'][0]['geometry']['location']['lat'];
                            $lng = $result['results'][0]['geometry']['location']['lng'];
                        }
                    }  
                }
            }else{
                $lat = $value['lat'];
                $lng = $value['lng'];
            }
            
            $out['lat'] = $lat;
            $out['lng'] = $lng;
            
            
            if (!isset($value['clat'])) {
                $clat = $lat;
                $clng = $lng;
            }else{
                $clat = $value['clat'];
                $clng = $value['clng'];
            }
            
            $out['clat'] = $clat;
            $out['clng'] = $clng;
            
            if (!isset($value['zoom'])) {
                if ($tag->zoom()) {
                    $zoom = $tag->zoom();
                }else{
                    $zoom = 15;
                }
            }else{
                $zoom = $value['zoom'];
            }
            
            if (!isset($value['type'])) {
                if ($tag->type()) {
                    $type = $tag->type();
                }else{
                    $type = 'roadmap';
                }
            }else{
                $type = $value['type'];
            }
            
                       
            $adr    = $value['adr'];
            $width  = ($tag->width() ? $tag->width() : '460');
            $height = ($tag->height() ? $tag->height() : '320');  
            
            $out['zoom'] = $zoom;
            $out['type'] = $type;
                        
            $r  = '<img id="cmsmap'.PerchUtil::html($id).'" src="http://maps.google.com/maps/api/staticmap';
            $r  .= '?center='.$clat.','.$clng.'&amp;sensor=false&amp;size='.$width.'x'.$height.'&amp;zoom='.$zoom.'&amp;maptype='.$type;
            if ($lat && $lng)   $r .= '&amp;markers=color:red|color:red|'.$lat.','.$lng;    
            $r  .= '" ';
            if ($tag->class())  $r .= ' class="'.PerchUtil::html($tag->class()).'"';
            $r  .= ' width="'.$width.'" height="'.$height.'" alt="'.PerchUtil::html($adr).'" />';
            
            $out['admin_html'] = $r;
            
            // JavaScript
            $r .= '<script type="text/javascript">/* <![CDATA[ */ ';
            $r .= "if(typeof(CMSMap)=='undefined'){var CMSMap={};CMSMap.maps=[];document.write('<scr'+'ipt type=\"text\/javascript\" src=\"".PerchUtil::html(PERCH_LOGINPATH)."/assets/js/public_maps.js\"><'+'\/sc'+'ript>');}";
            $r .= "CMSMap.maps.push({'mapid':'cmsmap".PerchUtil::html($id)."','width':'".$width."','height':'".$height."','type':'".$type."','zoom':'".$zoom."','adr':'".addslashes(PerchUtil::html($adr))."','lat':'".$lat."','lng':'".$lng."','clat':'".$clat."','clng':'".$clng."'});";
            $r .= '/* ]]> */';
            $r .= '</script>';

            
            if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
    		    $r = str_replace('/>', '>', $r);
    		}
            
            $out['html'] = $r;
        }
        
        return $out;
    }


    private function _render_html($items)
    {
        $Template = new PerchTemplate('/templates/content/'.$this->contentTemplate(), 'content');
        $tags     = $Template->find_all_tags('content');
        $processed_vars = array();
        $i = 0;
        
        if (PerchUtil::count($items) && PerchUtil::count($tags)) {
            foreach($items as $item) {
                foreach($tags as $tag) {
                    if (isset($item[$tag->id()])) {
                        $processed_vars[$i][$tag->id()] = $this->post_process_field($tag, $item[$tag->id()]);
                    }
                }
                $i++;
            }
        }

        list($items, $processed_vars) = $this->post_process_region($items, $processed_vars);
        
        $data = array();
        $data['contentJSON'] = PerchUtil::json_safe_encode($items);
        $data['contentHTML'] = $Template->render_group($processed_vars, true);
        
        return $data;
    }
    

    

}

?>