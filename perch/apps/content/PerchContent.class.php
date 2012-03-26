<?php

class PerchContent extends PerchApp
{
    protected $table     = 'contentItems';
	protected $pk        = 'contentID';
	protected $singular_classname = 'PerchContentItem';
	
	private $registered = array();
	private $raw_content_cache = array();
	
	private $preview = false;
	
	private $tmp_url_vars = false;
	
	private $api;
	
	private $key_requests = array();
	private $keys_reordered = array();
	private $new_keys_registered = false;
    
	public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}
	
	public function set_preview($contentID, $rev=false)
	{
	    $this->preview = true;
	    $this->preview_contentID = $contentID;
	    $this->preview_rev = $rev;
	}
	
	public function get($key=false)
	{
	    if ($key === false) return ' ';
	    
	    if ($this->cache === false) {
	        $this->populate_cache();
	    }
	    
	    if (!in_array($key, $this->key_requests)) $this->key_requests[] = $key;
	    
	    $r = '';
	    
        if (isset($this->cache[$key])) {
            $r = $this->cache[$key];
        }else{
            $this->register_new_key($key);
        }
        
        if ($this->new_keys_registered) {
            // re-order keys in light of the new key
            $this->_reorder_keys();
        }
        
        return $r;
	    
	}
	
	public function get_custom($key=false, $opts=false)
	{
	    if ($key === false) return ' ';
	    
	    if ($opts===false) return $this->get($key);
	    
	    if (isset($opts['page'])) {
	        $ContentItem = $this->get_content_raw($key, $opts['page']);
	    }else{
	        $ContentItem = $this->get_content_raw($key);
	    }
	    
	    if (is_object($ContentItem)) {
	        
	        if ($this->preview && (intval($this->preview_contentID) == intval($ContentItem->id()) || $this->preview_contentID=='all')) {
                if ($this->preview_rev==false) {
                    $content = $ContentItem->pull_from_history();
                }else{
                    $content = $ContentItem->get_revision($this->preview_rev);
                }
	        }else{
	            $content = PerchUtil::json_safe_decode($ContentItem->contentJSON(), true);
	        }
	        
	        // return blank string if no content
    	    if (!is_array($content)) return ' ';
	    }else{
	        return ' ';
	    }
	    
	    // trim empty items
	    $content = array_filter($content, "count");
	    
	    // find specific _id
	    if (isset($opts['_id'])) {
	        if (PerchUtil::count($content)) {
	            $out = array();
	            foreach($content as $item) {
	                if (isset($item['_id']) && $item['_id']==$opts['_id']) {
	                    $out[] = $item;
	                    break;
	                }
	            }
	            $content = $out;
	        }   
	    }else{
	        
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            if (PerchUtil::count($content)) {
    	            $out = array();
    	            $key = $opts['filter'];
    	            $val = $opts['value'];
    	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
    	            foreach($content as $item) {
    	                if (isset($item[$key])) {
    	                    switch ($match) {
                                case 'eq': 
                                case 'is': 
                                case 'exact': 
                                    if ($item[$key]==$val) $out[] = $item;
                                    break;
                                case 'neq': 
                                case 'ne': 
                                case 'not': 
                                    if ($item[$key]!=$val) $out[] = $item;
                                    break;
                                case 'gt':
                                    if ($item[$key]>$val) $out[] = $item;
                                    break;
                                case 'gte':
                                    if ($item[$key]>=$val) $out[] = $item;
                                    break;
                                case 'lt':
                                    if ($item[$key]<$val) $out[] = $item;
                                    break;
                                case 'lte':
                                    if ($item[$key]<=$val) $out[] = $item;
                                    break;
                                case 'contains':
                                    $value = str_replace('/', '\/', $val);
                                    if (preg_match('/\b'.$value.'\b/i', $item[$key])) $out[] = $item;
                                    break;
                                case 'regex':
                                case 'regexp':
                                    if (preg_match($val, $item[$key])) $out[] = $item;
                                    break;
                                case 'between':
                                case 'betwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>trim($vals[0]) && $item[$key]<trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'eqbetween':
                                case 'eqbetwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>=trim($vals[0]) && $item[$key]<=trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'in':
                                case 'within':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)) {
                                        foreach($vals as $value) {
                                            if ($item[$key]==trim($value)) {
                                                $out[] = $item;
                                                break;
                                            }
                                        }
                                    }
                                    break;

    	                    }
    	                }
    	            }
    	            $content = $out;
    	        }
	        }
	    }
    
	    // sort
	    if (isset($opts['sort'])) {
	        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
	            $desc = true;
	        }else{
	            $desc = false;
	        }
	        $content = PerchUtil::array_sort($content, $opts['sort'], $desc);
	    }
    
	    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            shuffle($content);
        }
    
        // Pagination
        if (isset($opts['paginate'])) {
            if (isset($opts['pagination_var'])) {
                $Paging = new PerchPaging($opts['pagination_var']);
            }else{
                $Paging = new PerchPaging();
            }
            
            $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);
            
            $opts['count'] = $Paging->per_page();
            $opts['start'] = $Paging->lower_bound()+1;
            
            $Paging->set_total(PerchUtil::count($content));
        }else{
            $Paging = false;
        }
                
        // limit
	    if (isset($opts['count']) || isset($opts['start'])) {

            // count
	        if (isset($opts['count'])) {
	            $count = (int) $opts['count'];
	        }else{
	            $count = PerchUtil::count($content);
	        }
            
	        // start
	        if (isset($opts['start'])) {
	            if ($opts['start'] === 'RAND') {
	                $start = rand(0, PerchUtil::count($content)-1);
	            }else{
	                $start = ((int) $opts['start'])-1; 
	            }
	        }else{
	            $start = 0;
	        }

	        // loop through
	        $out = array();
	        for($i=$start; $i<($start+$count); $i++) {
	            if (isset($content[$i])) {
	                $out[] = $content[$i];
	            }else{
	                break;
	            }
	        }
	        $content = $out;
	    }
        
        
        

	    
	    
	    
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            if (isset($opts['raw']) && $opts['raw']==true) {
                return $content; 
            }
	    }
    
	    
	    // template
	    if (isset($opts['template'])) {
	        $template = $opts['template'];
	    }else{
	        $template = $ContentItem->contentTemplate();
	    }
	    
	    $Template = new PerchTemplate('/templates/content/'.$template, 'content');
	    
	    if (!$Template->file) {
	        return 'The template <code>' . PerchUtil::html($template) . '</code> could not be found.';
	    }
	    
	    // post process
        $tags   = $Template->find_all_tags('content');
        $processed_vars = array();
        $used_items = array();
        foreach($content as $item) {
            $tmp = $item;
            foreach($tags as $tag) {
                if (isset($item[$tag->id()])) {
                    $tmp[$tag->id()] = $ContentItem->post_process_field($tag, $item[$tag->id()]);
                    $used_items[] = $item;
                }
            }
            if ($tmp) $processed_vars[] = $tmp;
        }
        
	    // Paging to template
        if (is_object($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
	        foreach($processed_vars as &$item) {
	            foreach($paging_array as $key=>$val) {
	                $item[$key] = $val;
	            }
	        }
        }
	    
	    
	    if (isset($opts['skip-template']) && $opts['skip-template']==true) {
	        $out = array();
	        for($i=0; $i<PerchUtil::count($content); $i++) {
	            $out[] = array_merge($content[$i], $processed_vars[$i]);
	        }
            return $out;
	    }else{
            if (PerchUtil::count($processed_vars)) {
                $html = $Template->render_group($processed_vars, true);
            }else{
                $Template->use_noresults();
                $html = $Template->render(array());
            }

	        
	    }
	    
	    return $html;
	}
	
	public function get_list($filter_mode=false, $val=false, $sort=true)
	{
	    $db     = PerchDB::fetch();
	    
	    $sql    = 'SELECT contentID, contentKey, contentPage, contentNew, contentOrder, contentTemplate, contentOptions
	                FROM '.$this->table . '
	                WHERE 1=1 ';
	                
	    switch ($filter_mode) {
	        case 'new':
	            $sql .= 'AND contentNew=1 ';
	            break;
	           
            case 'page':
                $sql .= 'AND contentPage='.$this->db->pdb($val);
                break;

            case 'template':
                $sql .= 'AND contentTemplate='.$this->db->pdb($val);
                break;
	       
	        default:
	           # code...
	           break;
	    }            
	    
	    $results    = $db->get_rows($sql);
	    
	    if ($sort && PerchUtil::count($results) > 0) {
	        foreach($results as &$result) {
	            $result['formattedPage'] = PerchUtil::filename($result['contentPage'], true, true);    
	            $result['displayLevel'] = PerchUtil::get_folder_depth($result['contentPage']);
	            $result['_sort'] = $result['formattedPage'] .' '.PerchUtil::pad($result['contentOrder']);
	        }
	        
	        $results = PerchUtil::array_sort($results, '_sort');
	    }
	    
	    return $this->return_instances($results);
	}
	
	public function get_pages()
	{
	    $sql = 'SELECT DISTINCT contentPage
	            FROM '.$this->table.'
	            ORDER BY contentPage ASC';
	            
	    $rows   = $this->db->get_rows($sql);
	    
	    if (PerchUtil::count($rows)>0) {
	        $out = array();
	        foreach($rows as $row) {
	            $out[] = $row['contentPage'];
	        }
	        
	        return $out;
	    }
	    
	    return false;
	}
	
	public function get_templates()
    {
        $a = array();
        if (is_dir(PERCH_PATH.'/templates/content')) {
            if ($dh = opendir(PERCH_PATH.'/templates/content')) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.' && substr($file, 0, 1) != '_') {
                        $extension = PerchUtil::file_extension($file);
                        if ($extension == 'html' || $extension == 'htm') {
                            $a[] = array('filename'=>$file, 'path'=>PERCH_PATH.'/templates/content' . $file, 'label'=>$this->template_display_name($file));
                        }
                    }
                }
                closedir($dh);
            }
            if (PerchUtil::count($a)) $a = PerchUtil::array_sort($a, 'label');
        }
        return $a;
    }
    
    public function delete_with_key($key, $new_only=false)
    {
        $sql = 'DELETE FROM '.$this->table.'
                WHERE contentKey='.$this->db->pdb($key);
                
        if ($new_only) {
            $sql .= ' AND contentNew=1';
        }
        
        $this->db->execute($sql);
    }
    
    public function template_display_name($file_name)
    {
        $file_name = str_replace('.html', '', $file_name);
        $file_name = str_replace('_', ' ', $file_name);
        $file_name = str_replace('-', ' - ', $file_name);
        
        $file_name = ucwords($file_name);
        
        return $file_name;
    }
    
	public function page_has_drafts($page, $contentItems)
	{
	    if (PerchUtil::count($contentItems)) {
	        foreach($contentItems as $item) {
	            if ($item->contentPage() == $page) {
	                if ($item->get_option('draft')) {
	                    return true;
	                }
	            }
	        }
	    }
	    
	    return false;
	}
	
	
	public function search_content($key, $opts)
	{
	    PerchUtil::debug('Search term: '.$key);
	    
	    $search_handlers = PerchSystem::get_registered_search_handlers();
	    
	    $out = array();

        if ($key!='') {
    	    if (!$this->api) {
    	        $this->api = new PerchAPI(1.0, 'content');
    	    }
    	    
    	    $encoded_key = str_replace('"', '', PerchUtil::json_safe_encode($key));
	    
    	    $Paging = $this->api->get('Paging');
	    
    	    if (isset($opts['count'])) {
    	        $Paging->set_per_page($opts['count']);
                if (isset($opts['start']) && $opts['start']!='') {
                    $Paging->set_start_position($opts['start']);
                }
    	    }else{
    	        $Paging->disable();
    	    }
	    
    	    // Proper query using FULLTEXT
    	    $sql = $Paging->select_sql(); 
    	            
    	    $sql .= '   "content" AS source, MATCH(contentJSON) AGAINST('.$this->db->pdb($key).') AS score, 
    	            contentPage AS col1, contentHTML AS col2, contentJSON AS col3, contentOptions AS col4, "" AS col5, "" AS col6, "" AS col7, "" AS col8
    	            FROM '.$this->table.' 
    	            WHERE contentPage!=\'*\' AND contentSearchable=1 
    	                AND (MATCH(contentJSON) AGAINST('.$this->db->pdb($key).') OR MATCH(contentJSON) AGAINST('.$this->db->pdb($encoded_key).') )
    	                AND contentPage LIKE '.$this->db->pdb($opts['from_path'].'%').' ';
    	                
    	    if (PerchUtil::count($search_handlers)) {
    	        foreach($search_handlers as $handler) {	            
    	            //$handler_sql = $handler::get_search_sql($key);
    	            $handler_sql = call_user_func(array($handler, 'get_search_sql'), $key);
    	            if ($handler_sql) {
        	            $sql .= ' 
        	            UNION 
        	            '.$handler_sql.' ';
	                }
	                $handler_sql = false;
    	        }
    	    }
    	                
    	    $sql .= ' ORDER BY score DESC';
	            
            if ($Paging->enabled()) {
                $sql .= ' '.$Paging->limit_sql();
            }        
	            
    	    $rows = $this->db->get_rows($sql);
	    
    	    if (PerchUtil::count($rows)==0) {
	        
    	        // backup query using REGEXP
    	        $sql = $Paging->select_sql() . ' "content" AS source, 0-(LENGTH(contentPage)-LENGTH(REPLACE(contentPage, \'/\', \'\'))) AS score, 
    	                contentPage AS col1, contentHTML AS col2, contentJSON AS col3, contentOptions AS col4, "" AS col5, "" AS col6, "" AS col7, "" AS col8
        	            FROM '.$this->table.' 
        	            WHERE contentPage!=\'*\' AND contentSearchable=1 
        	                AND REPLACE(contentJSON, \'\\\n\', \' \') REGEXP '.$this->db->pdb('[[:<:]]'.$key.'[[:>:]]').' 
        	                AND contentPage LIKE '.$this->db->pdb($opts['from_path'].'%').' ';
        	                
	            if (PerchUtil::count($search_handlers)) {
        	        foreach($search_handlers as $handler) {
        	            //$handler_sql = $handler::get_backup_search_sql($key);
        	            $handler_sql = call_user_func(array($handler, 'get_backup_search_sql'), $key);
        	            if ($handler_sql) {
            	            $sql .= ' 
            	            UNION 
            	            '.$handler_sql.' ';
    	                }
    	                $handler_sql = false;
        	        }
        	    }
        	    
        	    $sql .= ' ORDER BY score ASC ';

                if ($Paging->enabled()) {
                    $sql .= ' '.$Paging->limit_sql();
                }        

        	    $rows = $this->db->get_rows($sql);
	        
    	    }
	    
	    
    	    if ($Paging->enabled()) {
    	        $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
    	    }
	    	    
    	    if (PerchUtil::count($rows)) {
    	        foreach($rows as $row) {
    	            switch($row['source']) {
    	                    case 'content':
    	                        $out[] = $this->format_search_result($key, $opts, $row);
    	                        break;
    	                    default:
    	                        $className = $row['source'];
    	                        $out[] =  call_user_func(array($className, 'format_result'), $key, $opts, $row);
    	            }
    	            
    	        }
    	    }
    	}
    	
        if (isset($opts['skip-template']) && $opts['skip-template']) {
            return $out;
        }
        
        $Template = new PerchTemplate('/templates/search/'.$opts['template'], 'search');
        $Template->enable_encoding();
        
        if (PerchUtil::count($out)) {
            foreach($out as &$row) {
                if ($opts['hide_extensions'] && strpos($row['url'], '.')) {
                    $parts = explode('.', $row['url']);
                    $ext = array_pop($parts);
                    $query = '';
                    if (strpos($ext, '?')!==false) {
                        $qparts = explode('?', $ext);
                        array_shift($qparts);
                        if (PerchUtil::count($qparts)) {
                            $query = '?'.implode('?', $qparts);
                        }
                    }
                    $row['url'] = implode('.', $parts).$query;
                }
            }
            

            if (isset($Paging) && $Paging->enabled()) {
                $paging_array = $Paging->to_array();
                // merge in paging vars
    	        foreach($out as &$item) {
    	            foreach($paging_array as $key=>$val) {
    	                $item[$key] = $val;
    	            }
    	        }
            }
            
            return $Template->render_group($out, 1);
        }else{
            $Template->use_noresults();
            return $Template->render(array('key'=>$key));
        }
	}
	
	
	private function populate_cache()
	{
	    
	    if ($this->preview) {
	        if ($this->preview_contentID != 'all') {
	            $this->cache = $this->get_content();
	            $ContentItem = $this->find($this->preview_contentID);
    	        if (is_object($ContentItem)) {
    	            $html = $ContentItem->render_revision($this->preview_rev);
    	            $this->cache[$ContentItem->contentKey()] = $html;
    	        }
	        }else{
	            $this->cache = $this->get_content_latest_revision();
	        }
	    }else{
	        $this->cache = $this->get_content();
	    }
	    
	}
	
    private function get_content($page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }
        
        
        $db     = PerchDB::fetch();
        
        $sql    = 'SELECT contentKey, contentHTML
                    FROM '.$this->table. '
                    WHERE contentPage='.$db->pdb($page).' OR contentPage='.$db->pdb('*');
        $results    = $db->get_rows($sql);
        
        if (PerchUtil::count($results) > 0) {
            $out = array();
            foreach($results as $row) {
                if (!array_key_exists($row['contentKey'], $out)) {
                    $out[$row['contentKey']] = $row['contentHTML'];
                }
            }
            return $out;
        }else{
            return array();
        }
    }
    
    private function get_content_latest_revision($page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }
        
        
        $db     = PerchDB::fetch();
                
        $sql    = 'SELECT *
                    FROM '.$this->table. '
                    WHERE contentPage='.$db->pdb($page).' OR contentPage='.$db->pdb('*');
        $results    = $db->get_rows($sql);
        
        if (PerchUtil::count($results) > 0) {
            $items = $this->return_instances($results);
            $out  = array();
            foreach($items as $Item) {
                $out[$Item->contentKey()] = $Item->render_revision(false);
            }
            return $out;
        }else{
            return array();
        }
    }
	
	private function get_content_raw($key, $page=false)
	{
	    $Perch  = Perch::fetch();
	    
	    if ($page===false) {
	        $page   = $Perch->get_page();
	    }
	    
	    $cache_key = $page.':'.$key;
	    
	    if (array_key_exists($cache_key, $this->raw_content_cache)) {
	        return $this->raw_content_cache[$cache_key];
	    }else{
	        $db     = PerchDB::fetch();

    	    $sql    = 'SELECT contentID, contentJSON, contentTemplate';
    	    
    	    if ($this->preview) $sql .= ', contentHistory';
    	    
    	    $sql    .= ' FROM '.$this->table. '
    	                WHERE contentKey='.$db->pdb($key).' 
    	                        AND (contentPage='.$db->pdb($page).' OR contentPage='.$db->pdb('*') .')';
    	    $result    = $db->get_row($sql);

    	    if (PerchUtil::count($result) > 0) {    	        
    	        $r = $this->return_instance($result);
    	        $this->raw_content_cache[$cache_key] = $r;
    	        return $r;
    	    }
    	    
	    }
	    
	    return false;
	}
	
	private function register_new_key($key)
	{
	    if (!isset($this->registered[$key])) {	    
    	    
    	    $Perch  = Perch::fetch();
    	    $page   = $Perch->get_page();
	    
    	    $data = array();
    	    $data['contentKey'] = $key;
    	    $data['contentPage'] = $page;
    	    $data['contentHTML'] = '<!-- Undefined content: '.PerchUtil::html($key).' -->';
    	    $data['contentJSON'] = '';
    	    $data['contentHistory'] = '';
    	    $data['contentOptions'] = '';
	    
    	    $db = PerchDB::fetch();
    	    
    	    $cols	= array();
    		$vals	= array();

    		foreach($data as $key => $value) {
    			$cols[] = $key;
    			$vals[] = $db->pdb($value).' AS '.$key;
    		}

    		$sql = 'INSERT INTO ' . $this->table . '(' . implode(',', $cols) . ') 
    		        SELECT '.implode(',', $vals).' 
    		        FROM (SELECT 1) AS dtable
    		        WHERE (
    		                SELECT COUNT(*) 
    		                FROM '.$this->table.' 
    		                WHERE contentKey='.$db->pdb($data['contentKey']).' 
    		                    AND contentPage='.$db->pdb($data['contentPage']).'
    		                )=0
    		        LIMIT 1';
    		                
    		$db->execute($sql);
    	    
    	    $this->registered[$key] = true;
    	    $this->new_keys_registered = true;
    	}
	}
	
	private function _reorder_keys()
	{
	    if (PerchUtil::count($this->key_requests)) {
	        $Perch  = Perch::fetch();
    	    $page   = $Perch->get_page();
    	    $db = PerchDB::fetch();
    	    $i = 0;
    	    foreach($this->key_requests as $key) {
    	        if (!in_array($key, $this->keys_reordered)) {
    	            $sql = 'UPDATE '.$this->table.' SET contentOrder='.$i.' WHERE contentPage='.$db->pdb($page).' AND contentKey='.$db->pdb($key).' LIMIT 1';
        	        $db->execute($sql);
        	        $this->keys_reordered[] = $key;
    	        }
    	        $i++;
    	    }
	    }
	}
	
	// Used for custom searchURLs e.g. /example.php?id={_id}
	private function substitute_url_vars($matches)
	{
	    $url_vars = $this->tmp_url_vars;
    	if (isset($url_vars[$matches[1]])){
    		return $url_vars[$matches[1]];
    	}
	}

    private function format_search_result($key, $opts, $row)
    {
        $_contentPage = 'col1';
        $_contentHTML = 'col2';
        $_contentJSON = 'col3';
        $_contentOptions = 'col4';
        
        $this->mb_fallback();
        
        $lowerkey = strtolower($key);
        $json = PerchUtil::json_safe_decode($row[$_contentJSON], 1);
        if (PerchUtil::count($json)) {
            foreach($json as $item) {
                foreach($item as $subitem) {

                    // maps and other complex data types
                    if (is_array($subitem)) {
                        $subitem = implode(' ', $subitem);
                    }
                    
                    $lowersubitem = strtolower($subitem);

                    if (true || mb_stripos($lowersubitem, $lowerkey)!==false) { // doesn't match multi-word queries. I don't think that's a problem

                        $excerpt_chars = (int) $opts['excerpt_chars'];
                        $first_portion = floor(($excerpt_chars/4));

                        $match = array();
                        $match['url'] = $row[$_contentPage];
                    
                        $regionOptions = PerchUtil::json_safe_decode($row[$_contentOptions]);
                        if ($regionOptions) {
                            if (isset($regionOptions->searchURL) && $regionOptions->searchURL!='') {
                                $match['url'] = $regionOptions->searchURL;
                                $this->tmp_url_vars = $item;
                                $match['url'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $match['url']);
                                $this->tmp_url_vars = false;
                            }
                        }
                    
                        if (isset($item['_title'])) {
                            $match['title'] = $item['_title'];
                        }else{
                            $match['title'] = $match['url'];
                        }
                        $html = strip_tags($row[$_contentHTML]);
                        $html = preg_replace('/\s{2,}/', ' ', $html);
                        $pos = mb_stripos($html, $key);
                        if ($pos<$first_portion){
                            $lower_bound = 0;
                        }else{
                            $lower_bound = $pos-$first_portion;
                        }
                    
                        $html = mb_substr($html, $lower_bound, $excerpt_chars);
                    
                        // trim broken works
                        $parts = explode(' ', $html);
                        array_pop($parts);
                        array_shift($parts);
                        $html = implode(' ', $parts);
                    
                        // keyword highlight
                        $html = preg_replace('/('.preg_quote($key, '/').')/i', '<span class="keyword">$1</span>', $html);
                    
                        $match['excerpt'] = $html;
                    
                        $match['key'] = $key;
                    
                        return $match;
                    
                    }
                }
            }
        }
        return false;
    }
    
    private function mb_fallback()
    {
        if (!function_exists('mb_stripos')) {
            function mb_stripos($a, $b) {
                return stripos($a, $b);
            }
        }
        
        if (!function_exists('mb_substr')) {
            function mb_substr($a, $b, $c) {
                return substr($a, $b, $c);
            }
        }
        
    }
	
}
?>