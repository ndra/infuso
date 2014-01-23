<?

/**
 * Класс-билдер формы
 **/
class form extends tmp_helper {

	private $blocks = array();
	private $fields = array();
	
	public static $widgetStack = array();
    
	public function addGeneric($generic) {
		$this->blocks[] = $generic;
		return $generic;
	}
	
	public function addFacade($facade) {
	    $this->blocks[] = $facade;
	    $this->addField($facade->field());
	}
    
	/**
	 * Добавляет шаблон в представление формы
	 **/
	public function template($name,$params=array()) {
	
		$template = tmp::get($name);
		
		// Добавляем в шаблон параметры формы
		foreach($this->params() as $key=>$val)
		    $template->param($key,$val);
		    
		foreach($params as $key=>$val)
		    $template->param($key,$val);
		    
		$this->addGeneric($template);
	}
	
	public function labelAlign($a) {
	    $this->param("labelAlign",$a);
	}

	/**
	 * Вставляет начало виджета
	 **/
	public function widgetStart($name,$params=array()) {
		$widget = tmp_widget::get($name);
		foreach($params as $key=>$val)
		    $widget->param($key,$val);
		$this->template("form:widget.start",array(
			"widget" => $widget,
		));
		self::$widgetStack[] = $widget;
	}

	/**
	 * Вставляет конец виджета
	 **/
	public function widgetEnd() {
		$widget = array_pop(self::$widgetStack);
		$this->template("form:widget.end",array(
			"widget" => $widget,
		));
	}

	/**
	 * Выполняет форму
	 **/
	public function execWidget() {

	    $id = "form-{$this->hash()}";
	    tmp::exec("form:form",array(
	        "xx" => 1213,
			"form" => $this,
			"id" => $id
		));
	    $this->bind("#".$id);

	}

	/**
	 * Возвращает список всех блоков
	 **/
	public function blocks() {
		return $this->blocks;
	}

	/**
	 * Добавляем поле в модель формы
	 **/
	public function addField($type) {
	
	    if(is_object($type)) {
	        $field = $type;
	    } else {
			$field = mod::field($type);
		}
		$field->addBehaviour("form_fieldBehaviour");
		$this->fields[] = $field;
		return $field;
	}

	/**
	 * Возвращает масисво полей формы
	 **/
	public function fields() {
		return $this->fields;
	}

	/**
	 * @return Строковой хэш формы, зависящий от данных валидации
	 **/
	public function hash() {
	    return md5($this->serializeValidation().":".$this->code());
	}

	/**
	 * Распаковывает сериализованную форму
	 **/
	public static function unserialize($str) {
	    $array = @unserialize($str);
	    $form = new form();
	    if($array) {
		    foreach($array as $item){
		        $field = $form->addField($item["type"]);
		        foreach($item as $a=>$b)
		            $field->conf($a,$b);
		    }
		}
		return $form;
	}

	/**
	 * @return Сериализованные данные о валидации формы
	 **/
	public function serializeValidation() {
	    $array = array();
	    foreach($this->fields() as $field) {
	        $conf = $field->conf();
			unset($conf["id"]);
			unset($conf["value"]);
			$array[] = $conf;
	    }
	    return serialize($array);
	}

	/**
	 * Сохраняет модель формы в базу
	 **/
	public function store() {
	    // Сохраняем форму в базу
	    $s = $this->serializeValidation();
	    $validate = form_validate::get($this->hash());
	    if(!$validate->exists()) {
	        $validate = reflex::create("form_validate",array(
	            "hash" => $this->hash(),
	            "formData" => $s,
	            "code" => $this->code(),
	        ));
		}
	}

	/**
	 * Возвращает / задает кодовое слово для формы
	 **/
	public function code($code=null) {

		if(func_num_args()==0) {
		    return $this->param("code");
		}

		if(func_num_args()==1) {
		    return $this->param("code",$code);
		}

	}

	/**
	 * Возвращает форму по кодовому слову
	 * При генерации формы можно указать кодовое слово для этой формы, по которому
	 * потом эту форму можно будет извлечь из базы
	 **/
	public static function byCode($code) {
	    return form_validate::all()->eq("code",$code)->neq("code","")->one()->form();
	}

	/**
	 * Связывает dom-объект с моделью формы
	 **/
	public function bind($selector) {
		$this->store();
	    tmp::jq();
	    tmp::reset();
	    mod::coreJS();
	    tmp::js("/form/res/form.js");
	    tmp::script("$(function() { form('$selector','{$this->hash()}'); })");
	}

	private $errorTxt = "";
	private $errorName = "";
	public function validate($data) {

		// Если у формы нет полей, она не пройдет валидацию
		if(!sizeof($this->fields()))
		    return false;

	    foreach($this->fields() as $field) {
	        if(!$field->validate($data[$field->name()],$data)) {

	            $txt = $field->error();
	            if(!$txt)
					$txt = "Форма заполнена неверно {$field->name()}";
	            $this->errorTxt = $txt;
	            $this->errorName = $field->name();
	            return false;
	        }
		}
	        
	    return true;
	}

	/**
	 * @return Текст ошибки валидации
	 **/
	public function error() {
	    return $this->errorTxt;
	}

	/**
	 * Имя поля, содержащее ошибку
	 **/
	public function errorName() {
	    return $this->errorName;
	}

	public function setData($data) {
		foreach($this->fields() as $field) {
			$field->value($data[$field->name()]);
		}
		return $this;
	}

	public function setFilledData($data) {
		foreach($this->fields() as $field) {
		    if($val = trim($data[$field->name()])) {
				$field->value($val);
			}
		}
		return $this;
	}

	/**
	 * Возвращает форму ввиде текста (например, для отправки по электронной почте)
	 **/
	public function text() {
		$ret = "";
		foreach($this->fields() as $field) {
		    $ret.= $field->name().": ".$field->rvalue()."\n";
		}
		return $ret;
	}

}
