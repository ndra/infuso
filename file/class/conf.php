<?

class file_conf extends mod_conf {

	/**
	 * Возвращает все параметры конфигурации
	 **/
	public function conf() {
	
        return array(
            array(
                "id"=>"file:preview-background",
                "title"=>"Цвет фона превьюшек",
            ),
            array(
                "id"=>"file:watermark",
                "title"=>"Файл водяного знака",
            ),
            array(
                "id"=>"file:watermark-position",
                "title"=>"Положение водяного знака",
                "type"=>"select",
                "values" => array(
                    "top-left" => "Лево-верх",
                    "top-right" => "Право-верх",
                    "bottom-left" => "Лево-низ",
                    "bottom-right" => "Право-низ",
                    "center" => "По центру",
                ),
            ),
            array(
                "id"=>"file:watermark-margin",
                "title"=>"Отступ водяного знака от края изображения",
            ),
        );
	
	}

}
