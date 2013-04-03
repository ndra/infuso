<?

class form_fieldBehaviour extends mod_behaviour {

	/**
	 * @return Возвращает экранированное имя поля
	 **/
	public function ename() {
		return htmlspecialchars($this->name(),ENT_QUOTES);
	}

	/**
	 * @return Возвращает экранированное значение поля
	 **/
	public function evalue() {
		return htmlspecialchars($this->value(),ENT_QUOTES);
	}

	/**
	 * Возвращает / изменет ширину поля
	 **/
	public function width($width=null) {
	    if(func_num_args()==0)
			return $this->conf("width");
	    $this->conf("width",$width);
	    return $this;
	}

	/**
	 * Устанавливает правило валидации:
	 * Нужно ли заполнять поле?
	 **/
	public function fill($val=null) {
	    if(func_num_args()==0)
			return !!$this->conf("fill");
	    $this->conf("fill",!!$val);
	    return $this;
	}

	/**
	 * Устанавливает минимально допустимую длину строки или минимальное значение для числовых полей
	 **/
	public function min($val=null) {
	    if(func_num_args()==0)
			return $this->conf("min");
	    $this->conf("min",$val);
	    return $this;
	}

	/**
	 * Устанавливает максимальную допустимую длину строки или максимальное значение для числовых полей
	 **/
	public function max($val=null) {
	    if(func_num_args()==0)
			return $this->conf("max");
	    $this->conf("max",$val);
	    return $this;
	}

	/**
	 * Устанавливает регулярное выражение для проверки строки
	 **/
	public function regex($val=null) {
	    if(func_num_args()==0)
			return $this->conf("regex");
	    $this->conf("regex",$val);
	    return $this;
	}

	/**
	 * Устанавливает правило валидации:
	 * Значение данного поле должно совпадать со значением другого поля
	 **/
	public function match($val=null) {
	    if(func_num_args()==0)
			return $this->conf("match");
	    $this->conf("match",$val);
	    return $this;
	}

	/**
	 * Устанавливает метод валидации
	 **/
	public function fn($fn=null) {
	    if(func_num_args()==0) return $this->conf("fn");
	    $this->conf("fn",$fn);
	    return $this;
	}


	/**
	 * Возвращает / устанавливаент текст ошибки валидации
	 **/
	public function error($error=null) {
	    if(func_num_args()==0)
			return $this->conf("error");
		$this->conf("error",$error);
	    return $this;
	}

	/**
	 * Добавляет условие того, что валидацию нужно делать только в случае если $data[$name] = $value
	 **/
	public function validateIf($name=null,$values=1) {

		if(func_num_args()==0) {
			return $this->conf("validateIf");
		}

		if(func_num_args()==2 || func_num_args()==1) {
		    if(!is_array($values))
		        $values = array($values);
			$this->conf("validateIf",array($name,$values));
			return $this;
		}
	}

	/**
	 * Проверяет валидность данного поля
	 **/
	public function validate($val,$data) {

		if($v = $this->validateIf()) {
		    if(!in_array($data[$v[0]],$v[1]))
		        return true;
		}

	    if($this->fill() && !$val)
	        return false;

	    if($this->min() && strlen($val)<$this->min())
	        return false;
	    if($this->max() && strlen($val)>$this->max())
	        return false;

	    if($this->regex() && !preg_match($this->regex(),$val))
	        return false;

	    if($fn = $this->fn()) {
	        $fn = strtr($fn,array("::"=>":"));
	        list($class,$method) = explode(":",$fn);
	        $ret = call_user_func(array($class,$method),$val,$data,$this);
	        if($ret!==true) {
	            if($ret)
	                $this->error($ret);
	            return false;
	        }
	    }

	    if($match = $this->match()) {
	        $secondValue = $data[$match];
	        if($secondValue!=$val)
	            return $false;
	    }

	    return true;
	}

}
