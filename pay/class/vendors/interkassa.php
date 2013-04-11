<?php
/**
 * Драйвер системы оплаты Interkassa
 * http://interkassa.ru
 *
 * @version 0.2
 * @package pay
 * @author Alexey.Dvourechesnky <alexey@ndra.ru>
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
class pay_vendors_interkassa extends pay_vendors {

    /**
     * Защитный ключ
     **/
    private static $key = null;
    private static $login = null;

    /**
     * Валюта зачисляемых денежных средств: только RUR (код 643)
     **/
    private static $currency = 643;

    /**
    * Заполняем данные по умолчанию для драйвера Interkassa
    *
    * @return void
    **/
    private function loadConf() {
        if (null == self::$key = mod::conf("pay:interkasssa-key"))
            throw new Exception("Не задан секретный ключ");
        if (null == self::$login = mod::conf("pay:interkasssa-shopid"))
            throw new Exception("Не задан логин");
    }

    /**
    * Зачисление денежных средств для драйвера Interkassa
    *
    * @return void
    * @todo Изменить вызов метода incoming у инвойса
    **/
    public function index_result($p = null) {

        self::loadConf();
  
        $out_summ = $_REQUEST['ik_payment_amount'];
        $inv_id =  $_REQUEST['ik_payment_id'];
        
        //Вычесляем хеш суммы по пришедшим полям
        $sing_hash_str =$_REQUEST['ik_shop_id'].':'. 
                        $_REQUEST['ik_payment_amount'].':'.
                        $_REQUEST['ik_payment_id'].':'.
                        $_REQUEST['ik_paysystem_alias'].':'.
                        $_REQUEST['ik_baggage_fields'].':'.
                        $_REQUEST['ik_payment_state'].':'.
                        $_REQUEST['ik_trans_id'].':'.
                        $_REQUEST['ik_currency_exch'].':'.
                        $_REQUEST['ik_fees_payer'].':'.
                        self::$key;
        $sign_hash = strtoupper(md5($sing_hash_str)); //Склееную строку  в md5 и вверхний регистр(обязаловка)
        
        if($_REQUEST['ik_sign_hash'] != $sign_hash) {
            throw new Exception("Неверная подпись CRC");
        }

        // Загружаем счет
        $invoice = pay_invoice::get((integer)$inv_id);
        
        //Зачисляем средства
        $invoice->incoming(array(
            "sum" => (string)$out_summ,
            "driver" => "Interkassa")
        );
    }

    /**
    * Выполнено зачисление средств
    *
    * @return void
    * @todo Нужно переписать методы index_success и index_fail что бы они возвращали редирект на страницу счета.
    **/
    public function index_success($p = null) {
       self::loadConf();
       $inv_id = $_REQUEST["ik_payment_id"];
       $invoice = pay_invoice::get((integer)$inv_id);
       
       header("location: {$invoice->url()}");
       die();
    }

    /**
    * Ошибка при зачисление денежных средств
    *
    * @return void
    * @todo Нужно переписать методы index_success и index_fail что бы они возвращали редирект на страницу счета.
    **/
    public function index_fail($p = null) {
       self::loadConf();
       $inv_id = $_REQUEST["ik_payment_id"];
       $invoice = pay_invoice::get((integer)$inv_id);
       
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
        
        $url  = "http://www.interkassa.com/lib/payment.php?"; //урл куда надо отрпавить        
        
        $parameters = array(
            'ik_shop_id' => self::$login,
            'ik_payment_amount' => $this->invoice()->sum(),
            'ik_payment_id' =>$this->invoice()->id(),
            'ik_payment_desc' => "Оплата по счету: " . $this->invoice()->id() . ". " . mb_substr($this->invoice()->details(), 0, 99),
            'ik_paysystem_alias' => '',
            'ik_baggage_fields' => '',
        );
        
        
        $url .= http_build_query($parameters);
        
        return $url;

    }


}