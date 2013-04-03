<?

class eshop_yandexMarket extends mod_controller implements mod_handler {

    public static function indexTest() {
        return user::active()->checkAccess("eshop:yandexMarket:viewExport");
    }

    public static function postTest() {
        return user::active()->checkAccess("eshop:yandexMarket:doExport");
    }

    public static function indexTitle() {
        return "Выгрузка в Яндекс.маркет";
    }

    public static function index() {
        admin::header("Выгрузка в Яндекс.маркет");
        echo "<div style='padding:40px;' >";
        inx::add("inx.mod.eshop.admin.yandexmarket");

        echo "<br/>";
        $file = file::get("/eshop/yandex/yandexmarket.xml");
        $size = round($file->size()/1024);
        echo "<a target='_new' href='/eshop/yandex/yandexmarket.xml'>Просмотреть XML</a> ({$size}кб.)<br/>";
        echo "Изменен ".$file->time()->txt();

        echo "</div>";
        admin::footer();
    }

    public static function post_export() {
        return self::export();
    }

    /**
     * Возвращает текущую страницу выгрузки
     **/
    private function page() {
        return file::get("/eshop/yandex/page.txt")->data();
    }

    /**
     * Устанавливает страницу выгрузки
     **/
    private function setPage($page) {
        return file::get("/eshop/yandex/page.txt")->put($page);
    }

    private static $fh;

    /**
     * Возвращает файловый дескриптор для файла выгрузки
     **/
    private static function fh() {
        if(!self::$fh) {
            $path = "/eshop/yandex/yandexmarket-new.xml";
            file::mkdir(file::get($path)->up()->path());
            self::$fh = fopen(file::get($path)->native(), 'a');
        }
        return self::$fh;
    }

    /**
     * Возвращает коллекцию товаров, которые нужно выгружать в Яндекс-маркет
     **/
    private static function getItems() {
        return eshop::items()->eq("yandexMarket",1)->neq("parent",0)->gt("price",0)->asc("id")->limit(100);
    }

    /**
     * Первый шаг экспорта
     **/
    public static function exportStart() {

        file::get("/eshop/yandex/yandexmarket-new.xml")->delete();
        file::get("/eshop/yandex/yandexmarket-new.xml")->put('<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'."\n");

        $domain = mod_url::current()->scheme()."://".mod_url::current()->host();

        self::open("yml_catalog",array("date"=>util::now()));
        self::open("shop");

        // Информация о магазине
        self::complete("name",null,"Магазин");
        self::complete("company",null,"Магазин");
        self::complete("url",null,$domain);

        // Валюты
        self::open("currencies");
        self::complete("currency",array("id"=>"RUR","rate"=>1));
        self::close("currencies");

        // Товары
        $items = self::getItems();

        // Группы товаров
        self::open("categories");
        foreach(eshop_group::all()->eq("id",$items->distinct("parent"))->limit(0) as $group)
            self::complete("category",array("id"=>$group->id()),$group->title());
        self::close("categories");

        self::open("offers");

    }

    /**
     * Обработчик крона
     * Вызываешт шаг выгрузки
     **/
    public function on_mod_cron() {
        if(!mod_conf::get("eshop:yandex:market"))
            return;
        self::export();
    }

    /**
     * Выгружает одну страницу товаров
     **/
    public static function export() {

        if(self::page()==0)
            self::exportStart();
        self::setPage(self::page()+1);

        // Товары
        $items = self::getItems()->page(self::page());
        foreach($items as $item) {

            if($item->price()<1)
                continue;

            $data = $item->yandexMarketData();
            if(!$data) {
                continue;
            }

            // Данные с этим ключем попадают в атрибуты
            // Остальные - в элементы
            $attrKeys = array(
                "id",
                "type",
                "available",
                "bid",
                "cbid"
            );

            $attr = array(
                "id" => $item->id(),
            );

            foreach($attrKeys as $key)
                if($val = trim($data[$key]))
                    $attr[$key] = $val;

            // Открываем предложение
            self::open("offer",$attr);

            // Выводим элементы
            foreach($data as $key=>$val)
                if(!in_array($key,$attrKeys))
                    self::complete($key,null,$val);

            // Закрываем предложение
            self::close("offer");
            fwrite(self::fh(), "\n");
        }

        reflex_mysql::clearCache();
        reflex::freeAll();

        if(self::page() >= $items->pages()) {
            self::exportComplete();
            self::setPage(0);
            return array(
                "done" => true
            );
        }

        return array(
            "log" => "Выгружается страница ".self::page(),
        );

    }

    /**
     * Завершающий этап выгрузки в Яндекс-маркет
     * Дописывает в файл выгрузки "хвост" xml и выкладывает готовый xml
     **/
    private static function exportComplete() {
        self::close("offers");
        self::close("shop");
        self::close("yml_catalog",array("date"=>util::now()));
        file::get("/eshop/yandex/yandexmarket.xml")->delete();
        fclose(self::fh());
        file::get("/eshop/yandex/yandexmarket-new.xml")->rename("/eshop/yandex/yandexmarket.xml");
    }

    /**
    * Записывает в файл выгрузки открывающий тэг
    **/
    public static function open($name,$attrs=array()) {
        $ret = "";
        $ret.= "<$name ";
        if(is_array($attrs))
        foreach($attrs as $key=>$val) {
        
            $val = trim($val);
        
            if($val) {
                $val = htmlspecialchars($val,ENT_QUOTES);
                $ret.= " $key='$val' ";
            }
            
        }
            
        $ret.= ">";
        fwrite(self::fh(), $ret);
    }

    /**
     * Записывает в файл выгрузки закрывающий тэг
     **/
    public static function close($name) {
        fwrite(self::fh(),"</$name>");
    }

    /**
     * Записывает в файл выгрузки самозакрывающий тэг
     **/
    public static function complete($name,$attrs=array(),$content="") {
        self::open($name,$attrs);
        fwrite(self::fh(), htmlspecialchars(trim($content),ENT_QUOTES));
        self::close($name);
    }

}
