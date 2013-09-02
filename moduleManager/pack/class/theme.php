<?php

class site_theme extends tmp_theme {

    public function autoload() {
        return true;
    }
    
    public function path() {
        return "/site/theme/";
    }
    
    public function name() {
        return "Theme";
    }

} /*END CLASS*/