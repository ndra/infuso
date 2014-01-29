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
	
	public function renderPath() {
	    return mod::app()->publicPath()."/render/";
	}
	
	/**
	 * Очищает кэш рендера скриптов и стилей
	 **/
	public static function clearRender() {
	    $path = self::renderPath();
	    file::get($path)->delete(true);
	    file::mkdir($path);
	    file::get("{$path}/renderID.txt")->put(util::id());
	}

	/**
	 * Возвращает ключ рендера для предотвращения кэширования
	 * Он меняется каждый раз при изменении шаблонов из админки
	 **/
	private function renderID() {
	
	    $path = self::renderPath();
	
		if(!self::$renderID) {
		    self::$renderID = file::get("{$path}/renderID.txt")->data();
		    if(!self::$renderID) {
		    	self::$renderID = "*";
			}
		}
		return self::$renderID;
	}
	
	/**
	 * @return bool Включен ли lesscss
	 **/
	public function less() {
		return true;
	}
	
    /**
     * Упаковывает массив css или js файлов в один, сохраняет на диск
     * и возвращает имя сгенерированного файла
     * @todo Сделать отключение кэширваония рендера
     **/
	public static function packIncludes($items,$ext) {
	
	    $rpath = self::renderPath();

	    if(is_scalar($items)) {
			return $items;
        }

	    $hash = md5(self::renderID()." - ".serialize($items));
	    $file = file::get("{$rpath}/$hash.$ext");

        //if(mod::conf("tmp:always-render") || !$file->exists()) {
	    if(!$file->exists()) {

	        $code = "";
	        foreach($items as $item) {
	            if($str = trim(file::get($item)->data())) {
	                // В режиме отладки дописываем источник
	                if(mod::debug()) {
	                	$code.= "/* source:".$item.": */\n\n";
	                }

	                $code.= $str.($ext=="js" ? "\n;\n" : "\n\n");
				}
			}

			// Если включен lesscss и расширение css - пропускаем через пармер less
			if(self::less()) {
				if($ext=="css") {
				    $code = self::lesscssInstance()->parse($code);
				}
			}
			
			if(!trim($code)) {
			    return null;
			}

			// Сохраняем результат
		    file::mkdir($file->up(),true);
	        $file->put($code);
	    }

	    return $file."";
	}

}

