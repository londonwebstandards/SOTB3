<?php

class PerchUtil
{
		
	static function count($a)
	{
		if (is_array($a)){
			return count($a);
		}else{
			return 0;
		}
	}
	
	static function debug($msg, $type='log')
	{
		$Perch  = Perch::fetch();

		if (!$Perch->debug){
			return false;
		}

	    $message_styles	= array();
		$message_styles['error']	= 'color: red; font-weight: bold;';
		$message_styles['notice']	= 'color: orange;';
		$message_styles['success']	= 'color: green;';
		$message_styles['db']		= 'color: purple; margin: 0.5em 0; padding-left: 0.5em; border-left: 2px solid silver; display: block;';
		$message_styles['post']		= 'color: teal; margin: 0.5em 0; padding-left: 0.5em; border-left: 2px solid silver; display: block;';
		$message_styles['xmlrpc']	= 'color: navy;';
		$message_styles['stats']    = 'color: black;';


		$debug_messages	= '';
		$style			= 'color: #787878;';

		if (isset($message_styles[$type])){ $style	= $message_styles[$type];}
		$debug_messages .= '<span style="'.$style.'">';

		if (isset($msg) && (is_array($msg) || is_object($msg))){
			$msg	= '<pre>'.print_r($msg, 1).'</pre>';
		}

		$debug_messages .= ((isset($msg)) ? $msg : 'Something errored (no message sent).') . "\n";

		$debug_messages .= '</span>';
		

		
		$Perch->debug_output	.= $debug_messages;

	}
	
	public static function output_debug($return_value=false)
	{
		$Perch  = Perch::fetch();
		
		if (!$Perch->debug){
			return false;
		}

		if ($Perch->debug == true){

	        if ($return_value) {
	            return "\n<div class=\"debug\">\nDIAGNOSTICS:<br />\n".nl2br($Perch->debug_output)."\n</div>";
	        }else{
	            echo "\n<div class=\"debug\">\nDIAGNOSTICS:<br />\n".nl2br($Perch->debug_output)."\n</div>";
	        }
			    
		}
	}

	
	public static function html($s=false, $quotes=false)
	{
	    if ($quotes) {
	        $q = ENT_QUOTES;
	    }else{
	        $q = ENT_NOQUOTES;
	    }
	    
		if ($s || (is_string($s) && strlen($s))) return htmlspecialchars($s, $q, 'UTF-8');
	    return '';
	}
	
	
	public static function microtime_float() 
	{ 
		list($usec, $sec) = explode(" ", microtime()); 
		return ((float)$usec + (float)$sec); 
	}
	
	
	public static function redirect($url)
	{	
	    PerchSession::close();
	    header('Location: ' . $url);
	    exit;
	}
	
	public static function setcookie($name, $value = '', $expires = 0, $path = '', $domain = '', $secure = false, $http_only = false)
	{
	   header('Set-Cookie: ' . rawurlencode($name) . '=' . rawurlencode($value)
	                         . (empty($expires) ? '' : '; expires=' . gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expires))
	                         . (empty($path)    ? '' : '; path=' . $path)
	                         . (empty($domain)  ? '' : '; domain=' . $domain)
	                         . (!$secure        ? '' : '; secure')
	                         . (!$http_only    ? '' : '; HttpOnly'), false);
	}
	
	
	public static function pad($n)
	{
	    $n = (int)$n;
		if ($n<10){
			return '0'.$n;
		}else{
			return ''.$n;
		}

	}
	
	public static function contains_bad_str($str) 
	{
		$bad_strings = array(
			"content-type:"
			,"mime-version:"
			,"multipart/mixed"
			,"Content-Transfer-Encoding:"
			,"bcc:"
			,"cc:"
			,"to:"
		);

		foreach($bad_strings as $bad_string) {
		    if (stripos(strtolower($str), $bad_string) !== false) {
				return true;
			}
		}
	}
	
	
	public static function is_valid_email($email) 
	{
        if (function_exists('filter_var')) {         
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }else{
            
            if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
    			return false;
    		}

    		// Split it into sections to make life easier
    		$email_array = explode("@", $email);
    		$local_array = explode(".", $email_array[0]);
    		for ($i = 0; $i < sizeof($local_array); $i++) {
    			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
    				return false;
    			}
    		}
    		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
    			$domain_array = explode(".", $email_array[1]);
    			if (sizeof($domain_array) < 2) {
    				return false; // Not enough parts to domain
    			}
    			for ($i = 0; $i < sizeof($domain_array); $i++) {
    				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
    					return false;
    				}
    			}
    		}
    		return true;
        }
	}

	public static function send_email($to, $from_address, $from_name, $subject, $body, $misc_headers=false)
	{
		$Perch  = Perch::fetch();
		
		$headers    = "From: ".$from_name." <".$from_address.">\r\n";
		
		if (!$misc_headers) $headers .= "Content-Type: text/plain; charset=utf-8\r\n";    
	    //$subject    = '=?utf-8?B?'.base64_encode($subject).'?=';
		$subject    = PerchUtil::mail_escape_header($subject);
		
		if (defined('PERCH_MAIL_PARAMS')) {
		    $params = PERCH_MAIL_PARAMS;
		}else{
		    $params = false;
		}
		
		if ($misc_headers) $headers.=$misc_headers;
		
		if (is_array($to)) {
		    foreach($to as $mail_to) {
		        PerchUtil::debug("Sending mail '$subject' to '$mail_to' from '$from_name' ($from_address)");
		        @mail($mail_to, $subject, $body, $headers, $params);
		    }
		    return true;
		}else{
		    PerchUtil::debug("Sending mail '$subject' to '$to' from '$from_name' ($from_address)");
    		return @mail($to, $subject, $body, $headers, $params);
		}
		
		
	}
	
    public static function mail_escape_header($subject){ // thanks, Cal
        $subject = preg_replace('/([^a-z ])/ie', 'sprintf("=%02x",ord(StripSlashes("\\1")))', $subject);
        $subject = str_replace(' ', '_', $subject);
        return "=?utf-8?Q?$subject?=";
    }
	
	
	public static function excerpt($str, $words, $strip_tags=true, $balance_tags=false) {
	    $limit  = $words;
		$str 	= trim($str);
	    if ($strip_tags) $str = strip_tags($str);
        $aStr 	= explode(" ", $str);
		$newstr	= '';
		
		if (PerchUtil::count($aStr) <= $limit) {
			return $str;
		}
		
        for($i=0; $i < $limit; $i++) {
                $newstr.=$aStr[$i] . " ";
        }
        
        if ($balance_tags) return PerchUtil::balance_tags($newstr);
        
        return $newstr;
	}
	
	public static function excerpt_char($str, $chars, $strip_tags=true, $balance_tags=false)
	{
	    $limit  = $chars;

	    $str 	= trim($str);
	    if ($strip_tags) $str = strip_tags($str);
	    
	    if (strlen($str) <= $limit) return $str;
	    
	    $str    = substr($str, 0, intval($limit));
	    $last_space = strrpos($str, ' ');
	    if ($last_space > 0) $str = substr($str, 0, $last_space);

        if ($balance_tags) return PerchUtil::balance_tags($str);

	    return $str;
	}
	
	public static function balance_tags($str)
	{
	    // find broken tags
	    $regexp = '/<[^>]*$/';
	    preg_match($regexp, $str, $matches);
	    if (PerchUtil::count($matches)) {
	        // we have a broken tag
	        $last_lt = strrpos($str, '<');
	        if ($last_lt > 0) $str = substr($str, 0, $last_lt);
	    }
	    
	    // find opening tags
	    $regexp = '/<([^\/]([a-zA-z]*))[^>]*>/';
	    preg_match_all($regexp, $str, $matches);
	    if (PerchUtil::count($matches)) {
	        $opening_tags = $matches[1];
	        $closing_tags = array();
	        
	        $regexp = '/<\/([a-zA-z]*)>/';
    	    preg_match_all($regexp, $str, $matches);
    	    if (PerchUtil::count($matches)) {
	            $closing_tags = $matches[1];
    	    }
    	    
    	    // find closing tags for openers
    	    $opening_tags = array_reverse($opening_tags);
    	    foreach($opening_tags as $opening_tag) {
    	        if (isset($closing_tags[0])) {
    	            if ($closing_tags[0]!=$opening_tag) {
    	                $str .= '</'.$opening_tag.'>';
    	            }else{
    	                array_shift($closing_tags);
    	            }
    	        }else{
    	            $str .= '</'.$opening_tag.'>';
    	        }
    	    }
	    }
	    
	    return $str;
	}
	
	public static function text_to_html($string, $strip_tags=true)
	{
		if ($strip_tags) $string = strip_tags($string);
		
		$Textile	= new Textile;
		$r = $Textile->TextileThis($string);
		
		if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
		    $r = str_replace('/>', '>', $r);
		}
		
		return $r;
	}
	
	public static function array_sort($arr_data, $str_column, $bln_desc=false)
    {
        $arr_data                 = (array) $arr_data;
        
        if (PerchUtil::count($arr_data)) {
        
            $str_column               = (string) trim($str_column);
            $bln_desc                 = (bool) $bln_desc;
            $str_sort_type            = ($bln_desc) ? SORT_DESC : SORT_ASC;

            foreach ($arr_data as $key => $row)
            {
                ${$str_column}[$key]    = isset($row[$str_column]) ? $row[$str_column] : '';
            }

            array_multisort($$str_column, $str_sort_type, $arr_data);
            
        }

        return $arr_data;
    }
    
    public static function flip($odd_value, $flip=true)
    {
        global $perch_flip;
        
        if ($flip) {
            if ($perch_flip == true) {
                $perch_flip = false;
            }else{
                $perch_flip = true;
            }
        }
        
        if (!$perch_flip) return $odd_value;
        
        
    }
    
    public static function bool_val($str)
    {
              
        $str = strtolower($str);
    
        if ($str === 'false') return false;
        if ($str === '0') return false;
        if ($str === 0) return false;
        if ($str === 'no') return false;
        if ($str === 'n') return false;
        if ($str === false) return false;
        
        if ($str === 'true') return true;
        if ($str === '1') return true;
        if ($str === 1) return true;
        if ($str === 'y') return true;
        if ($str === 'yes') return true;
        if ($str === true) return true;
        
        return false;
    }
    
    public static function filename($filename, $include_crumb=true, $for_sorting=false)
    {
        $extensions = array('.html', '.htm', '.php');
        $filename = str_replace(array('.html', '.htm', '.php'), '', $filename);
        
        $filename = ltrim($filename, '/');
        $filename = str_replace(array('_', '-'), ' ', $filename);
        
        $parts = explode('/', $filename);
        foreach($parts as &$part) $part = ucfirst($part);

        $filename = array_pop($parts);
                
        if (strtolower($filename) == 'index') {
            if (count($parts)==0) {
                if ($for_sorting) {
                    $filename = '/';
                }else{
                    $filename = PerchLang::get('Home page');
                }
                
            }else{
                $filename = array_pop($parts);
            }
            
        }
  
        if ($include_crumb) {
            $parts[] = $filename;
            $filename = implode(' → ', $parts);
        }
        
        return $filename;
    }
	
	
	public static function in_section($section_path, $page_path)
	{
	    $parts = explode('/', $section_path);
	    array_pop($parts);
	    $section = implode('/', $parts);
	    
	    if ($section == '') return false;
	    

        $section_parts = explode('/', $section_path);
        $page_parts = explode('/', $page_path);

        
        for($i=0; $i<PerchUtil::count($section_parts); $i++) {
            if ($section_parts[$i] != $page_parts[$i]) {
                return $i-1;
            }
        }

	    
	    return false;
	}
	
	public static function get_folder_depth($filename)
	{
        $parts = explode('.', strtolower($filename));
        array_pop($parts);
        $filename = implode('.', $parts);
        $filename = str_replace('/index', '', $filename);
	    $segments = explode('/', $filename);
	    return PerchUtil::count($segments)-1;
	}
	
	
    
    public static function json_safe_decode($json, $assoc=false)
    {        
        if (function_exists('json_decode')) {
            return json_decode($json, $assoc);
        }else{      
            PerchUtil::debug('Decoding with Services_JSON (slow)');

            if ($assoc) {
               $Services_JSON = new Services_JSON(SERVICES_JSON_LOOSE_TYPE); 
            }else{
               $Services_JSON = new Services_JSON;
            }
            
            $result = $Services_JSON->decode($json);
            return $result;
        }
    }   
    
    public static function json_safe_encode($arr)
    {    
        if (function_exists('json_encode')) {
            return json_encode($arr);
        }else{
            PerchUtil::debug('Encoding wth Services_JSON (slow)');
            $Services_JSON = new Services_JSON;
            return $Services_JSON->encode($arr);
        }
    }
    
    public static function tidy_json($json)
    {
        $json = str_replace('{', "{\n\t", $json);
        $json = str_replace('",', '",'."\n\t", $json);
        $json = str_replace('}', "\n}", $json);
        return $json;
    }
    
    public static function tidy_file_name($filename)
    {
		$s	= strtolower($filename);
		$s	= str_replace('-', ' ', $s);
		$s	= preg_replace('/[^a-z0-9\s\.]/', '', $s);
		$s	= trim($s);
		$s	= preg_replace('/\s+/', '-', $s);

		if (strlen($s)>0){
			return $s;
		}else{
			$md5	= md5($filename);
			$s		= strtolower($md5);
			return 'ra-'.substr($s, 0, 4).'-'.substr($s, 5, 4);
		}
    }
    
    public static function get_dir_contents($dir, $include_dirs=true)
    {
        $a = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.') {
                        if ($include_dirs || (!$include_dirs && !is_dir($dir.DIRECTORY_SEPARATOR.$file))) {
                            $a[] = $file;
                        }
                    }
                }
                closedir($dh);
            }
        }
        
        return $a;
    }
    
    public static function file_extension($file)
    {
        return substr($file, strrpos($file, '.')+1);
    }
    
    public static function strip_file_extension($file)
    {
        
        return substr($file, 0, strrpos($file, '.'));
    }
    
    public static function get_files_in_folder($folder)
    {
        $a = array();
        if (is_dir($folder)) {
            if ($dh = opendir($folder)) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.' && substr($file, 0, 1) != '_') {
                        $a[] = $file;
                    }
                }
                closedir($dh);
            }
        }
        
        return $a;
    }
    
    public static function get_current_app()
    {
        $Perch = PerchAdmin::fetch();
        $page = $Perch->get_page();
        $apps = $Perch->get_apps();
        
        if (PerchUtil::count($apps)) {
            foreach($apps as $app) {
                if (strpos($page, $app['section'])!==false) {
                    return $app;
                }
            }
        }
        return false;
    }
    
    public static function urlify($string)
    {   
        $s  = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        
        $s  = strtolower($s);
        $s  = preg_replace('/[^a-z0-9\s]/', '', $s);
        $s  = trim($s);
        $s  = preg_replace('/\s+/', '-', $s);
        
        if (strlen($s)>0){
            return $s;
        }else{
            return PerchUtil::urlify_non_translit($string);
        }
        
    }
    
    public static function urlify_non_translit($string)
    {           
        $s  = strtolower($string);
        $s  = preg_replace('/[^a-z0-9\s]/', '', $s);
        $s  = trim($s);
        $s  = preg_replace('/\s+/', '-', $s);
        
        if (strlen($s)>0){
            return $s;
        }else{
            $md5    = md5($string);
            $s      = strtolower($md5);
            return 'ra-'.substr($s, 0, 4).'-'.substr($s, 5, 4);
        }
        
    }
	
	public static function http_get_request($protocol, $host, $path)
	{
	    $url = $protocol . $host . $path;
	    PerchUtil::debug($url);
        $result = false;
        $use_curl = false;
        if (function_exists('curl_init')) $use_curl = true;
        
        if ($use_curl) {
            $ch 	= curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$result = curl_exec($ch);
			PerchUtil::debug($result);
			curl_close($ch);
        }else{
            if (function_exists('fsockopen')) {
                $fp = fsockopen($host, 80, $errno, $errstr, 10);
                if ($fp) {            
                    $out = "GET $path HTTP/1.1\r\n";
                    $out .= "Host: $host\r\n";
                    $out .= "Connection: Close\r\n\r\n";

                    fwrite($fp, $out);
                    stream_set_timeout($fp, 10);
                    while (!feof($fp)) {
                        $result .=  fgets($fp, 128);
                    }
                    fclose($fp);
                }

                if ($result!='') {
                    $parts = preg_split('/[\n\r]{4}/', $result);
                    if (is_array($parts)) {
                        $result = $parts[1];
                    }
                }
            }
        }
        
        if ($result) {
            return $result;
        }
        
        return false;
        
	}
	
    public static function move_uploaded_file($filename, $destination)
    {
        $r = move_uploaded_file($filename, $destination);
        PerchUtil::set_file_permissions($destination);
        return $r;
    }
    
    public static function set_file_permissions($filename)
    {
        if (defined('PERCH_CHMOD_FILES'))
            @chmod($filename, PERCH_CHMOD_FILES);
    }
	
}

if (!function_exists('json_decode')) include(dirname(__FILE__).'/legacy/Services_JSON.php');

?>