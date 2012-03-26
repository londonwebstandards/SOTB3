<?php

class PerchXMLTag
{
	private $attributes = array();
	private $tag;
	
	function __construct($tag) {
		$this->tag	= $tag;
		$this->parse();
	}
	
	private function parse()
	{
		$tag	= $this->tag;
		
		$s		= '/([a-z-]*)="([^"]*)"/';
		$count	= preg_match_all($s, $tag, $matches, PREG_SET_ORDER);
		
		if ($count > 0) {
			foreach($matches as $match) {
				$this->attributes[$match[1]] = $match[2];
			}
			
		}
	}
	
	function get_attributes()
	{
		return $this->attributes;
	}
	
	function __call($method, $arguments=false)
	{
	    $method = str_replace('_', '-', $method);
        
		if (isset($this->attributes[$method]) && $this->attributes[$method]!='false') {
			return $this->attributes[$method];
		}
		
		return false;
	}
	
	public function set($key, $val)
	{
	    $this->attributes[$key] = $val;
	}
	
	public function is_set($key)
	{
	    return isset($this->attributes[$key]);
	}

    public static function create($name, $type, $attrs=array())
    {    
        if ($type!='closing' && PerchUtil::count($attrs)) {
            $attpairs = array();
            foreach($attrs as $key=>$val) {
                if ($val!='') {
                    $attpairs[] = PerchUtil::html($key).'="'.PerchUtil::html($val, true).'"';
                } 
            }
            $attstring = ' '.implode(' ', $attpairs);
        }else{
            $attstring = '';
        }
        
        switch($type) {
            case 'opening':
                return '<'.PerchUtil::html($name).$attstring.'>';
                break;
                
            case 'single':
                if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
                    return '<'.PerchUtil::html($name).$attstring.'>';
                }else{
                    return '<'.PerchUtil::html($name).$attstring.' />';
                }
                break;

            case 'closing':
                return '</'.PerchUtil::html($name).'>';
                break;
        }
        
        return '';
    }
}

?>
