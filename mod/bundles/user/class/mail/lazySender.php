<?

class user_mail_lazySender extends mod_component implements mod_handler {

    /**
     * Берет из очереди и отправляет одну склейку писем
     **/
    public function sendOne() {
        
        // Выбираем из очереди одно неотправленное письмо
        $mail = user_mail::all()
            ->eq("done",0)
            ->neq("glue","")
            ->leq("glueSendAfter",util::now())
            ->one();
        
        if(!$mail->exists()) {
            return false;
        }
        
        // Находим все письма, которые надо склеить с данным
        // Помечаем их как отправленные и складываем текст в массив
        $message = array();
        foreach($mail->glueMails() as $item) {

            $item->done(true);
            $item->store();

            if($item->mailer()->evalBusinessRules()) {
                $message[] = $item->message();
            }
        }
        
        // Создаем и отправляем склейку
        if(sizeof($message)) {
            $message = implode("<br><br/>",$message);
            $mailer = $mail->mailer();
            $mailer->message($message);
            $mailer->glue("");
            $mailer->code($mail->mailer()->codeAfterGlue());
            $mailer->send();
        }
        
    }
    
    public function on_mod_cron() {
    
        for($i=0;$i<10;$i++) {
            self::sendOne();
        }
        
    }

}
