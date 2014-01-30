<? class eshop_admin_testContent extends mod_controller {

// -----------------------------------------------------------------------------

public static function indexTest() { return mod_superadmin::check(); }
public static function indexTitle() { return "Тестовая информация"; }
public static function indexFailed() { return admin::fuckoff(); }
public static function index() {
	admin::header("Тестовая информация");
	echo "<div style='padding:40px;' >";
    inx::add(array(
        "type"=>"inx.button",
        "text"=>"Заполнить",
        "onclick"=>"this.call({cmd:'eshop:admin:testContent:generate'})",
    ));
	echo "</div>";
	admin::footer();
}

// -----------------------------------------------------------------------------

private static $groups = array(
    "Электроника, фото" => array(
        "Телевизоры"=>true,
        "Фотоаппараты"=>array("Цифровые"=>true,"Зеркальные"=>true,"Пленочные"=>true),
        "DVD-плееры"=>true,
        "GPS-навигаторы"=>true,
        "MP3-плееры"=>array("С сенсоным экраном"=>true,"Без экрана"=>true),
        "Электронные книги"=>true,
    ),
    "Телефоны" => array("Сотовые телефоны"=>true,"Гарнитуры"=>true,"Радиотелефоны"=>true),
    "Авто" => array("Шины"=>true,"Диски"=>true,"Магнитолы"=>true),
    "Бытовая техника" => array("Холодильники"=>true,"Кухонные плиты"=>true,"Стиральные машины"=>true,"Пылесосы"=>true,"Обогреватели"=>true,"Увлажнители"=>true),
    "Спорт и отдых" => array("Горные лыжи"=>true,"Сноуборды"=>true,"Тренажеры"=>true),
    "Ремонт" => array("Сантехника"=>true,"Инструменты"=>true),
);

private static $vendors = array(
"A-Data","Apacer","Apexto","Axxen","BONE Collection",
"Canyon","Clickfree","Corsair","Cowon",
"DIGITEX","Dicom","Digma","Drivix");

public static function postTest() { return mod_superadmin::check(); }
public static function post_generate() {

	reflex::get("eshop_group")->delete();
	reflex::get("eshop_attr_descr")->delete();
	reflex::get("eshop_item")->delete();
	reflex::get("eshop_attr")->delete();
	reflex::get("eshop_vendor")->delete();
    
    foreach(self::$vendors as $vendor)
        reflex::create("eshop_vendor",array(
            "title" => $vendor,
        ));
        
    self::generateGroup(self::$groups);
}

public function generateGroup($src,$parent=null) {


    // Подразделы
    foreach($src as $key=>$val) {
        $group = reflex::create("eshop_group",array(
            "title" => $key,
            "description" => util_delirium::generate($key),
            "parent" => $parent,
            "active" => true,
        ));
        $group->store();
        
        if(is_array($val)) {
            self::generateGroup($val,$group->id());
        } else {
            // Добавляем товаров в раздел
            $n = 15 + rand()%10;
            for($i=0;$i<$n;$i++) {
                $item = reflex::create("eshop_item",array(
                    "title" => util_delirium::word()." ".$f2." ".util_delirium::word(),
                    "parent" => $group->id(),
                    "price" => rand()%10000,
                    "active" => true,
                    "instock" => rand()%100,
                    "vendor" => eshop::vendors()->rand()->id(),
                    "article" => util::id(10),
                ));
                reflex::freeAll();
                
            }
        }
    }
}

public function randomizeAttributes($item) {

	$item->reflex_repair();
	foreach($item->attributes() as $attr)
	    switch($attr->type("type")) {
	        case "1": $attr->data("value",rand()%2);break;
	        case "2": $attr->data("value",rand()%1000);break;
	        case "3":
	            $values = array_keys($attr->descr()->options());
	            shuffle($values);
	            $value = end($values);
	            $attr->data("value",$value);
				break;
	    }
}

// -----------------------------------------------------------------------------

} ?>
