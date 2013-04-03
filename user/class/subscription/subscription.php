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
     * рассылка писем по ключу рассылки
     **/
    public static function mailByKey($key,$message,$subject){
        reflex_task::add("user_subscription","`key`='".reflex_mysql::escape($key)."'","mail",array($message,$subject));
        
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
