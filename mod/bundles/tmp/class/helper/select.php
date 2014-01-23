<?

class tmp_helper_select extends tmp_widget {

	public function name() {
		return "Выпадающий список";
	}

	public function execWidget() {

		$attr = $this->param("attributes");
		if(!$attr)
		    $attr = array();
		foreach($attr as $key=>$val)
		    $attr[$key] = $key."='".$val."'";
		$attr = implode(" ",$attr);

		echo "<select $attr>";
		$options = $this->param("options");
		foreach($options as $key=>$val) {
		    $inject = ($key==$this->param("selected")) ? "selected" : "";
		    echo "<option value='$key' $inject>$val</option>";
		}
		echo "</select>";
	}

	public function selected($selected) {
		$this->param("selected",$selected);
	}

	public function option($key,$val) {
		if(!$this->param("options"))
		    $this->param("options",array());
		$options = &$this->param("options");
		$options[$key] = $val;
		return $this;
	}

	public function attr($key,$val) {
		if(!$this->param("attributes"))
		    $this->param("attributes",array());
		$attrs = &$this->param("attributes");
		$attrs[$key] = $val;
		return $this;
	}

}
