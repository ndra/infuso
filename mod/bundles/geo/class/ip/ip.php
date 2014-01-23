<?

/**
 * Класс для работы с ip-адресами
 **/
class geo_ip extends mod_component {

	private $data = null;
    private $ip = null;

    public function dataWrappers() {
        return array(
            "cache" => "mixed",
        );
    }
    
    public function initialParams() {
        return array(
            "cache" => false,
        );
    }
    
    
    public function current() {
        return new self($_SERVER["REMOTE_ADDR"]);
    }

    public function __construct($ip=null) {
        $this->ip = $ip;
    }

    public static function get($ip) {
        return new self($ip);
    }

    public function ip() {
        return $this->ip;
    }
    
    public function data() {

        if(!$this->data) {
            
            //Получение данных из кеша
            if ($this->param("cache")) {
                $cache = geo_ip_cache::all()->eq("ip", $this->ip)->one();
                if ($cache->exists()) {
                    return array(
                        "country" => $cache->country(),
                        "region" => $cache->region(),
                        "city" => $cache->city(),
                    );
                }
            }
            
            // получаем данные по ip
            $url = "http://ipgeobase.ru:7020/geo?ip=".$this->ip;
            $opts = array(
                "http" => array (
                    "timeout" => 1
                ),
            );

            $context = stream_context_create($opts);
            $string = file_get_contents($url, false, $context);
            
            //Не получили данных от ipgeobase.ru
            if ($string === false) {
                return false;
            }
            
            $xml = simplexml_load_string($string);
            
            $ret = array(
                "ip" => $this->ip,
                "country" => $xml->ip->country."",
                "region" => $xml->ip->region."",
                "city" => $xml->ip->city."",
            );
            
            //Добавляем данные в кеш
            if ($this->param("cache")) {
                reflex::create("geo_ip_cache", $ret);
            }
            
            return $ret;
        }
    }

    /**
     * Запрашивает в
     **/
    public static function requestData($ip) {
    }

    /**
     * @return Возвращает объект города, которому принадлежит данный ip адрес
     * Еслив базе нет соответствующего города, метод вернет виртуальный объект
     **/
    public function city() {
        $data = $this->data();
        
        //Данные не получены, возвращаем пустой объект
        if ($data === false) {
            return reflex::virtual("geo_city");
        }
        
        $city = geo_city::all()->eq("title",$data["city"])->one();
        
        //Если такого нет в БД, создаем виртуальный объект с таким названием
        if (!$city->exists()) {
            $city = reflex::virtual("geo_city",array(
                "title" => $data["city"],
            ));
        }
        
        return $city;
    }

}
