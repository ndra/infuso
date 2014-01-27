<?

/**
 * Фасад поля формы
 * При добавлении в форму поля, виджета, шаблона и т.п. возаращается фасад добавленного элемента
 * Фасад перенаправляет методы для работы с полем (например name(),value())
 * объекту поля, а методы, относящиеся к внешнему виду блока - к объекту шаблона
 * Пользователь же работает с фасадом и вызывает все методы у одного единственного объекта
 **/

class form_facade extends \mod\template\generic {

	private $field = null;
	private $template = null;

	public function exec() {
	    tmp::exec("/form/layout",array(
	        "facade" => $this,
	        "template" => $this->template(),
	        "label" => $this->label(),
	        "name" => $this->name(),
		));
	}
	
	/**
	 * Возвращает поле, связанное с этим фасадом
	 **/
	public function field() {
	
	    if(!$this->field)
	        $this->setField(mod::field("textfield"));

        return $this->field;
	    
	}
	
	/**
	 * Устанавливает поле, связанное с этим фасадом
	 **/
	public function setField($field) {
	    $this->field = $field;
	}
	
	/**
	 * Устанавливает шаблон, связанный с этим фасадом
	 **/
	public function setTemplate($template) {
	    $this->template = $template;
	}

	/**
	 * Возвращает шаблон, свуязанный с этим фасадом
	 **/
	public function template() {
		return $this->template;
	}
	
	public function layout($layout=null) {
	
	    if(func_num_args()==0) {
	        return $this->param("layout");
		}
		
        if(func_num_args()==1) {
            return $this->param("layout",$layout);
        }
	}
	
	public function componentCall($method,$args) {
	    
	    $fieldMethods = array(
	        "label",
	        "name",
	        "value",
	        "min",
	        "max",
	        "match",
	        "regex",
	        "fn",
	        "error",
	        "width",
	        "options",
		);

		// Вызов метода поля
		if(in_array($method,$fieldMethods)) {
		
		    $ret = call_user_func_array(array($this->field(),$method),$args);
		    
			// Если метод поля вернул это поля, мы возвращаем обхект фасада
			if($ret==$this->field()) {
			    return $this;
			}
			
			return $ret;
		}
		
		parent::componentCall($method,$args);
	    
	}

}
