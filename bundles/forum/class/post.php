<?php
/**
 * Модель и контроллер
 *
 * @package site
 * @author Petr.Grishin
 **/
class forum_post extends reflex {

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
    
    
    public function index_edit($p){
        $post = self::get($p["id"]);
        tmp::exec("/forum/topic/editPost",array(
            "post" => $post,
        ));    
    }
    
    public function initialParams() {
        return array(
            "autoMailSubscribers" => true,
        );
    }
    
    /**
     * Возвращает текущею коллекцию
     *
     * @return reflex_list
     **/
    public static function all() {
        return reflex::get(get_class())
            ->asc("editDate")
            ->limit(0);
    }

    /**
     * Возвращает пост по id
     *
     * @return forum_post
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    /**
     * Вывод родительской группы
     *
     * @return reflex
     **/
    public function reflex_parent() {
        return $this->topic();
    }
    
    /**
     * Дочерние элементы для каталога
     **/
    public function reflex_children() {
        return array(
            $this->attachments()->title("Вложения"),
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
     * Возвращает коллекцию последних сообщений
     *
     * @return reflex_list
     **/
    public static function recent() {
        return reflex::get(get_class())
            ->desc("date");
    }

    /**
     * Вывод автора
     *
     * @return reflex
     **/
    public function user() {
        return $this->pdata("userID");
    }

    /**
     * Вывод сообщения
     *
     * @return reflex
     **/
    public function message() {
        return $this->data("message");
    }


    /**
     * Вывод даты создания
     *
     * @return reflex
     **/
    public function date() {
        return $this->pdata("date");
    }

    /**
     * Возвращает прикрепленные файлы
     *
     * @return reflex
     **/
    public function attachments() {
        return forum_postAttachments::all()->eq("postId", $this->id());
    }

    /**
     * Вывод родительской группы
     *
     * @return reflex
     **/
    public function topic() {
        return $this->pdata("topic");
    }

    /**
     * Приводит $_FILES в нормальный масив файлов
     *
     * @autor christiaan at baartse dot nl <http://php.net/manual/ru/features.file-upload.multiple.php#96365>
     * @return array
     **/
    private static function normalizePostFiles($entry) {

        if(isset($entry['name']) && is_array($entry['name'])) {
            $files = array();
            foreach($entry['name'] as $k => $name) {
                $files[$k] = array(
                    'name' => $name,
                    'tmp_name' => $entry['tmp_name'][$k],
                    'size' => $entry['size'][$k],
                    'type' => $entry['type'][$k],
                    'error' => $entry['error'][$k]
                );
            }
            return $files;
        }

        return $entry;
    }


    /**
     * Создать сообщение в Topic
     *
     * @return array
     **/
    public function post_create($p = null) {

        $topic = reflex::get("forum_topic", $p["topic"]);

        if (!user::active()->exists()) {
            throw new mod_userLevelException("Вы не авторизовались");
        }

        if (!$topic->exists()) {
            throw new mod_userLevelException("Темы не существует");
        }

        if ($topic->close()) {
            throw new mod_userLevelException("Тема закрыта для новых сообщений");
        }

        $form = form::byCode("forum_post_create_msnjn4jsbj");
        if(!$form->validate($p)) {
            throw new mod_userLevelException("Ошибка валидации формы");
        }

        $post = reflex::create("forum_post",array(
            "topic" => $topic->id(),
            "title" =>  "Re: ".$topic->title(),
            "message" => $p["message"],
            "userID" => user::active()->id(),
        ));

        // Добавляем файлы из вложений
        if(array_key_exists("file",$_FILES)) {
            foreach (self::normalizePostFiles($_FILES['file']) as $file) {

                if (!$file['name']) {
                    continue;
                }
                
                if($file["error"]!=0) {
                    mod::msg("Не удалось закачать файл. Код: ".$file["error"],1);
                    continue;
                }

                $filePath = $post->storage()->addUploaded($file['tmp_name'], $file['name']);
                reflex::create("forum_postAttachments", array(
                    "postId" => $post->id(),
                    "title" => $file['name'],
                    "file" => $filePath,
                ));

            }
        }
        
        $params = array (
            "message" => "Новое сообщение на форуме в теме: ".$post->topic()->title(),
            "subject" => "Новое сообщение на форуме в теме: ".$post->topic()->title(),
            "postMessage" => $post->message(),
            "groupTitle" => $post->topic()->title(),
            "postUrl" => $post->url()->absolute()."",
            "topicUrl" => $post->topic()->url()->absolute()."",
            "topicTitle" => $post->topic()->title(),
            "author" => $post->author()->title(),
            "authorID" => $post->author()->id(),
            "code" => "forum/newPost",
        );

        // Подписываю автора на этот Topic
        user::active()->subscribe("forum:topic:".$post->topic()->id(), $params);
        
        if($post->param("autoMailSubscribers")){
            $post->_mailSubscribers($params);
        }
        
        // редиректим к сообщению
        header("Location: " . $post->url());
        die();

    }
    
    /**
    * Раcсылка уведомлений пользователям подписанным на тему
    **/
    public function _mailSubscribers($params = null) {
        $post = $this;
        if(!$params) {
            $params = array (
                "message" => "Новое сообщение на форуме в теме: ".$post->topic()->title(),
                "subject" => "Новое сообщение на форуме в теме: ".$post->topic()->title(),
                "postMessage" => $post->message(),
                "groupTitle" => $post->topic()->title(),
                "postUrl" => $post->url()->absolute()."",
                "topicUrl" => $post->topic()->url()->absolute()."",
                "topicTitle" => $post->topic()->title(),
                "author" => $post->author()->title(),
                "authorID"=> $post->author()->id(),
                "code" => "forum/newPost",
            );
        }

        // Рассылаем всем о том что создан ответ в теме
        user_subscription::mailByKey("forum:topic:".$post->topic()->id(), $params);

        // Рассылаем всем о том что есть ответ в текущем разделе
        user_subscription::mailByKey("forum:group:".$post->topic()->group()->id(), $params);

        // Рассылаем всем о том что есть ответ в "Родительских" разделах
        foreach ($post->topic()->group()->parents() as $group) {
            user_subscription::mailByKey("forum:group:".$group->id(), $params);
        }
    }
    
     /**
     * Редактирует имещиесе сообщение
     *
     * @return array
     **/
    public function post_edit($p = null) {
        $user = user::active();
        $post =  self::get($p["post"]);
        
        if (!$user->exists()) {
            throw new mod_userLevelException("Вы не авторизовались");
        }
        
        if ($user->id() != $post->data("userID")) {
            throw new mod_userLevelException("Вы не можете редактировать это сообщение");
        }

        if (!$post->exists()) {
            throw new mod_userLevelException("Такого поста не существует");
        }
        
        $form = form::byCode("forum_post_edit_sjxnr6l0j0");
        if(!$form->validate($p)) {
            throw new mod_userLevelException("Ошибка валидации формы");
        }
        
        $date = util::now();
        
        $post->data("message", $p["message"]);
        $post->data("editDate",$date);
        $post->data("edited", 1);
        
        // Добавляем файлы из вложений
        if(array_key_exists("file",$_FILES)) {
            foreach (self::normalizePostFiles($_FILES['file']) as $file) {

                if (!$file['name']) {
                    continue;
                }
                
                if($file["error"]!=0) {
                    mod::msg("Не удалось закачать файл. Код: ".$file["error"],1);
                    continue;
                }

                $filePath = $post->storage()->addUploaded($file['tmp_name'], $file['name']);
                reflex::create("forum_postAttachments", array(
                    "postId" => $post->id(),
                    "title" => $file['name'],
                    "file" => $filePath,
                ));

            }
        }
        
        //удаляем файлы которые были выбраны для удаления
        $attachIds = explode(" ", $p["deletedattachments"]);
        
        if(count($attachIds)>0){
            foreach($attachIds as $attachId){
                if($attachId == ""){
                    continue;
                }
                $atach = forum_postAttachments::get($attachId);
                if($atach->exists() && ($atach->data("postId") == $post->id())){
                    $atach->delete();    
                }else{
                    throw new mod_userLevelException("Вы не можете удалить это вложение");    
                }    
            }
        }
        
        header("Location: " . $post->url()); 
        die();
            
    }
    
    public function actualDate() {
        if($this->data("edited")){
            return $this->pdata("editDate");
        }
        
        return $this->pdata("date"); 
    }
    
    public function author() {
        return $this->pdata("userID");
    }

    /**
     * Возвращает посты перед данным (из того же топика)
     **/
    public function postsBefore() {
        return $this->topic()->posts()->lt("date",$this->date());
    }

    /**
     * Возвращает ссылку на пост
     * Т.к. у постов нет отдельной страницы, то ссылка формируется следующим образом
     * = {$topicURL}?page={$postPage}#post-{$postID}
     **/
    public function reflex_url() {
        $page = floor($this->postsBefore()->count() / $this->postsBefore()->perPage()) + 1;
        return $this->topic()->url()."?page=".$page."#post-".$this->id();
    }


} //END CLASS
