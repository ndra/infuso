<?

class mod_field_files extends mod_field {

	public function typeID() {
		return "f927-wl0n-410x-4grx-pg0o";
	}
	
	public function typeName() {
		return "Список файлов";
	}

	public function mysqlType() {
		return "blob";
	}

	public function mysqlIndexFields() {
		return $this->name()."(1)";
	}

	public function editorInx() {
		return array(
		    "type" => "inx.mod.file.files",
		    "value" => $this->value(),
		);
	}

	public function tableCol() { return array(
	    "type"=>"image",
	);}

	public function tableRender() {
	    return $this->pvalue()->first()->preview(16,16)."";
	}

	public function pvalue() {
	    $files = @json_decode($this->value(),1);
	    if(!is_array($files)) $files = array();
	    $ret = array();
	    foreach($files as $file)
	        $ret[] = file::get($file["f"]);
	    $ret = new \infuso\core\flist($ret);
	    return $ret;
	}

	public function prepareValue($files) {

	    if(!$files)
			$files = array();

	    // Если передана строка, пытаемся раскодировать ее как json
	    // Если не получилось, считаем что строка - имя файла
	    // Преобразуем строку в массив и переходим к обработке массива
	    if(is_string($files)) {
	        $e = @json_decode($files,1);
	        if(is_array($e)) $files = $e;
	        else $files = array($files);
	    }

	    // Если передан массив, преобразуем его в json
	    if(is_array($files)) {
	        $ret = array();
	        foreach($files as $file) {
	            if(!is_array($file)) $file = array("f"=>$file."");
	            $ret[] = $file;
	        }
	        $files = json_encode($ret);
	    }

		// Отфильтровываем пустые массивы
	    if($files=="[]")
			$files = "";

	    return $files;
	}

	public function filterType() { return "checkbox"; }

}
