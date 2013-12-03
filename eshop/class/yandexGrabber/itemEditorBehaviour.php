<?

/**
 * Поведение группы с атрибутами
 **/
class eshop_yandexGrabber_itemEditorBehaviour extends mod_behaviour {

	public function addToClass() {
	//	return mod_conf::get("eshop:yandex:grab") ? "eshop_item_editor" : null;
	}

	public function behaviourPriority() {
		return -1;
	}

	public function actions() {
	    return array(
	        array(
				"text"=>"Подобрать фотографию",
				"icon"=>"images",
				"dlg"=>array(
					"type"=>"inx.mod.eshop.photosearch"
				),
			),
	    );
	}

	/**
	 * Скачивает фотографию с заданного адреса
	 **/
	public function downloadPhoto($url) {

	    if(!$url) return;
	    $ext = strtolower(file::get(mod_url::get($url)->path())->ext());
	    if(!in_array($ext,array("jpg","gif","png"))) $ext = "jpg";
	    $unique = md5($url);
	    $name = "auto-$unique.$ext";

	    // Скачиваем картинку
	    file::mkdir("/eshop/_tmp");
	    $dir = file::get("/eshop/_tmp");
	    $data = file_get_contents($url);
	    file::get("/eshop/_tmp/$name")->put($data);

	    $size = file::get("/eshop/_tmp/$name")->size();
	    if($size>1500000) {
	        mod::msg("File is too large",1);
	        return;
	    }

	    // Добавляем файл в хранилище
	    $img = $this->item()->storage()->add("/eshop/_tmp/$name",$name);

	    $photos = array();
	    foreach($this->item()->photos() as $photo)
	        $photos[] = $photo->path();
	    $photos[] = $img;
	    $this->item()->data("photos",$photos);

	    // Убираем временные файлы
	    file::get("/eshop/_tmp")->delete(true);

	    return $img;
	}

	public function grabSearchQueries() {
	    return array($this->item()->title());
	}

}
