<?

/**
 * Класс, склеивающий кусочки файлов css и js
 **/
class tmp_render {

	private static $less;
	private static $renderID = null;
	
	private static function lesscssInstance() {
		if(!self::$less) {
			self::$less = new tmp_lesscss();
		}
		return self::$less;
	}
	
	/**
	 * Очищает кэш рендера скриптов и стилей
	 **/
	public static function clearRender() {
	    file::get("/tmp/render/")->delete(true);
	    file::mkdir("/tmp/render");
	    file::get("/tmp/render/renderID.txt")->put(util::id());
	}

	/**
	 * Возвращает ключ рендера для предотвращения кэширования
	 * Он меняется каждый раз при изменении шаблонов из админки
	 **/
	private function renderID() {
		if(!self::$renderID) {
		    self::$renderID = file::get("/tmp/render/renderID.txt")->data();
		    if(!self::$renderID)
		    	self::$renderID = "*";
		}
		return self::$renderID;
	}
	
	/**
	 * @return bool Включен ли lesscss
	 **/
	public function less() {
		return mod::conf("tmp:lesscss");
	}
	
    /**
     * Упаковывает массив css или js файлов в один, сохраняет на диск
     * и возвращает имя сгенерированного файла
     **/
	public static function packIncludes($items,$ext) {

	    if(is_scalar($items)) {
			return $items;
        }

	    $hash = md5(self::renderID()." - ".serialize($items));
	    $file = file::get("/tmp/render/$hash.$ext");

	    if(mod::conf("tmp:always-render") || !$file->exists()) {

	        $code = "";
	        foreach($items as $item) {
	            if($str = trim(file::get($item)->data())) {
	                // В режиме отладки дописываем источник
	                if(mod_conf::get("mod:debug"))
	                	$code.= "/* source:".$item.": */\n\n";

	                $code.= $str.($ext=="js" ? "\n;\n" : "\n\n");
				}
			}

			// Если включен lesscss и расширение css - пропускаем через пармер less
			if(self::less()) {
				if($ext=="css") {
				    $code = self::lesscssInstance()->parse($code);
				}
			}

			// Сохраняем результат
		    file::mkdir($file->up(),true);
	        $file->put($code);
	    }

	    return $file."";
	}

}

