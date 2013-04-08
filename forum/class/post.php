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
    
    /**
     * Возвращает текущею коллекцию
     *
     * @return reflex_list
     **/
    public static function all() { 
        return reflex::get(get_class())
            ->asc("date")
            ->limit(0);
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
     * Вывод родительской группы
     *
     * @return reflex
     **/
    public function reflex_parent() {
        return $this->topic();
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
     * Приводит $_FILES в нормальный масив файлов
     * 
     * @autor christiaan at baartse dot nl <http://php.net/manual/ru/features.file-upload.multiple.php#96365>
     * @return array
     **/
    private static function _normalizePostFiles($entry) {
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
    
        if (!user::active()->exists()) {
            throw new Exception("Вы не авторизовались");
        }

        $topic = reflex::get("forum_topic", $p["topic"]); 
        
        if (!$topic->exists()) {
            throw new Exception("Темы не существует");
        }
        
        if ($topic->close()) {
            throw new Exception("Тема закрыта для новых сообщений");
        }
        
        $post = reflex::create("forum_post");
        
        $post->data("topic", $topic->id());
        
        $post->data("title", "Re: " . $topic->title());
        
        $post->data("message", $p["message"]);
        
        foreach (self::_normalizePostFiles($_FILES['file']) as $file) {
            
            if (!$file['name']) {
				continue;
			}
            
            $filePath = $post->storage()->addUploaded($file['tmp_name'], $file['name']);
            reflex::create("forum_postAttachments", array(
                "postId" => $post->id(),
                "title" => $file['name'],
                "file" => $filePath,
            ));
            
        }
        
        $post->data("userID", user::active()->id());
        
        $host = mod_url::current()->scheme()."://".mod_url::current()->host();
        
        $params = array (
            "message" => "Новое сообщение на форуме в теме: ".$post->topic()->title(),
            "subject" => "Новое сообщение на форуме в теме: ".$post->topic()->title(),
            "postMessage" => $post->message(),
            "groupTitle" => $post->topic()->title(),
            "url" => $post->url(),
		);
        
        //Подписываю автора на этот Topic
        user::active()->subscribe("forum:topic:".$post->topic()->id(), $params);
        
        //Рассылаем всем о том что создан ответ в теме
        user_subscription::mailByKey("forum:topic:".$post->topic()->id(), $params);
        
        //Рассылаем всем о том что есть ответ в текущем разделе
        user_subscription::mailByKey("forum:group:".$post->topic()->group()->id(), $params);
        
        //Рассылаем всем о том что есть ответ в "Родительских" разделах
        foreach ($post->topic()->group()->parents() as $group) {
            user_subscription::mailByKey("forum:group:".$group->id(), $params);
        }
        
        
        header("Location: " . $post->topic()->latestPostURL());
        die();
        
    }
    
    public function author() {
        return $this->pdata("userID");
    }
    
    
} //END CLASS
