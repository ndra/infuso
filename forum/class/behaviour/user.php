<?php
/**
 * Поведение для модели Пользователя
 **/
class forum_behaviour_user extends mod_behaviour {
        
    /**
    * Подключаем поведение к классу
    **/
    public function addToClass() {
        return "user";
    }
    
    /**
    * Кол-во сообщений пользователя на форуме
    **/
    public function forumCountPosts() {
        return forum_post::all()->eq("userID", $this->id())->count();
    }
}