<?


/**
 * Абстрактный класс драйвера платежной системы
 *
 * @version 0.1
 * @package pay
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
abstract class pay_vendors extends mod_controller {
    
    /**
     * Ссылка на объект Счета
     **/
    protected $invoice = NULL;
    
    /**
    * Конструктор оъбекта
    *
    * @return void
    **/
    public function __construct($invoice = NULL) {
        $this->invoice = $invoice;
    }
    
    /**
    * Возвращает ссылку на объект Счета
    *
    * @return reflex
    **/
    public function invoice() {
        return $this->invoice;
    }
    
    /**
     * Видимость класса для http запросов
     *
     * @return boolean
     **/
    public static function indexTest() { 
        return true;
    }
    
    /**
    * Сгенерировать адрес платежной системы для оплаты
    *
    * @return string
    **/
    abstract public function payUrl();
    
} // END class
