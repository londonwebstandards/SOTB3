<?php

class PerchPaging
{
    public $enabled  = true;
    
    private $qs_param = 'page';
    
    private $per_page       = 10;
    private $start_position = 0;
    private $total          = 0;
    private $current_page   = 1;
    
    private $offset         = 0;
    
    function __construct($qs_param=false)
    {
    	if ($qs_param) $this->qs_param = $qs_param;
    	
    	if(isset($_GET[$this->qs_param]) && $_GET[$this->qs_param]!='') {
    		$this->current_page = (int)$_GET[$this->qs_param];
    	}
    }    
    
    public function select_sql()
    {
        if ($this->enabled) {
            return 'SELECT SQL_CALC_FOUND_ROWS DISTINCT';
        }
        return 'SELECT';
    }
    
    public function limit_sql()
    {
        if ($this->enabled) {
            return 'LIMIT ' . $this->lower_bound() . ', ' . $this->per_page();
        }
        return '';
    }

    public function total_count_sql()
    {
        if ($this->enabled) {
            return 'SELECT FOUND_ROWS() AS `count` ';
        }
        return '';
    }

    public function enable()
    {
        $this->enabled  = true;
    }
    
    public function disable()
    {
        $this->enabled = false;
    }
    
    public function enabled()
    {
        return $this->enabled;
    }
    
    public function set_per_page($per_page=10)
    {
        $this->per_page = $per_page;
    }
    
    public function per_page()
    {
        return $this->per_page;
    }
    
    public function set_start_position($start_position=0)
    {
        $this->start_position = $start_position;
    }
    
    public function start_position()
    {
        return $this->start_position;
    }
    
    public function offset()
    {
        return $this->offset;
    }
    
    public function set_offset($offset=0)
    {
        $this->offset = $offset;
    }
    
    public function set_total($total)
    {
        $this->total    = $total;
    }
    
    public function total()
    {
        return $this->total;
    }
    
    public function lower_bound()
    {
        return (($this->per_page * $this->current_page) - $this->per_page) + $this->offset;
    }
    
    public function upper_bound()
    {
        $ub = $this->lower_bound() + $this->per_page - 1;
        
        if ($this->total != 0 && $ub > $this->total) {
            return $this->total;
        }
        
        return $ub;
    }
    
    public function number_of_pages()
    {
        return ceil((0-$this->offset + $this->total) / $this->per_page);
    }
    
    public function is_first_page()
    {
        if ($this->current_page == 1) {
            return true;
        }
        
        return false;
    }
    
    public function is_last_page()
    {
        if ($this->current_page == $this->number_of_pages()) {
            return true;
        }
        
        return false;
    }
    
    public function current_page()
    {
        return $this->current_page;
    }
    
    public function to_array($opts=false)
    {
        $Perch = PerchAdmin::fetch();
        $request_uri = $Perch->get_page(1);
        
        if (is_array($opts)) {
            if (isset($opts['hide_extensions']) && $opts['hide_extensions']==true) {
                
                if (strpos($request_uri, '.')) {
                    $parts = explode('.', $request_uri);
                    array_pop($parts);
                    $request_uri = implode('.', $parts);
                }
                
            }
        }
        
        
        $qs_char = '?';
        if (strpos($request_uri, $qs_char)!==false) $qs_char = '&amp;';
        
        $out    = array();
        $out['paging']          = true;
        $out['total']           = $this->total();
        $out['number_of_pages'] = $this->number_of_pages();
        $out['total_pages']     = $this->number_of_pages();
        $out['per_page']        = $this->per_page();
        $out['current_page']    = $this->current_page();
        
        $out['lower_bound']     = $this->lower_bound()+1;
        $out['upper_bound']     = $this->upper_bound()+1;
        
        if ($this->total != 0 && $out['upper_bound'] > $this->total) {
            $out['upper_bound'] = $this->total;
        }
                
        $out['prev_url']        = '';
        $out['next_url']        = '';
            
        if (!$this->is_first_page()) {
            $out['prev_url']    = preg_replace('/'.$this->qs_param.'=[0-9]+/', $this->qs_param.'='.($this->current_page()-1), $request_uri);
            $out['not_first_page'] = true;
        }
        
        if (!$this->is_last_page()) {
            if (strpos($request_uri, $this->qs_param.'=') !== false) {
                $out['next_url']    = preg_replace('/'.$this->qs_param.'=[0-9]+/', $this->qs_param.'='.($this->current_page()+1), $request_uri);
            }else{
                $out['next_url']    = rtrim($request_uri,'/') . $qs_char.$this->qs_param.'=2';
            }
            $out['not_last_page'] = true;
        }
        
        return $out;
    }
    
    public function get_page_links($limit=false)
    {
        $number_of_pages = $this->number_of_pages();
        $lower = 1;
        $upper = $number_of_pages;
        
        if ($limit) {
            
            $half_limit = ceil($limit/2);
            
            $lower = $this->current_page() - $half_limit;
            $upper = $this->current_page() + $half_limit+1;
            
            
            if ($upper > $number_of_pages) {
                $upper = $number_of_pages;
                $lower = $upper-$limit;
            } 
            
            if ($lower < 1) {
                $lower = 1;
                $upper = ($limit>$number_of_pages?$number_of_pages:$limit);
            }    
            
        }
        
        $Perch = PerchAdmin::fetch();
        $request_uri = $Perch->get_page(1);
        
        $qs_char = '?';
        if (strpos($request_uri, $qs_char)!==false) $qs_char = '&';
        
        $page_links = array();
        
        for ($i=$lower; $i<=$upper; $i++) {
            $tmp = array();
            $p = $request_uri;
            if (strpos($p, $this->qs_param.'=')===false) {
                $p = rtrim($p, '/').$qs_char.$this->qs_param.'=0';
            }
            $p = preg_replace('/'.$this->qs_param.'=[0-9]+/', $this->qs_param.'='.($i), $p);
            
            $tmp['url'] = $p;
            $tmp['page_number'] = $i;
            
            if ((int)$this->current_page() == $i){
                $tmp['selected'] = true;
            }
            
            $page_links[] = $tmp;
        }
        
        return $page_links;
    }
}

?>
