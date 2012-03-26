<?php

class PerchAPI_HTML
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
    
    public function title_panel_start()
    {
        return '<div id="h1">';
    }
    
    public function title_panel_end()
    {
        $Perch = Perch::fetch();
        $help_html = $Perch->help_html;
        return $help_html.'</div>';
    }
    
    public function side_panel_start()
    {
        return '<div id="side-panel">';
    }
    
    public function side_panel_end()
    {
        $r = '<div class="clear"></div>';
        $r .= '</div>';
        
        return $r;
    }
    
    public function main_panel_start()
    {
        return '<div id="main-panel">';
    }
    
    public function main_panel_end()
    {
        $r = '<div class="clear"></div>';
        $r .= '</div>';
        
        return $r;
    }
    
    
    public function heading1($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
       
        return '<h1>'.$string.'</h1>';
    }

    public function heading2($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<h2>'.$string.'</h2>';
    }
    
    public function heading3($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<h3><span>'.$string.'</span></h3>';
    }
    
    public function heading3filter($string, $opts)
    {
        $s = '<h3 class="em"><span>'.$this->Lang->get($string).'<span class="filter">';
        
        foreach($opts as $opt) {
            $s .= '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/apps/'.$this->app_id.'/').'?by='.$opt['slug'].'" class="filter-'.$opt['slug'].' '.($opt['selected']?'selected':'').'">'.$this->Lang->get($opt['title']).'</a> ';
        }
        
        $s .= '</span></span></h3>';
        
        return $s;
    }
    
    public function heading4($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<h4>'.$string.'</h4>';
    }
    
    
    public function para($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<p>'.$string.'</p>';
    }
    
    public function form_help($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<p class="form-help">'.$string.'</p>';
    }
    
    public function warning_message($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<p class="alert-notice">'.$string.'</p>';
    }
    
    public function success_message($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<p class="alert-success">'.$string.'</p>';
    }
    
    public function failure_message($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return '<p class="alert-failure">'.$string.'</p>';
    }
    
    public function icon($type='tick', $alt='Success')
    {
        switch($type) {
            
            case 'tick':
                $file = 'icon_tick.gif';
                break;
            case 'warn':
                $file = 'icon_warn.gif';
                break;
            case 'draft':
                $file = 'icon_draft.png';
                break;
            case 'notice':
                $file = 'icon_notice.gif';
                break;
            case 'page-preview':
                $file = 'icon_page_preview.png';
                break;
            case 'page':
                $file = 'icon_page.png';
                break;
            case 'pages':
                $file = 'icon_pages.png';
                break;
            case 'undo':
                $file = 'icon_undo.png';
                break;
            case 'user':
                $file = 'icon_user.png';
                break;
        
            default:
                $file = false;
                break;
        }
        
        if ($file) {
            return '<img src="'.PERCH_LOGINPATH.'/assets/img/'.$file.'" alt="'.$this->encode($alt).'" />"';
        }
        
    }
    
    public function paging($Paging)
    {
        $paging = $Paging->to_array();

        if ((int)$paging['total_pages']<2) return '';
        
        $s = '<div class="paging">';
        
        if (isset($paging['not_first_page']) && $paging['not_first_page']) {
            $s .= '<a class="paging-prev" href="'.$paging['prev_url'].'">'.$this->Lang->get('Previous').'</a> ';
        }
        
        if (isset($paging['not_last_page']) && $paging['not_last_page']) {
            $s .= '<a class="paging-next" href="'.$paging['next_url'].'">'.$this->Lang->get('Next').'</a> ';
        }
        
        $s .= '</div>';
        
        return $s;
    }
    
    
    
    public function encode($string)
    {
        return PerchUtil::html($string);
    }
}

?>