<?php
/**
 * Сервис для отправка письма через стандартный sendmail
 */
class mod_mailer extends \infuso\core\service {

    /**
     * Этот класс используеться по-умолчанию для сервиса "mailer"
     *
     * @return string
     */
    public function defaultService() {
        return "mailer";
    }

    /**
     * Параметры при инициализации сервиса
     *
     * @return array
     */
    public function initialParams() {
        return array(
            "subject" => "",
            "message" => "",
            "from" => "",
            "to" => "",
            "headers" => array(),
            "attachments" => array(),
            "type" => "text/plain",
        );
    }    
       
    /**
     * Задает тип письма как html
     *
     * @return $this
     */
    public function html() {
        $this->type("text/html");
        return $this;
    }    

    /**
     * Возвращает список датаврапперов для сайта
     *
     * @return array
     */
    public function dataWrappers() {
        return array(
            "subject" => "mixed",
            "message" => "mixed",
            "from" => "mixed",
            "to" => "mixed",
            "type" => "mixed",
            "headers" => "mixed",
            "attachments" => "mixed",
        );
    }

    /**
     * Добавляет заголовок в письмо
     *
     * @author Petr.Grishin
     * @param string $header Строка являющаяся заголовком для письма которое добавляеться в конец блока headers письма
     * @return $this
     */
    public function addHeader($header) {
        $headers = $this->param("headers");
        $headers[] = $header;
        $this->param("headers",$headers);
        return $this;
    }

    /**
     * Прикрепляет файл к письму от корня веб проекта
     *
     * @param string $file Файл для прикрепления от корня веб проекта
     * @param $name Имя файла
     * @param $cid Уникальный код, для использования в теле письма
     * @return $this
     * @author Petr.Grishin
     */
    public function attach($file, $name = null, $cid = null) {

        //Замалчивание ошибок
        if ($file === null || $file == "") {
            return $this;
        }
        
        return $this->attachNative(mod_file::get($file)->native(), $name, $cid);
    }


    /**
     * Прикрепляет файл к письму с абсолютным путем к файлу
     *
     * @param string $file Файл для прикрепления
     * @param $name Имя файла
     * @param $cid Уникальный код, для использования в теле письма
     * @return $this
     * @author Petr.Grishin
     */
    public function attachNative($file, $name = null, $cid = null) {

        //Замалчивание ошибок
        if ($file === null || $file == "")
            return $this;
        
        if ($name === null || $name == "") {
            $name = mod_file::get($file)->name();
        }
        
        $attachments = $this->param("attachments");
        $attachments[] = array(
            "name" => $name,
            "file" => $file,
            "cid"  => $cid,
        );
        $this->param("attachments",$attachments);
        
        return $this;
    }

    /**
     * Непосредственно отправляет сообщение
     *
     * @return boolean
     * @author Petr.Grishin
     */
    public function send() {
             
        $message = $this->message();
            
        // Генерируем уникальный разделитель
        $boundary  = md5(uniqid(time()));
        
        // Заголовки
        $headers = $this->headers();
        $headers[] = "MIME-Version: 1.0;";
        $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";        
        if ($this->from() != "") {
            $headers[] = "From:" . $this->utf8email($this->from());
        }
        
        // Тело письма
        $multipart = array();
        
        $multipart[] = "--" . $boundary;
        $multipart[] = "Content-Type: " . $this->type() . "; charset=utf-8";
        $multipart[] = "";
        $multipart[] = $message;
        $multipart[] = "";
        
        // Прикрепляем к письму файлы вложений
        foreach($this->attachments() as $attach) {
            
            $filename = $attach["name"];
            $filecontent = $attach["file"];
            $cid = $attach["cid"];
            
            $multipart[] ="--".$boundary;
            
            $multipart[] = "Content-Type: application/octet-stream; name=\"" . $filename . "\""; //image/jpeg
            $multipart[] = "Content-Transfer-Encoding: base64";
            
            if ($cid != null && $cid != "")
                $multipart[] = "Content-ID: <" . $cid . ">";
            
            $multipart[] = "Content-Disposition: attachment; filename=\"" . $filename . "\"";
            $multipart[] = "";
            $multipart[] = "";
            $multipart[] = chunk_split(base64_encode(file_get_contents($filecontent)), 76, "\n");
        }
                
        $multipart[] = "--$boundary--";
        $multipart[] = "";        

        $subject = '=?UTF-8?B?' . base64_encode($this->subject()) . '?=';
            
        return mail (
            $this->to(),
            $subject,
            implode("\n", $multipart),
            implode("\n", $headers)
        );
    }

    /**
     * Правильное форматирование utf-8 email адресов
     *
     * @param string $email Строка c эмейлом для форматирования в вид <имя> е-майл
     * @return string
     * @author Petr.Grishin
     */
    private function utf8email($email) {
        if (preg_match("/.*? <.*?>/ui", $email)) {
            $name = preg_replace("/(.*?) (<.*?>)/ui", "\$1", $email);
            $name = '"=?UTF-8?B?' . base64_encode($name) . '?=" ';
            $email = preg_replace("/(.*?) (<.*?>)/ui", "\$2", $email);
            return $name . $email;
        } else {
            return $email;
        }
    }

}
