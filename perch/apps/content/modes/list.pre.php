<?php

    $do_list_collapse = $Settings->get('content_collapseList')->settingValue();

    $filter = false;
    if (isset($_GET['by']) && $_GET['by']!='') {
        $filter = $_GET['by'];
    }

    if (!$filter && isset($_GET['page']) && $_GET['page'] != '') {
        $filter = 'page';
        $page = $_GET['page'];
    }
    
    if (!$filter && isset($_GET['type']) && $_GET['type'] != '') {
        $filter = 'type';
        $type = $_GET['type'];
        $template = $type.'.html';
    }
    
    switch ($filter) {
        case 'all':
            $contentItems = $PerchContent->get_list();
            $heading = PerchLang::get('All Content');
            break;
            
        case 'new':
            $contentItems = $PerchContent->get_list('new');
            $heading = PerchLang::get('New Content');
            break;

        case 'page':
            $contentItems = $PerchContent->get_list('page', $page);
            if ($page == '*') {
                $heading = PerchLang::get('Content shared with all pages');
            }else{
                $heading = PerchLang::get('Content for page: '). $page;
            }
            
            
            break;

        case 'type':
            $contentItems = $PerchContent->get_list('template', $template);
            $heading = PerchLang::get('Content of type'). ': ' . ucfirst($type);
            break;

        default:
            $contentItems = $PerchContent->get_list();
            $heading = PerchLang::get('All Content');
            break;
    }
    
    if (!$filter) $filter = 'all';

    if ($filter!='all') $do_list_collapse = false;

    $page_has_drafts = false;

?>