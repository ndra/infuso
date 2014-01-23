<?

/**
 * Модель записи в журнале
 **/ 
class reflex_log extends reflex {

	public function reflex_table() {
		return array (
			'name' => 'reflex_log',
			'fields' =>	array (
				array (
					'name' => 'id',
					'type' => 'jft7-kef8-ccd6-kg85-iueh',
					"editable" => 2,
				), array (
					'name' => 'datetime',
					'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
					'label' => 'Дата',
					"editable" => 2,
		    	), array (
					'name' => 'user',
					'type' => 'pg03-cv07-y16t-kli7-fe6x',
					'label' => 'Пользователь',
					'class' => 'user',
					"editable" => 2,
		    	), array (
					'name' => 'index',
					'type' => 'v324-89xr-24nk-0z30-r243',
					'label' => 'Индекс',
					"editable" => 2,
		    	), array (
					'name' => 'text',
					'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
					'label' => 'Текст',
					"editable" => 2,
		    	), array (
					'name' => 'comment',
					'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
					'label' => 'Это комментарий',
					"editable" => 2,
		    	), array (
					'name' => 'type',
					'type' => 'textfield',
					'label' => 'Тип',
					'length' => 30,
					'editable' => 2,
		    	), array (
					'name' => 'p1',
					'type' => 'textfield',
					'editable' => 2,
		    	),
			),
			'indexes' => array (
				array (
				  'name' => 'main',
				  'fields' => 'datetime,user,index,comment',
				  'type' => 'index',
				),
			),
		);
	}

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    public static function all() {
        return reflex::get(get_class())->desc("datetime");
    }

    public function reflex_beforeCreate() {
        $this->data("datetime",util::now());
    }

    /**
     * Возвращает пользователя, сделавшего запись
     **/
    public function user() {
        return $this->pdata("user");
    }

    /**
     * Возвращает текст сообщения
     **/
    public function message() {
        return $this->data("text");
    }

    /**
     * Возвращает текст сообщения
     **/
    public function msg() {
		return $this->message();
	}

    /**
     * Иконка для лога
     **/
    public function reflex_icon() {
        return "log";
    }

	/**
	 * Вернет элемент к которому прикреплена запись
	 **/
    public function item() {
        list($class,$id) = explode(":",$this->data("index"));
        return reflex::get($class,$id);
    }

}
