<?

/**
 * Модель подписки
 **/
class user_subscription extends reflex {

	public function reflex_table() {
	
		return array (
			'name' => 'user_subscription',
			'fields' => array (
				array (
					'name' => 'id',
					'type' => 'jft7-kef8-ccd6-kg85-iueh',
					'editable' => '1',
				), array (
					'name' => 'userID',
					'type' => 'pg03-cv07-y16t-kli7-fe6x',
					'editable' => 1,
					'id' => 'uixp5bqfu6qylst8g23m1b7casd49b',
					'label' => 'Пользователь',
					'indexEnabled' => 1,
					'class' => 'user',
				), array (
					'name' => 'key',
					'type' => 'v324-89xr-24nk-0z30-r243',
					'editable' => 1,
					'label' => 'Ключ',
					'indexEnabled' => 1,
				), array (
					'name' => 'title',
					'type' => 'v324-89xr-24nk-0z30-r243',
					'editable' => 1,
					'label' => 'Название подписки',
					'indexEnabled' => 1,
				), array (
					'name' => 'group',
					'type' => 'v324-89xr-24nk-0z30-r243',
					'editable' => 0,
					'indexEnabled' => 0,
				),
			),
		);
	}

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
    public function mail($params) {
    
        $mailer = $this->user()->mailer()->params($params);
    
        if($this->onBeforeMail($mailer->params())) {
        	$mailer->send();
        } else {
            mod::msg("mail stopped");
        }
    }
    
    public function onBeforeMail($params) {
        $event = mod::fire("user_subscription_beforeMail",$params);
        return !$event->stopped();
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

        if(func_num_args()==2 && is_array($second)) {
            $params = $second;
        } elseif(func_get_args()==3 && is_string($second) && is_string($third)) {
            $params = array(
                "message" => $second,
                "subject" => $third,
            );
        }
        
        $params["subscriptionKey"] = $key;

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

	public function reflex_title() {
		return $this->user()->title()." / ".$this->data("key");
	}

    /**
     * Разделы для каталога
     * В режиме суперадмина показываем все подписки для тестирования
     **/
    public static function reflex_root() {
        $ret = array();
        if(mod_superadmin::check() && mod::debug()) {
            $ret[] = self::all()->param("tab","user")->title("Все подписки");
		}
        return $ret;
    }

}
