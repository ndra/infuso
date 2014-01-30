<?php

class forum_theme extends tmp_theme {

    public function autoload() {
        return true;
    }
    
    public function path() {
        return "/forum/theme/";
    }
    
    public function name() {
        return "Theme";
    }
    
    public function base() {
        return "forum";
    }

} /*END CLASS*/