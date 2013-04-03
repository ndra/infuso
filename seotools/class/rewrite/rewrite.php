<?

class seotools_rewrite extends reflex {

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    public static function all() {
        return reflex::get(get_class());
    }
    
    public static function normalizeText($text) {
        $text = util::str($text);
        $text = $text->text();
        $text = $text->removeDuplicateSpaces();
        $text = trim($text);
        return $text;
    }
    
    /**
     * Включае управление реврайтом (при нажатии ctrl+r появится окно)
     **/
    public function inc() {
    
        if(mod_superadmin::check()) {
            tmp::jq();
            mod::coreJS();
            tmp::js("/seotools/res/rewrite.js");
        }
        
    }
    
    public static function replace($text,$debug=false) {
    
        if(!mod_superadmin::check()) {
            $debug = false;
        }
    
        foreach(self::all()->limit(0) as $replace) {
        
            $original = $replace->data("original");
            $replacement = $replace->data("replacement");
            
            if($debug)
                $replacement = "<span style='background:yellow;' title='{$original}' >".$replacement."</span>";
        
            $text = strtr( $text,
                array (
                    $original => $replacement,
                ));
        }
        
        return $text;
    
    }

}
