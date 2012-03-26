<?php

class PerchEmail
{
    
    private $vars = array();
    private $template;
    private $template_path;
    
    private $cache	= array();
    
    private $subject;
    private $senderName;
    private $senderEmail;
    private $recipientEmail;
    
    private $template_data;
    
    
    function __construct($template)
    {    
        $this->template = $template; 
        $this->template_path = PERCH_PATH . '/emails/' . $template . '.txt'; 
    }
    
    
    public function subject($str=false)
    {
        if ($str === false) {
            return $this->subject;
        }
        
        $this->subject = $str;
    }
    
    public function senderName($str=false)
    {
        if ($str === false) {
            return $this->senderName;
        }
        
        $this->senderName = $str;
    }
    
    public function senderEmail($str=false)
    {
        if ($str === false) {
            return $this->senderEmail;
        }
        
        $this->senderEmail = $str;
    }
    
    public function recipientEmail($str=false)
    {
        if ($str === false) {
            return $this->recipientEmail;
        }
        
        $this->recipientEmail = $str;
    }
    
    public function set($key, $str=false)
    {
        if ($str === false) {
            return $this->vars[$key];
        }
        
        $this->vars[$key] = $str;
    }
    
    public function set_bulk($data)
    {
        if (is_array($data)) {
            
            foreach ($data as $key=>$val) {
                $this->set($key, $val);
            }
            
        }
    }
    
    public function send()
    {
        $body = $this->build_message();
        
        // Fix any bare linefeeds in the message to make it RFC821 Compliant. 
        // - thanks Ian Routledge.
        $body = preg_replace("#(?<!\r)\n#si", "\r\n", $body);
        
        $this->send_plain_text($body);
    }
    
    
    private function send_plain_text($body)
    {
        if (is_array($this->recipientEmail)) {
            foreach($this->recipientEmail as $recipient) {
                PerchUtil::send_email($recipient, $this->senderEmail, $this->senderName, $this->subject, $body);
            }
        }else{
           PerchUtil::send_email($this->recipientEmail, $this->senderEmail, $this->senderName, $this->subject, $body); 
        }
    }
    
    
    private function build_message()
    {
        $path		= $this->template_path;
        $template   = $this->template;
        $data       = $this->vars;
		
		// test for data
		if (!is_array($data)){
			PerchUtil::debug('No data sent to email templating engine.', 'notice');
			return false;
		}
				
			
		// check if template is cached
		if (isset($this->cache[$template])){
			// use cached copy
			$contents	= $this->cache[$template];		
		}else{
			// read and cache		
			if (file_exists($path)){
				$contents 	= file_get_contents($path);
				$this->cache[$template]	= addslashes($contents);
			}
		}
		
		if (isset($contents)){
			$this->template_data 	= $data;
			$contents			    = preg_replace_callback('/\$(\w+)/', array($this, "substitute_vars"), $contents);
			$this->template_data 	= '';
			
			return stripslashes($contents);
		}else{
			PerchUtil::debug('Template does not exist: '. $template, 'error');
			return false;
		}
    }
    
    private function substitute_vars($matches)
    {
    	$tmp_template_data = $this->template_data;
    	if (isset($tmp_template_data[$matches[1]])){
    		return $tmp_template_data[$matches[1]];
    	}else{
    		PerchUtil::debug('Template variable not found: '.$matches[1], 'notice');
    		return '';
    	}
    }
    

}
?>