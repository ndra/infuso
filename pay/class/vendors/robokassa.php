<?
/**
 * Драйвер системы оплаты Robokassa
 * http://robokassa.ru
 *
 * @version 0.2
 * @package pay
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
class pay_vendors_robokassa extends pay_vendors {
        
    /**
     * Защитный ключ
     **/
    private static $key = NULL;
    private static $login = NULL;
    private static $passSecure1 = NULL;
    private static $passSecure2 = NULL;
    
    /**
     * Валюта зачисляемых денежных средств: только RUR (код 643)
     **/
    private static $currency = 643;
    
    /**
     * Возвращает все параметры конфигурации
     *
     * @return array
     **/
    public static function configuration() {
        return array(
            array("id"=>"pay:robokassa-key","title"=>"Robokassa: секретный ключ"),
            array("id"=>"pay:robokassa-login","title"=>"Robokassa: логин"),
            array("id"=>"pay:robokassa-secure-1","title"=>"Robokassa: подпись 1"),
            array("id"=>"pay:robokassa-secure-2","title"=>"Robokassa: подпись 2"),
        );
    }
    
    /**
    * Заполняем данные по умолчанию для драйвера Robokassa
    *
    * @return void
    **/
    private function loadConf() {
        if (NULL == self::$key = mod::conf("pay:robokassa-key"))
            throw new Exception("Робокасса: не задан секретный ключ");
        if (NULL == self::$login = mod::conf("pay:robokassa-login"))
            throw new Exception("Робокасса: не задан логин");
        if (NULL == self::$passSecure1 = mod::conf("pay:robokassa-secure-1"))
            throw new Exception("Робокасса: не задана подпись 1");
        if (NULL == self::$passSecure2 = mod::conf("pay:robokassa-secure-2"))
            throw new Exception("Робокасса: не задана подпись 2");
    }    
    
    /**
    * Зачисление денежных средств для драйвера Robokassa
    *
    * @return void
	* @todo Изменить вызов метода incoming у инвойса
    **/
    public function index_result($p = NULL) {
        
        self::loadConf();
        
        if ($p["key"] != self::$key)
            throw new Exception("Робокасса: неверный защитный ключ");        
        
        $out_summ = $_REQUEST["OutSum"];
        $inv_id = $_REQUEST["InvId"];
        $crc = strtoupper($_REQUEST["SignatureValue"]);
        $my_crc = strtoupper(md5("$out_summ:$inv_id:".self::$passSecure2));
        
        if (strtoupper($my_crc) != strtoupper($crc))
            throw new Exception("Неверная подпись CRC");        
        
        //Загружаем счет
        $invoice = pay_invoice::get((integer)$inv_id);
        
        if ($invoice->data("currency") != self::$currency) {
            $invoice->log("Счет выставлен в другой валюте, код " . $invoice->data("currency"));
            throw new Exception("Счет выставлен в другой валюте, код " . $invoice->data("currency"));
        }
        
        //Зачисляем средства
        $invoice->incoming($out_summ);
     
    }
    
    /**
    * Выполнено зачисление средств
    *
    * @return void
	* @todo Нужно переписать методы index_success и index_fail что бы они возвращали редирект на страницу счета.
    **/
    public function index_success($p = NULL) {
    
        self::loadConf();
       
        $out_summ = $_REQUEST["OutSum"];
        $inv_id = $_REQUEST["InvId"];
        $crc = strtoupper($_REQUEST["SignatureValue"]);
        
        $my_crc = strtoupper(md5("$out_summ:$inv_id:".self::$passSecure1));
        
        if (strtoupper($my_crc) != strtoupper($crc))
            throw new Exception("Неверная подпись CRC");
        
        $invoice = pay_invoice::get((integer)$inv_id);
        
        tmp::exec("pay:success", $invoice);
    }
    
    /**
    * Ошибка при зачисление денежных средств
    *
    * @return void
	* @todo Нужно переписать методы index_success и index_fail что бы они возвращали редирект на страницу счета.
    **/
    public function index_fail($p = NULL) {
        //Выводим шаблон ошибки и отправляем в лог
        tmp::exec("pay:fail");
    }    
    
    /**
    * Сгенерировать адрес платежной системы для оплаты
    *
    * @return string
    **/
    public function payUrl() {
        
        self::loadConf();
        
        $crc = md5(self::$login.":".$this->invoice()->sum().":".$this->invoice()->id().":".self::$passSecure1);
        
        $url  = "https://merchant.roboxchange.com/Index.aspx?";
        
        $parameters = array(
            'MrchLogin' => self::$login,
            'OutSum' => $this->invoice()->sum(),
            'InvId' => $this->invoice()->id(),
            'Desc' => mb_substr($this->invoice()->details(), 0, 99),
            'SignatureValue' => $crc,
        );
        
        $url .= http_build_query($parameters);
        
        return $url;
    }    
    
}
