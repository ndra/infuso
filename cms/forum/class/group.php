<?php
/**
 * Модель и контроллер
 *
 * @package site
 * @author Petr.Grishin
 **/
class forum_group extends reflex {

    /**
     * Видимость класса для http запросов
     *
     * @return boolean
     **/
    public static function indexTest() {
        return true;
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
     * Вывод колекции
     *
     * @return array
     **/
    public function index() {
        tmp::exec("/forum/index");
    }


    /**
     * Вывод эелемента колекции
     *
     * @return array
     **/
    public function index_item($p) {

        $group = self::get($p["id"]);
        tmp::exec("/forum/group",array(
            "group" => $group,
        ));
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
            ->asc("sort")
            ->param("sort",true);
    }

    /**
     * Возвращает группу по id
     *
     * @return forum_group
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    /**
     * Возвращает корневые элементы коллекции
     *
     * @return reflex_list
     **/
    public static function root() {
        return self::all()->eq('parent', 0);
    }


    /**
     * Возвращает кол-во сообщений в форуме
     *
     * @return reflex_list
     **/
    public function countPosts() {

        $count = 0;

        foreach ($this->children_topic() as $topic) {
            $count += $topic->countPosts();
        }

        return $count;
    }



    /**
     * Вывод родительской группы
     *
     * @return array
     **/
    public function reflex_parent() {
        return self::get($this->data("parent"));
    }


    /**
     * Вывод дочерних групп
     *
     * @return reflex_list
     **/
    public function childrenGroups() {
        return self::all()
            ->eq("parent", $this->id())
            ->asc("sort")
            ->param("sort",true);
    }


    /**
     * Вывод Тем
     *
     * @return reflex_list
     **/
    public function _topics() {
        return forum_topic::all()
            ->eq("group", $this->id())
            ->asc("date");
    }

    public function lastPost() {
        $topics = $this->topics()->idList();
        $post = forum_post::all()->eq("topic",$topics)->desc("date")->one();
        return $post;
    }


    /**
     * Дочерние группы
     *
     * @return array
     **/
    public function reflex_children() {
        return array(
            $this->childrenGroups()->title("Дочерние группы"),
            $this->topics()->title("Темы")
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


        $group = reflex::get("forum_group", $p["id"]);

        //Подписываю на Group
        user::active()->subscribe("forum:group:".$group->id(), "Новое сообщение на форуме: " . $group->title());

        mod::msg("Вы подписаны на форум: " . $group->title());
    }


} //END CLASS
