<?php

class PerchTemplate
{
    protected $namespace;
	public $file;
	protected $template;
	protected $cache		= array();
	
	protected $autoencode = false;
	
	public $apply_post_processing = false;
	
	function __construct($file=false, $namespace='content')
	{
		
		$this->namespace = $namespace;
		
		if ($file!=false && file_exists(PERCH_PATH.$file)) {
		    $this->file		= PERCH_PATH.$file;
			$this->template	= $file;   
		}else{
		    if ($file!=false) PerchUtil::debug('Template file not found: ' . PERCH_PATH.$file, 'error');
		}
			
	}
	
	public function render_group($content_vars, $return_string=false)
	{
		$r	= array();
		if (PerchUtil::count($content_vars)){
		    $count = PerchUtil::count($content_vars);
		    for($i=0; $i<$count; $i++) {
                if (isset($content_vars[$i])) {
                    $item = $content_vars[$i];
                		    
    			    if (is_object($item)) {
                        $item = $item->to_array();
                    }
			    
    			    if ($i==0) $item['perch_item_first'] = true;
    			    if ($i==($count-1)) $item['perch_item_last'] = true;
    			    $item['perch_item_index'] = $i+1;
    			    $item['perch_item_odd'] = ($i % 2 == 0 ? '' : 'odd');
    			    $item['perch_item_count'] = $count;
    				$r[] = $this->render($item, $i+1);
    			}
			}
		}
		
		if ($return_string) {
		    return implode('', $r);
		}
		
		return $r;
	}

	public function render($content_vars, $index_in_group=false)
	{
        if (is_object($content_vars)) {
            $content_vars = $content_vars->to_array();
        }
		
		$template	= $this->template;
		$path		= $this->file;
		
		$PerchImage = new PerchImage;
		
		$contents	= $this->load();
		
		// FORMS
		$contents = str_replace('<perch:form', '<perch:form template="'.$template.'"', $contents);
		

		// CONDITIONALS
		$i = 0;
        while (strpos($contents, 'perch:')>0 && $i<10) {
            
            $s = '/(<perch:(if|after|before)[^>]*>)(((?!perch:(if|after|before)).)*)<\/perch:(if|after|before)>/s';
            /*$s = '/(<perch:((?>if|after|before))[^>]*?>)(((?!perch:(?>if|after|before)).)*?)?<\/perch:(\2)>/s';*/
            /*$s = '/((?><(perch:(before|after|if)))[^>]*?>)((?!perch:(before|after|if)).*?)(?><\/\2>)/s';*/
    		
    		$count	= preg_match_all($s, $contents, $matches, PREG_SET_ORDER);
		    
    		if ($count > 0) {		    
    			foreach($matches as $match) {
    			    $contents = $this->parse_conditional($match[2], $match[1], $match[3], $match[0], $contents, $content_vars);
    			}	
    		}
    		
    		$i++;
    	}

        // REPEATERS
        if ($index_in_group!==false) {
            $i = 0;
            while (strpos($contents, 'perch:every')>0 && $i<10) {
                $s = '/((?><(perch:(every)))[^>]*?>)((?!perch:every).*?)(?><\/\2>)/s';

        		$count	= preg_match_all($s, $contents, $matches, PREG_SET_ORDER);

        		if ($count > 0) {		    
        			foreach($matches as $match) {
        			    $contents = $this->parse_repeater($index_in_group, $match[1], $match[4], $match[0], $contents, $content_vars);
        			}	
        		}

        		$i++;
        	}
        }

		// CONTENT
		foreach ($content_vars as $key => $value) {	

			
			$s = '/<perch:'.$this->namespace.'[^>]*id="'.$key.'"[^>]*>/';
			$count	= preg_match_all($s, $contents, $matches);
					
			if ($count > 0) {
				foreach($matches[0] as $match) {
					$tag = new PerchXMLTag($match);
					if ($tag->suppress()) {
					    $contents = str_replace($match, '', $contents);
					}else{	
    					if (is_object($value) && get_class($value) == 'Image') {
    						if ($tag->class()) {
    							$out		= $value->tag($tag->class());
    							$contents 	= str_replace($match, $out, $contents);
    						}else{
    							$out		= $value->tag();
    							$contents 	= str_replace($match, $out, $contents);
    						}
    					}else{
    					    $modified_value = $value;
					    
    					    // check for 'format' attribute
    					    if ($tag->format()) {
    					        
    					        switch (substr($tag->format(), 0, 2)) {
    					            
    					            case '$:':
    					                // Money format = begins $: 
                                        if (substr($tag->format(), 0, 2)==='$:') {
                                            $modified_value = money_format(substr($tag->format(), 2), floatval($modified_value));
                                        }
    					                break;
    					                
    					            case '#:':
    					                // Number format = begins #: 
                                        if (substr($tag->format(), 0, 2)==='#:') {
                                            $decimals = 0;
                                            $point = '.';
                                            $thou = ',';
                                            
                                            $number_parts = explode('|', substr($tag->format(), 2));
                                            
                                            if (is_array($number_parts)) {
                                                if (isset($number_parts[0])) $decimals = (int) $number_parts[0];
                                                if (isset($number_parts[1])) $point = $number_parts[1];
                                                if (isset($number_parts[2])) $thou = $number_parts[2];
                                                
                                                $modified_value = number_format(floatval($modified_value), $decimals, $point, $thou);
                                            }
                                        }
    					                break;
    					                
    					            default:
    					                // dates
    					                if (strpos($tag->format(), '%')===false) {
            					            $modified_value = date($tag->format(), strtotime($value));
            					        }else{
            					            $modified_value = strftime($tag->format(), strtotime($value));
            					        }
    					                break;
    					        }
    					    }
    					    
    					    // check for 'replace' strings
    					    if ($tag->replace()) {
    					        $pairs = explode(',', $tag->replace());
					            if (PerchUtil::count($pairs)) {
					                foreach($pairs as $pair) {
					                    $pairparts = explode('|', $pair);
					                    if (isset($pairparts[0]) && isset($pairparts[1])) {
					                        PerchUtil::debug('Replacing '.$pairparts[0].' with '.$pairparts[1]);
					                        $modified_value = str_replace(trim($pairparts[0]), trim($pairparts[1]), $modified_value);
					                    }
					                }
					            }
    					    }
    					    
    					    // check for urlify
    					    if ($tag->urlify()) {
    					        $modified_value = PerchUtil::urlify($modified_value);
    					    }
    					        					    
    					    // post processing - for API use mainly. Content app does this itself.
    					    if ($this->apply_post_processing) {
    					        // Trim by chars
                                if ($tag->chars()) {
                                    if (strlen($value) > (int)$tag->chars()) {
                                        $modified_value = PerchUtil::excerpt_char($modified_value, (int)$tag->chars(), false, true);
                                    }
                                }

                                // Trim by words
                                if ($tag->words()) {
                                    $modified_value = PerchUtil::excerpt($modified_value, (int)$tag->words(), false, true);
                                }
    					    }
    					    					    
					        
    					    // replace images
    					    if ($tag->type() == 'image' && ($tag->width() || $tag->height())) {
    					        $modified_value = $PerchImage->get_resized_filename($modified_value, $tag->width(), $tag->height());
    					    }
    					    
    					    // check encoding
    					    if ($this->autoencode) {
    					        if ((!$tag->is_set('encode') || $tag->encode()==true) && ($tag->html()==false && !$tag->textile() && !$tag->markdown())) {
					                $modified_value = PerchUtil::html($modified_value);
    					        }
    					    }
					    
    						$contents = str_replace($match, $modified_value, $contents);
    					}
					}
				}
				
			}
			
		}
		
		$contents   = $this->remove_help($contents);
		$contents   = $this->remove_noresults($contents);
		
		// CLEAN UP ANY UNMATCHED <perch: /> TAGS
		$s 			= '/<perch:(?!(form|input|label|error|success|setting))[^>]*>/';
		$contents	= preg_replace($s, '', $contents);
				
    	return $contents;
	}


	public function find_tag($tag)
	{ 
		$template	= $this->template;
		$path		= $this->file;
		
		$contents	= $this->load();
			
		$s = '/<perch:[^>]*id="'.$tag.'"[^>]*>/';
		$count	= preg_match($s, $contents, $match);

		if ($count == 1){
			return new PerchXMLTag($match[0]);
		}
		
		return false;
	}
	
	public function find_all_tags($type='content')
	{
	    $template	= $this->template;
		$path		= $this->file;
		
		$contents	= $this->load();
		
		$s = '/<perch:'.$type.'[^>]*>/';
		$count	= preg_match_all($s, $contents, $matches);
		
		if ($count > 0) {
		    $out = array();
		    $i = 100;
		    if (is_array($matches[0])){
		        foreach($matches[0] as $match) {
		            $tmp = array();
		            $tmp['tag'] = new PerchXMLTag($match);
		            
		            if ($tmp['tag']->order()) {
		                $tmp['order'] = (int) $tmp['tag']->order();
		            }else{
		                $tmp['order'] = $i;
		                $i++;
		            }
                    $out[] = $tmp;
		        }
		    }
		    
		    // sort tags using 'sort' attribute
		    $out = PerchUtil::array_sort($out, 'order');
		    
		    $final = array();
		    foreach($out as $tag) {
		        $final[] = $tag['tag'];
		    }
		    
		    return $final;
		}
		
		return false;
	}
	
	public function find_help()
	{
	    $template	= $this->template;
		$path		= $this->file;
		
		$contents	= $this->load();
		
		$out        = '';
		
		if (strpos($contents, 'perch:help')>0) {
            $s = '/<perch:help[^>]*>(.*?)<\/perch:help>/s';
    		$count	= preg_match_all($s, $contents, $matches, PREG_SET_ORDER);
		
    		if ($count > 0) {
    			foreach($matches as $match) {
    			    $out .= $match[1];
    			}	
    		}
    	}
    	
    	return $out;
	}
	
    public function remove_help($contents)
    {
        $s = '/<perch:help[^>]*>.*?<\/perch:help>/s';
        return preg_replace($s, '', $contents);     
    }

    public function remove_noresults($contents)
    {
        $s = '/<perch:noresults[^>]*>.*?<\/perch:noresults>/s';
        return preg_replace($s, '', $contents);     
    }

    public function use_noresults()
    {
        $contents = $this->load();
        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
        $count	= preg_match_all($s, $contents, $matches, PREG_SET_ORDER);
	    $out = '';
		if ($count > 0) {
			foreach($matches as $match) {
			    $out .= $match[1];
			}	
		}
		// replace template with string
		$this->load($out);
    }

	protected function load($template_string=false)
	{
		$contents	= '';
		
		if ($template_string!==false) {
		    $contents = $template_string;
		    $this->cache[$this->template]	= $contents;
		}else{
		    // check if template is cached
    		if (isset($this->cache[$this->template])){
    			// use cached copy
    			$contents	= $this->cache[$this->template];
    		}else{
    			// read and cache		
    			if (file_exists($this->file)){
    				$contents 	= file_get_contents($this->file);
    				$this->cache[$this->template]	= $contents;
    			}
    		}
		}
		
		return $contents;
	}
	
	protected function parse_conditional($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars)
	{
	    
	    // IF
	    if ($type == 'if') {
	        $tag = new PerchXMLTag($opening_tag);
	        
	        $positive = $condition_contents;
            $negative = '';
	        	        
	        // else condition
	        if (strpos($condition_contents, 'perch:else')>0) {
    	        $parts   = preg_split('/<perch:else\s*\/>/', $condition_contents);
                if (is_array($parts) && count($parts)>1) {
                    $positive = $parts[0];
                    $negative = $parts[1];
                }
            }
	        
	        // exists
	        if ($tag->exists()) {
	            if (array_key_exists($tag->exists(), $content_vars) && $content_vars[$tag->exists()] != '') {
    	            $template_contents  = str_replace($exact_match, $positive, $template_contents);
    	        }else{
    	            $template_contents  = str_replace($exact_match, $negative, $template_contents);
    	        }
	        }
	        
	        // id
	        if ($tag->id()) {
	            $matched = false;
	            $sideA = false;
	        	$sideB = false;
	        	
	        	if (array_key_exists($tag->id(), $content_vars) && $content_vars[$tag->id()] != '') {
    	            $sideA  = $content_vars[$tag->id()];
    	        }
	        	
	            $comparison = 'eq';
	            if ($tag->match()) $comparison = $tag->match();
	            if ($tag->value()) $sideB = $tag->value();
	                      
	                      
	            switch($comparison) {
	                case 'eq': 
                    case 'is': 
                    case 'exact': 
                        if ($sideA == $sideB) $matched = true;
                        break;
                    case 'neq': 
                    case 'ne': 
                    case 'not': 
                        if ($sideA != $sideB) $matched = true;
                        break;
                    case 'gt':
                        if ($sideA > $sideB) $matched = true;
                        break;
                    case 'gte':
                        if ($sideA >= $sideB) $matched = true;
                        break;
                    case 'lt':
                        if ($sideA < $sideB) $matched = true;
                        break;
                    case 'lte':
                        if ($sideA <= $sideB) $matched = true;
                        break;
                    case 'contains':
                        if (preg_match('/\b'.$sideB.'\b/i', $sideA)) $matched = true;
                        break;
                    case 'regex':
                    case 'regexp':
                        if (preg_match($sideB, $sideA)) $matched = true;
                        break;
                    case 'between':
                    case 'betwixt':
                        $vals  = explode(',', $sideB);
                        if (PerchUtil::count($vals)==2) {
                            if ($sideA>trim($vals[0]) && $sideB<trim($vals[1])) $matched = true;
                        }
                        break;
                    case 'eqbetween':
                    case 'eqbetwixt':
                        $vals  = explode(',', $sideB);
                        if (PerchUtil::count($vals)==2) {
                            if ($sideA>=trim($vals[0]) && $sideB<=trim($vals[1])) $matched = true;
                        }
                        break;
                    case 'in':
                    case 'within':
                        $vals  = explode(',', $sideB);
                        if (PerchUtil::count($vals)) {
                            foreach($vals as $value) {
                                if ($sideA==trim($value)) {
                                    $matched = true;
                                    break;
                                }
                            }
                        }
                        break;
                    
	            }          
	                      
	            
	            if ($matched) {
	                $template_contents  = str_replace($exact_match, $positive, $template_contents);
	            }else{
	                $template_contents  = str_replace($exact_match, $negative, $template_contents);
	            }
	        }
	        
	    }
	    
	    // BEFORE
        if ($type == 'before') {
            if (array_key_exists('perch_item_first', $content_vars)) {
                $template_contents = str_replace($exact_match, $condition_contents, $template_contents);
            }else{
                $template_contents = str_replace($exact_match, '', $template_contents);
            }
        }
        
        // AFTER
        if ($type == 'after') {
            if (array_key_exists('perch_item_last', $content_vars)) {
                $template_contents = str_replace($exact_match, $condition_contents, $template_contents);
            }else{
                $template_contents = str_replace($exact_match, '', $template_contents);
            }
        }
	    
	    return $template_contents;
	}
	
	protected function parse_repeater($index_in_group, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars)
	{
	    $tag = new PerchXMLTag($opening_tag);
	    
	    if ($tag->count()) {
	        $count = (int) $tag->count();
            $offset = 0;
            
            if ($count !== 0 && ($index_in_group % $count == 0)) {
	            $template_contents = str_replace($exact_match, $condition_contents, $template_contents);
	        }else{
	            $template_contents = str_replace($exact_match, '', $template_contents);
	        }
            
	    }elseif ($tag->nth_child()) {
	        
	        $nth_child = $tag->nth_child();
	        $nths = array(0);
	        
	        if (is_numeric($nth_child)) {
	            $nths[] = (int)$nth_child;
	        }else{
	            
	            $multiplier = 0;
	            $offset = 0;
	            
	            switch($nth_child) {
	                
	                case 'odd':
	                    $multiplier = 2;
	                    $offset = 1;
	                    break;
	                    
	                case 'even':
	                    $multiplier = 2;
	                    $offset = 0;
	                    break;
	                
	                default:
	                    $s = '/([\+-]{0,1}[0-9]*)n([\+-]{0,1}[0-9]+){0,1}/';
                        if (preg_match($s, $tag->nth_child(), $matches)) {
                            if (isset($matches[1]) && $matches[1]!='' && $matches[1]!='-') {
                                $multiplier = (int) $matches[1];
                            }else{
                                if ($matches[1]=='-') {
                                    $multiplier = -1;
                                }else{
                                    $multiplier = 1;
                                }
                            }

                            if (isset($matches[2])) {
                                $offset = (int) $matches[2];
                            }else{
                                $offset = 0;
                            }
                        }
	                    break;
	            }
                
                $n=0;        
                if ($multiplier>0) {
                    while($n<1000 && max($nths)<=$index_in_group) {
                        $nths[] = ($multiplier*$n) + $offset;
                        $n++;
                    }
                }else{
                    while($n<1000) {
                        $nth = ($multiplier*$n) + $offset;
                        if ($nth>0) {
                            $nths[] = $nth;  
                        }else{
                            break;
                        }
                        $n++;
                    }
                }
	        }
	        
	        if (PerchUtil::count($nths)) {
                if (in_array($index_in_group, $nths)) {
                    $template_contents = str_replace($exact_match, $condition_contents, $template_contents);
                }else{
                    $template_contents = str_replace($exact_match, '', $template_contents);
                }
	        }else{
	           $template_contents = str_replace($exact_match, '', $template_contents);  
	        }
	        
	        
	    }else{
	        // No count or nth-child, so scrub it.
	        $template_contents = str_replace($exact_match, '', $template_contents);   
	    }
	    
	    
	    
	    return $template_contents;
	}
	
	public function enable_encoding()
	{
	    $this->autoencode = true;
	}
	
	public function apply_runtime_post_processing($html)
    {
        $html = $this->render_settings($html);
        $html = $this->render_forms($html);
                
        return $html;
    }
    
    public function render_forms($html)
    {
        if (strpos($html, 'perch:form')!==false) {
            $Form = new PerchTemplatedForm($html);
            $html = $Form->render();
        }
        
        return $html;
    }
    
    public function render_settings($html)
    {
        if (strpos($html, 'perch:setting')!==false) {
            $Settings = PerchSettings::fetch();
            $settings = $Settings->get_as_array();
            
            $this->load($html);
            $this->namespace = 'setting';
            $html = $this->render($settings);
            
            $s = '/<perch:setting[^>]*\/>/s';
            $html = preg_replace($s, '', $html);
        }
        
        return $html;
    }

}
?>
