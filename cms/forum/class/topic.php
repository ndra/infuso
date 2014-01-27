<?php
/**
 * Модель и контроллер
 *
 * @package site
 * @author Petr.Grishin
 **/
class forum_topic extends reflex {

    public function initialParams() {
        return array(
            "postsPerPage" => 20, // Количество сообщений на одну страницу
        );
    }  
    
    /**
     * Видимость класса для http запросов
     *
     * @return boolean
     **/
    public static function indexTest() {
        return true;
    }
    
    /**
     * Вывод эелемента колекции
     *
     * @return array
     **/
    public function index_item($p = null) {
        
        if (!$p["id"]) {
            throw new Exception("Не задана тема");
        }
        
        $topic = self::get($p["id"]);
        $topic->registerView();
        
        $posts = $topic->posts()
            ->addBehaviour("reflex_filter");
            
        $posts->page($p["page"]);
        
        tmp::exec("/forum/topic", array(
            "topic" => $topic,
            "posts" => $posts,
        ));
    }
    
    /**
     * Создать тему
     *
     * @return array
     **/
    public function index_create($p = null) {
        
        if (!user::active()->exists()) {
            throw new Exception("Вы не авторизовались");
        }
        
        if (!$p["group"]) {
            throw new Exception("Не указан раздел для темы");
        }
            
        $group = forum_group::get($p["group"]);
        
        if (!$group->exists()) {
            throw new Exception("Не найден указанный раздел");
        }
        
        $_group = $group->parents();
        array_push($_group, $group);        
        
        tmp::exec("/forum/topic-create", array(
            "group" => $group
        ));
        
    }
    
    /**
     * Видимость класса для POST запросов
     *
     * @return boolean
     **/
    public function postTest() {
        return true;
    }
    
    /**
     * Метаданные в админке
     *
     * @return boolean
     **/
    public function reflex_meta() {
        return true;
    }
    
    /**
     * Возвращает текущею коллекцию
     *
     * @return reflex_list
     **/
    public static function all() { 
        return reflex::get(get_class())
            ->asc("date");
    }
    
    /**
     * Возвращает текущею коллекцию
     *
     * @return reflex_list
     **/
    public static function get($id) { 
        return reflex::get(get_class(),$id);
    }
    
    /**
     * Возвращает кол-во ответов
     *
     * @return reflex_list
     **/
    public function countPosts() { 
        return $this->posts()->count();
    }
    
    /**
     * Возвращает последнее сообщение
     *
     * @return reflex_list
     **/
    public function latestPost() { 
        return $this->posts()->desc("date")->one();
    }
    
    /**
     * Возвращает url последнего поста (с учетом пейджера и хэштэга)
     **/
    public function latestPostURL() {     
		return $this->latestPost()->url();
    }
    
    /**
     * Возвращает кол-во просмотров
     *
     * @return integer
     **/
    public function countViews() { 
        return $this->data("views");
    }
    
    public function registerView() {
        $this->data("views",$this->data("views")+1);
    }
    
    /**
     * Регистрируем просмотр темы
     *
     * @todo Требует рефакторинг
     * @return integer
     **/
    public function incrementViews() {
        $views = (integer) $this->data("views");
        $this->data("views", $views + 1);
    }
    
    /**
     * Вывод родительской группы
     *
     * @return reflex
     **/
    public function group() {
        return $this->pdata("group");
    }
    
    /**
     * Возвращает принадлежит ли данная тема тек. пользователю
     *
     * @return boolean
     **/
    public function my() {
        return $this->pdata("userID")->id() == user::active()->id();
    }
    
    /**
     * Закрывает тему
     *
     * @return boolean
     **/
    public function close($p = null) {
        if(func_num_args() == 0) {
            return $this->data("close");
        }
        
        if(func_num_args() == 1) {
            $this->data("close", $p);
            return $this;
        }
    }
    
    /**
     * Вывод родительской группы
     *
     * @return array
     **/
    public function reflex_parent() {
        return $this->group();
    }
    
    /**
     * Вывод сообщений
     *
     * @return reflex_list
     **/
    public function posts() {
        return reflex::get("forum_post")
            ->eq("topic", $this->id())
            ->asc("date")
            ->limit($this->param("postsPerPage"));
    }
    
    /**
     * Дочерние группы
     *
     * @return array
     **/
    public function reflex_children()
    {
        return array(
            $this->posts()->removeRestrictions("date")->desc("date")->title("Сообщения"),
        ); 
    }
    
    /**
     * Настройка админки Название Группы влевом меню
     *
     * @return array
     **/
    public function reflex_rootGroup() {
        return "Форум";
    }
    
    /**
     * Подписаться на тему
     *
     * @return array
     **/
    public function post_subscribe($p = null) {
    
        if (!user::active()->exists())
            throw new Exception("Вы не авторизовались");
            
            
        $topic = reflex::get("forum_topic", $p["id"]);
        
        //Подписываю на Topic
        user::active()->subscribe("forum:topic:".$topic->id(), "Новое сообщение на форуме в теме: " . $topic->title());
        
        mod::msg("Вы подписаны на тему: " . $topic->title());
    }
    
    /**
     * Закрыть тему
     *
     * @return array
     **/
    public function post_close($p = null) {
    
        if (!user::active()->exists()) {
            throw new Exception("Вы не авторизовались");
        }
            
        $topic = reflex::get("forum_topic", $p["id"]);
        
        if (!$topic->my()) {
            throw new Exception("Вы не являетесь автором темы");
        }
        
        //Закрываем тему
        $topic->close(true);
        
        mod::msg("Вы закрыли тему: " . $topic->title());
        
    }
    
    /**
     * Подписаться на тему
     *
     * @return array
     **/
    public function post_create($p = null) {
        
        if (!$p["title"]) {
            throw new Exception("Не указано имя темы");
        }
            
        if (!$p["group"]) {
            throw new Exception("Не указан раздел для темы");
        }
                
        $group = forum_group::get($p["group"]);
        
        if (!$group->exists()) {
            throw new Exception("Не найден указанный раздел");
        }
        
        // Текущий пользователь
        $user = user::active();
        
        $topic = reflex::create(get_class(), array (
            "title" => $p["title"],
            "group" => $group->id(),
            "userID" => $user->id(),
        ));
        
        header("Location: " . $topic->url());    
    }
    
} //END CLASS
