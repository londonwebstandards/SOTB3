<?php

class PerchAPI_Email
{
    private $subject;
    private $senderName;
    private $senderEmail;
    private $recipientEmail;
    private $body;
    private $files = array();
    
    private $headers ='';

    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id = $app_id;
        $this->version = $version;
        
        $this->Lang = $Lang;
    }
    
    public function subject($str=false)
    {
        if ($str === false) {
            return $this->subject;
        }
        
        $this->subject = $str;
    }
    
    public function body($str=false)
    {
        if ($str === false) {
            return $this->body;
        }
        
        $this->body = $str;
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
    
    public function attachFile($name, $path, $mimetype)
    {
        $file = array();
        $file['name'] = $name;
        $file['path'] = $path;
        $file['mimetype'] = $mimetype;
        $this->files[] = $file;
    }
    
    public function send()
    {
        $body = $this->_build_message();
        
        $this->_send_plain_text($body);
    }
    
    private function _build_message()
    {
        $random_hash = md5(date('r', time()));
        $this->headers .= 'Content-Type: multipart/mixed; boundary="PHP-mixed-'.$random_hash.'"'."\r\n";
        
        $output = "\n";
        $output .= "--PHP-mixed-$random_hash;\n";
        $output .= "Content-Type: multipart/alternative; boundary='PHP-alt-$random_hash'\n";
        $output .= "--PHP-mixed-$random_hash\n";
        $output .= "Content-Type: text/plain; charset='utf8'\n";
        $output .= "Content-Transfer-Encoding: 7bit\n\n";
        
        $output .= $this->body."\n\n";
                
        if (PerchUtil::count($this->files)) {
            foreach($this->files as $file) {
                $attachment = chunk_split(base64_encode(file_get_contents($file['path'])));
                
                $output .= "--PHP-mixed-$random_hash\n";
                $output .= "Content-Type: ".$file['mimetype']."; name=".$file['name']."\n";
                $output .= "Content-Transfer-Encoding: base64\n";
                $output .= "Content-Disposition: attachment\n\n";
                $output .= $attachment."\n\n";
            }
        }
        
        $output .= "--PHP-mixed-$random_hash--\n\n";
        
        return $output;
        
    }
    
    
    private function _send_plain_text($body)
    {
        if (is_array($this->recipientEmail)) {
            foreach($this->recipientEmail as $recipient) {
                PerchUtil::send_email($recipient, $this->senderEmail, $this->senderName, $this->subject, $body, $this->headers);
            }
        }else{
           PerchUtil::send_email($this->recipientEmail, $this->senderEmail, $this->senderName, $this->subject, $body, $this->headers); 
        }
    }

    
}

?>