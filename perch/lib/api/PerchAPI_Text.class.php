<?php

class PerchAPI_Text
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id = $app_id;
        $this->version = $version;
        
        $this->Lang = $Lang;
    }
    
    public function text_to_html($str)
    {
        switch(PERCH_APPS_EDITOR_MARKUP_LANGUAGE) {
            case 'textile' :
                $Textile = new Textile;
                $str  =  $Textile->TextileThis($str);
                break;

            case 'markdown' :
                $Markdown = new Markdown_Parser;
                $str = $Markdown->transform($str);
                break;
        }
        
        if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
		    $str = str_replace(' />', '>', $str);
		}
		
		return $str;
    }
}

?>