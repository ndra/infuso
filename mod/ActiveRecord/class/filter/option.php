<?

/**
 * Класс описывающий опцию отобрадения (тип, количество элементов на страницу, сортировку)
 **/ 
class reflex_filter_option extends mod_component {

	private $list = null;

	public function __construct($p=null,$list=null) {

		if($p)
		    foreach($p as $key=>$val)
		        $this->param($key,$val);
		$this->list = $list;
	}
	
	/**
	 * @return Возвращает значение данной опции.
	 * Под значением имеется ввиду слово, которое передается через url, например "price_asc" 
	 * Значение передается в массиве-конструкторе ключом "val" или "value" 
	 **/ 
	public function val() {
	    $val = $this->param("val");
	    if(!$val) {
	        $val = $this->param("value");
        }
	    return $val;
	}
	
	/**
	 * alias для метода name()
	 **/  
	public function key() {
	    return $this->param("key");
	}
	
	/**
	 * @return Возвращает название данного режима
	 **/ 
	public function title() {
	    return $this->param("title");
	}
	
	/**
	 * Устанавливает этот режим в качестве активного
	 * Не вызывается напрямую 
	 **/ 
	public function setActive() {
	    $this->param("active",true);
	    $key = crc32($this->items()->itemClass().":".$this->key());
	    mod::cookie($key,$this->val());
	}
	
	/**
	 * @return true если в прошлый раз этот режим был активным (берет из куков)
	 **/
	public function wasActive() {

        if(!$this->items()->filterRemember()) {
            return false;
        }

	    $key = crc32($this->items()->itemClass().":".$this->key());
	    return mod::cookie($key)==$this->val();
	}
	
	/**
	 * @return bool Активен ли режим
	 **/ 
	public function active() {
	    return $this->param("active");
	}
	
	/**
	 * @return Возвращает коллекцию элементов 
	 **/ 
	public function items() {
	    return $this->param("collection");
	}
	
	/**
	 * @return Возвращает метод коллекции (для сортировки), который нужно вызвать чтобы
	 * получить требуемую сортировку. Метод передается в конструкторе  
	 * Не вызывается напрямую 
	 **/ 
	public function method() {
	    return $this->param("method");
	}
	
	/**
	 * Возвращает url базовой коллекции, но с выбранным данным пунктом
	 **/ 
	public function url() {   
	
	    $params = array(
	        $this->key() => $this->val(),
	    ); 
	
	    if(!$this->param("keepPage"))
	        $params["page"] = 1;
	
	    return $this->items()->url($params);
	}
	
	public function getList() {
		return $this->list;
	}

}
