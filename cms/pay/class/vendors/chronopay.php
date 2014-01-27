<?php
/**
 * Драйвер системы оплаты Chronopay
 * http://chronopay.com
 *
 * @version 0.4
 * @package pay
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
class pay_vendors_chronopay extends pay_vendors {
    
    /**
     * Защитный ключ
     **/
    private static $key = NULL;
    private static $login = NULL;
    private static $passSecure1 = NULL;
    
    /**
     * Валюта зачисляемых денежных средств: только RUR (код 643)
     **/
    private static $currency = 643;
    
    /**
     * Текущая конфигурация
     **/
    private static $config_id = NULL;
    
    
    /**
    * Заполняем данные по умолчанию для драйвера Robokassa
    *
    * @return void
    **/
    private function loadConf() {
        if (NULL == self::$key = mod::conf("pay:chronopay".self::$config_id."-key")) throw new Exception("Не задан секретный ключ");
        if (NULL == self::$login = mod::conf("pay:chronopay".self::$config_id."-id-site")) throw new Exception("Не задан ид - ид сайта");
        if (NULL == self::$passSecure1 = mod::conf("pay:chronopay".self::$config_id."-secure-1")) throw new Exception("Не задана подпись");
    }
    
    
    /**
    * Устанавливает текущею конфигурацию
    *
    * @return this
    **/
    public function setConf($n = NULL) {
        if ($n > 1) {
            self::$config_id = $n;
        } else {
            self::$config_id = NULL;
        }
        
        return $this;
    }
    
    
    /**
    * Зачисление денежных средств для драйвера
    *
    * @return void
    * @todo Изменить вызов метода incoming у инвойса
    **/
    public function index_result($p = NULL) {
        
        self::loadConf();
        
        if ($p["key"] != self::$key)
            throw new Exception("Неверный защитный ключ");

        $out_summ = $_REQUEST["total"];
        
        $crc = $_REQUEST["sign"];
        $my_crc = md5(self::$passSecure1 . $_REQUEST["customer_id"] . $_REQUEST["transaction_id"] . $_REQUEST["transaction_type"] . $out_summ);
        
        
        if ($my_crc != $crc)
            throw new Exception("Неверная подпись CRC");
        
        // Загружаем счет
        $invoice = pay_invoice::get((integer)$_REQUEST["cs1"]);
        
        if ($invoice->data("currency") != self::$currency)
        {
            $invoice->log("Счет выставлен в другой валюте, код " . $invoice->data("currency"));
            throw new Exception("Счет выставлен в другой валюте, код " . $invoice->data("currency"));
        }
        
        //Зачисляем средства
        $invoice->incoming(array(
            "sum" => (string)$out_summ,
            "driver" => "Chronopay")
        );
    }
    
    /**
    * Выполнено зачисление средств
    *
    * @return void
    * @todo Нужно переписать методы index_success и index_fail что бы они возвращали редирект на страницу счета.
    **/
    public function index_success($p = NULL) {
    
       self::loadConf();
       
       $invoice = pay_invoice::get((integer)$_REQUEST["cs1"]);
       
       header("location: {$invoice->url()}");
       die();
    }
    
    /**
    * Ошибка при зачисление денежных средств
    *
    * @return void
    * @todo Нужно переписать методы index_success и index_fail что бы они возвращали редирект на страницу счета.
    **/
    public function index_fail($p = NULL) {
       self::loadConf();
       
       $invoice = pay_invoice::get((integer)$_REQUEST["cs1"]);
       
       header("location: {$invoice->url()}");
       die();
    }
    
    /**
    * Сгенерировать адрес платежной системы для оплаты
    *
    * @return string
    **/
    public function payUrl() {
        
        self::loadConf();
        
        $crc = md5(self::$login."-0001"."-".$this->invoice()->sum()."-".self::$passSecure1);
        
        $url  = "https://payments.chronopay.com/?";
        
        $parameters = array(
            'product_id' => self::$login."-0001",
            'product_price' => $this->invoice()->sum(),
            'cs1' => $this->invoice()->id(),
            'cs2' => mb_substr($this->invoice()->details(), 0, 99),
            'cb_url' => mod_url::current()->scheme()."://" . mod_url::current()->host() . mod_action::get("pay_vendors_chronopay", "result", array("key"=>self::$key))->url(),
            'cb_type' => 'P',
            'success_url' => mod_url::current()->scheme()."://" . mod_url::current()->host() . mod_action::get("pay_vendors_chronopay", "success")->url(),
            'decline_url' => mod_url::current()->scheme()."://" . mod_url::current()->host() . mod_action::get("pay_vendors_chronopay", "fail")->url(),
            'sign' => $crc,
        );
        
        $url .= http_build_query($parameters);
        
        return $url;
    }
    

    
} // END class
