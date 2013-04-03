<?php
/**
 * Модель
 *
 * @package site
 * @author Petr.Grishin
 **/
class forum_postAttachments extends reflex {
    
    /**
     * Возвращает текущею коллекцию
     *
     * @return reflex_list
     **/
    public static function all() { 
        return reflex::get(get_class())
            ->limit(0);
    }
    
    /**
     * Возвращает true если файл являеться фотографией
     *
     * @return boolean
     **/
    public function typeImg() {
        $typeImg = array("jpg", "jpeg", "png", "gif");
        $ext = $this->pdata("file")->ext();
        return in_array($ext, $typeImg);
    }
    
    
} //END CLASS