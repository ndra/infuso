<?

/**
 * Модель письма в админке
 **/
class user_mail extends reflex {

    public function all() {
        return reflex::get(get_class())->desc("sent");
    }
    
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    public function dataWrappers() {
        return array(
            "subject" => "mixed/data",
            "from" => "mixed/data",
            "to" => "mixed/data",
            "message" => "mixed/data",
            "glue" => "mixed/data",
            "done" => "mixed/data",
            "sent" => "mixed/pdata",
        );
    }

    public function reflex_table() {
        return array(
            "name" => "user_mail",
            "fields" => array(
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    "label" => "Отправлено",
                    'name' => "sent",
                    'type' => "x8g2-xkgh-jc52-tpe2-jcgb",
                    "editable" => 2,
                    "default" => "now()",
                ), array(
                     "label" => "Прочитано",
                    'name' => "read",
                    'type' => "fsxp-lhdw-ghof-1rnk-5bqp",
                    "editable" => 2,
                ), array (
                    "label" => "Пользователь",
                    'name' => 'userID',
                    'type' => "pg03-cv07-y16t-kli7-fe6x",
                    "class" => "user",                    
                    "editable" => 2,
                ), array (
                    "label" => "От",
                    'name' => 'from',
                    'type' => "v324-89xr-24nk-0z30-r243",
                    "editable" => 2,
                ), array (
                    "label" => "Кому",
                    'name' => 'to',
                    'type' => "v324-89xr-24nk-0z30-r243",
                    "editable" => 2,
                ), array (
                    "label" => "Тема",
                    'name' => 'subject',
                    'type' => "v324-89xr-24nk-0z30-r243",
                    "editable" => 2,
                ), array (
                    "label" => "Сообщение",
                    'name' => 'message',
                    'type' => "kbd4-xo34-tnb3-4nxl-cmhu",
                    "editable" => 2,
                ), array (
                    "label" => "Код склейки",
                    'name' => 'glue',
                    'type' => "v324-89xr-24nk-0z30-r243",
                    "editable" => 2,
                ), array (
                    "label" => "Отправить после данного времени (для склеек)",
                    'name' => 'glueSendAfter',
                    'type' => "x8g2-xkgh-jc52-tpe2-jcgb",
                    "editable" => 2,
                ), array(
                    "label" => "Отправлено",
                    'name' => 'done',
                    'type' => "fsxp-lhdw-ghof-1rnk-5bqp",
                    "editable" => 2,
                ), array(
                    "label" => "Параметры",
                    'name' => 'params',
                    'type' => "puhj-w9sn-c10t-85bt-8e67",
                    "editable" => 2,
                )
            ),
        );
    }
    
    /**
     * Возвращает список писем, склееных с данным
     **/
    public function glueMails() {
        return self::all()
			->eq("to",$this->to())
			->eq("glue",$this->glue())
			->eq("done",0);
    }
    
    public function reflex_beforeCreate() {
        // Устанавливаем задержку в 10 минут до отправки
        $this->data("glueSendAfter",util::now()->shift(60*10));
    }
    
    /**
     * Возвращаей мейлер с параметрами данного письма
     **/
    public function mailer() {
    
        $user = user::byEmail($this->to());
        
        if(!$user->exists()) {
            $user = user::virtual(array(
                "email" => $this->to(),
            ));
        }
        
        // СОздаем мейлер
        $mailer = $user->mailer();
        
        // Записываем в параметры мейлера параметры этого письма
        $mailer->params($this->pdata("params"));
        
        $mailer->from($this->from());
        $mailer->to($this->to());
        $mailer->message($this->message());
        return $mailer;
        
    }

}

