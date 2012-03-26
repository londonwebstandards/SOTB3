<?php
    $this->register_app('content', 'Content', 1, 'Default app for managing content', $this->version);
    $this->add_setting('editorMayDeleteRegions', 'Editors may delete regions', 'checkbox', false);
    $this->add_setting('content_collapseList', 'Collapse content list', 'checkbox', false);
?>