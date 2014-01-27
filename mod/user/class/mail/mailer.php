<?

/**
 * Отправка письма
 *
 * $mail = new user_mailer("Я <@>");
 * $mail->subject("Руссссс")
 *      ->message("Текст тест русс")
 *      ->from("Русс <@>");
 * $mail->attach("/site/res/img.jpg");
 * $mail->send();
 *
 * @package user.mail
 * @author Petr.Grishin
 **/
class user_mailer extends mod_component {

    private $log = null;

    public function initialParams() {
        return array(
            "subject" => "",
            "message" => "",
            "from" => "",
            "to" => "",
            "layout" => "",
            "headers" => array(),
            "type" => "text/plain",
            "code" => null, // Код типа письма (используется для подключения шаблона)
            "glue" => null, // Код склейки писем
            "prepareHtml" => true, //Подготавливает текст перед отправкой в формате HTML
            "disable" => false, //Заблокированная отправка сообщений,
            "attachments" => array(),
            "businessRules" => "",
        );
    }
    
    /**
     * Статический метод для использования Mail builder'a
     *
     * @param string $to E-mail получателя
     * @return class user_mailer
     * @author Petr.Grishin
     **/
    public static function mailer($to) {
        $mail = new self();
        $mail->to($to);
        //Параметры по умолчанию
        $mail->from(mod::conf("user:email_from"));
        $mail->layout(mod::conf("user:email_template"));
        return $mail;
    }

    /**
     * Задает тип письма как html
     **/
    public function html() {
        $this->type("text/html");
        return $this;
    }

    /**
     * Возвращает список датаврапперов для сайта
     **/
    public function dataWrappers() {
        return array(
            "subject" => "mixed",
            "message" => "mixed",
            "from" => "mixed",
            "to" => "mixed",
            "code" => "mixed",
            "glue" => "mixed",
            "codeAfterGlue" => "mixed",
            "layout" => "mixed",
            "type" => "mixed",
            "headers" => "mixed",
            "userID" => "mixed",
            "prepareHtml" => "mixed",
            "disable" => "mixed",
        );
    }

    /**
     * Добавляет заголовок в письмо
     */
    public function addHeaders($header) {
        $headers = $this->param("headers");
        $headers[] = $header;
        $this->param("headers",$headers);
        return $this;
    }

    /**
     * Обработка письма reflex-шаблоном
     **/
    public function processTemplate() {

        // Находим шаблон с таким же кодом как у текущего почтового события
        // Если шаблон не найден, создаем его
        $tmp = user_mail_template::all()->eq("code",$this->code())->one();

        //Отключаем отправку писем если такой параметр указан в шаблоне
        if ($tmp->disable()) {
            $this->disable(true);
        }

        if(!$tmp->exists()) {
            $tmp = reflex::create("user_mail_template",array(
                "code" => $this->code(),
                "from" => $this->from(),
                "subject" => $this->subject(),
                "message" => $this->message(),
                "layout" => $this->layout(),
            ));
        }

        // Подготавливаем параметры для подстановки в шаблон в стиле %%name%%
        $replace = array();
        foreach($this->params() as $key=>$val) {
            if(is_scalar($val)) {
                $replace["%%".$key."%%"] = $val;
            }
        }


        // Записываем в шаблон параметры
        $tmp->data("params",implode(", ",array_keys($replace)));

        // Если шаблон включен, выполняем его
        if ($tmp->data("enable") == true) {

            // Параметры письма, которые будут заменены шаблоном
            $templateKeys = array("from","subject","message","layout");
            
            foreach($templateKeys as $key) {
                // Заменяем %%key%%
                if($code = trim($tmp->data($key))) {
                    $this->param($key,$this->evalPart($code));
                }
            }
        }
    }
    
    public function evalPart($code) {
    
        ob_start();
        $this->evalCode(" ?".">{$code}<"."?php ",$this->params());
        $code = ob_get_clean();
        
        $replace = array();
        foreach($this->params() as $key=>$val) {
            if(is_scalar($val)) {
                $replace["%%".$key."%%"] = $val;
            }
        }
        
        $code = strtr($code,$replace);
        return $code;
        
    }

    /**
     * Прикрепляет файл к письму
     * @var $file string Файл для прикрепления от корня веб проекта
     * @author Petr.Grishin
     **/
    public function attach($file = null, $name = null, $cid = null) {

        if ($file === null || $file == "")
            return $this;

        return $this->attachNative(mod_file::get($file)->native(), $name, $cid);
    }


    /**
     * Прикрепляет файл к письму
     * @var $file string Файл для прикрепления Нативный
     * @author Petr.Grishin
     **/
    public function attachNative($file = null, $name = null, $cid = null) {

        if ($file === null || $file == "")
            return $this;

        if ($name === null || $name == "") {
            $name = mod_file::get($file)->name();
        }

        $attachments = $this->param("attachments");
        $attachments[] = array(
            "name" => $name,
            "file" => $file,
            "cid" => $cid,
        );
        $this->param("attachments",$attachments);

        return $this;
    }

    /**
     * Возвращает объект пользователя, которому адресовано сообщение
     **/
    public function user() {
        return user::get($this->param("userID"));
    }

    /**
     * Получаем список прикрепленных к письму файлов
     **/
    public function attachments() {
        return $this->attachments;
    }

    /**
     * Непосредственно отправляет сообщение
     * @author Petr.Grishin
     **/
    public function send() {

        mod::fire("user_beforeMail", array("mail" => $this));

        // Обработка пользовательскими шаблонами
        if($this->code()) {
            $this->processTemplate();
        }

        // Если письмо заблокированно возвращаем false
        if ($this->disable()) {
            return;
        }

        $message = $this->message();

        if ($this->type() == "text/html") {
            $this->textToHTML();
        }

        $this->logMail();

        // Если нет склейки, отправляем письмо сразу
        if(!$this->glue()) {

            // Обработка каркаса
            if ($this->layout()) {
				$message = $this->evalPart($this->layout());
            }

            // Выполняем бизнес правила и отправляем письмо
            if($this->evalBusinessRules()) {
                $mailer = mod::service("mailer");
                $mailer->params($this->params());
                $mailer->param("message",$message);
                $mailer->send();
            }

            $this->log()->data("done",true);
        }

    }

    /**
     * Записывает письмо в лог
     **/
    public function logMail() {

        // Сохраняем письмо в лог
        $log = reflex::create("user_mail",array(
            "userID"=> $this->user()->id(),
            "to" => $this->to(),
            "from" => $this->from(),
            "subject" => $this->subject(),
            "message" => $this->message(),
            "glue" => $this->glue(),
            "params" => $this->params(),
        ));

        $this->log = $log;

    }

    /**
     * Возвращает объект лога, в который было сохранено сообщение
     * Если сообщение не было сохранено, возвращает виртуальный объект лога
     **/
    public function log() {
        if($this->log) {
            return $this->log;
        }
        return reflex::virtual("user_mail");
    }

    /**
     * Подготавливает текст перед отправкой в формате HTML
     *
     * @return Object
     * @author Petr.Grishin
     **/
    private function textToHTML() {

        $text = $this->param("message");

        if (!$this->param("prepareHtml")) {
            return $text;
        }

        //Если в тексте нет ни одного HTML Тега
        if (!preg_match("/<.*>/", $text)) {
            //Добавляет переносы
            $text = nl2br($text, true);
            //Преобразует ссылки
            $text = self::prepareLinks($text);
        }

        $this->param("message",$text);
        return $this;
    }

    /**
     * Выполняет бизнес-правила этого письма и возвращает то что вернул выполненный код
     * Если у письма отсутствуют бизнес-правила, вернет true
     **/
    public function evalBusinessRules() {
        if(!trim($this->param("businessRules"))) {
            return true;
        }
        return eval($this->param("businessRules"));
    }

    /**
     * Преобразует ссылки в тексте. Список ссылок: http, https, ftp, www, e-mail(@)
     *
     * @return string
     * @author Petr.Grishin
     **/
    private function prepareLinks($text) {
        $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $text);
        $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" >$3</a>", $text);
        $text = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);
        return($text);
    }


}
