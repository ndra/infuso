<?

use infuso\core;
namespace infuso\core\cache;

class service extends \infuso\core\service {

    private static $driver = null;
    private static $read = 0;
    private static $write = 0;
    private static $memoryCache = array();
    
    public function defaultService() {
        return "cache";
    }
    
    /**
     * Возвращает драйвер кэширущей системы
     * @todo вернуть возможность выбора драйвера
     **/
    private function driver() {

        if(!self::$driver) {
            switch("filesystem") {
                default:
                case "filesystem":
                    self::$driver = new filesystem();
                    break;
                case "xcache":
                    self::$driver = new xcache();
                    break;
            }
        }
        return self::$driver;
    }

    /**
     * @return Возвращает количество операций считывания
     **/
    public static function read() {
        return self::$read;
    }

    /**
     * @return Возвращает количество операций записи
     **/
    public static function write() {
        return self::$write;
    }

    /**
     * @return mixed Возвращает значение переменной из кэша
     **/
    public static function get($key) {

        \infuso\core\profiler::beginOperation("cache","read",$key);

        self::$read++;
        if(!array_key_exists($key,self::$memoryCache)) {
            self::$memoryCache[$key] = self::driver()->get($key);
        }

        \infuso\core\profiler::endOperation();

        return self::$memoryCache[$key];
        
    }

    /**
     * Записывает переменную в кэш
     **/
    public static function set($key,$val,$ttl = null) {
        \infuso\core\profiler::beginOperation("cache","write",$key);
        self::$write++;
        self::driver()->set($key,$val,$ttl);
        self::$memoryCache[$key] = $val;
        \infuso\core\profiler::endOperation();
    }

    public static function clear() {
        self::driver()->clear();
    }
    
    public static function clearByPrefix($prefix) {
        return self::driver()->clearByPrefix($prefix);
    }

}
