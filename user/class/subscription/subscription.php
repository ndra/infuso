<?

/**
 * Модель подписки
 **/
class user_subscription extends reflex {

     /**
     * Возвращает коллекцию всех подписок всех пользователей
     **/
    public static function all() {
        return reflex::get(get_class());
    }
    
    /**
     * @return Возвращает подписку по id
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    /**
     * @return Возвращает подписку по коду
     **/
    public static function getByKey($key) {
        return self::all()->eq("key",$key)->one();
    }
  
    /**
     * отправка письма пользователю
     **/
    public function mail($message,$subject){
        $this->user()->mail($message,$subject);
    } 

    public function reflex_beforeStore(){
       $keyparts = explode(":",$this->data("key"),2);
       $this->data("group",$keyparts[0]); 
    }   
    
    /**
     * Рассылка писем по ключу рассылки
     * Первый параметр - ключ рассылки (строка)
     * Если второй параметр - массив, он используется как массив параметров для писем
     * Если второй и третий параметры - строки, они используются как сообщение и тема соответственно
     **/
    public static function mailByKey($key,$second=null,$third=null) {
    
        if(func_get_args()==2 && is_array($second)) {
            $params = $second;
        } elseif(func_get_args()==3 && is_string($second) && is_string($third)) {
            $params = array(
                "message" => $second,
                "subject" => $third,
			);
        }
    
        reflex_task::add(array(
			"class" => "user_subscription",
			"query" => "`key`='".reflex_mysql::escape($key)."'",
			"method" => "mail",
			"params" => $params,
		));
    }
    
    /**
     * Возвращает пользователя, котрому принадлежит подписка
     **/
    public function user() {
        return $this->pdata("userID");
    }
    
    /**
     * Родительский объект подписки - пользователь
     **/
    public function reflex_parent() {
        return $this->user();
    }
    
    /**
     * Разделы для каталога
     * В режиме суперадмина показываем все подписки для тестирования
     **/
    public static function reflex_root() {
        $ret = array();
        if(mod_superadmin::check() && mod::debug())
            $ret[] = self::all()->param("tab","user")->title("Все подписки");
        return $ret;
    }
    
}
