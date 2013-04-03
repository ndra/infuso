<?

class reflex_editor_trash_editor extends reflex_editor {

	// Восстанавливает объект
	public function action_restore() {
		if(!$this->item()->exists()) return;
		$data = @unserialize($this->item()->data("data"));
		$meta = @unserialize($this->item()->data("meta"));
		$item = reflex::create($this->item()->data("class"),$data);
		if($item->exists()) {
		    $this->item()->delete();
		    foreach($meta as $key=>$val)
		        if($key!="id" && $key!="hash")
		        $item->meta($key,$val);
		    mod::msg("Объект восстановлен");
		}
	}

	public function actions() {
		return array(
		    array("text"=>"Восстановить","action"=>"restore"),
		);
	}
}
