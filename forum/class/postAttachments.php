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
     * Возвращает атач по id
     *
     * @return forum_postAttachments
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
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
    
    public function post() {
        return $this->pdata("postId");
    }
    
    public function reflex_parent() {
        return $this->post();
    }
    
    public function reflex_storageSource() {
        return $this->post();
    }
    
    
} //END CLASS
