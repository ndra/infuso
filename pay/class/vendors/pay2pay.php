<?php
/**
 * Драйвер системы оплаты pay2pay
 * http://www.pay2pay.com/
 *
 * @version 0.1
 * @package pay
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
class pay_vendors_pay2pay extends pay_vendors {
        
    /**
     * Идентификатор магазина в Pay2Pay
	 * @var $merchantId string
     **/
    private static $merchantId = NULL;
	
    /**
     * Секретный ключ
	 * @var $secretKey string
     **/
    private static $secretKey = NULL;
    
    /**
     * Валюта зачисляемых денежных средств: только RUB (код 643)
     **/
    private static $currency = 643;
    
    
    /**
     * Способ оплаты в системе Pay2Pay
     **/
    private $paymode = NULL;
    
    /**
     * Читает или записывает способ оплаты в системе Pay2Pay
     **/
    public function paymode($p = NULL) {
        if(func_num_args() == 0) {
            return $this->paymode;
        }
        
        if(func_num_args() == 1) {
            $this->paymode = $p;
            return $this;
        }
    }
    
    /**
    * Заполняем данные по умолчанию для драйвера
    *
    * @return void
    **/
    private function loadConf() {
        if (NULL == self::$merchantId = mod::conf("pay:pay2pay-merchantId"))
            throw new Exception("Pay2Pay: не задан идентификатор магазина в Pay2Pay");
		
        if (NULL == self::$secretKey = mod::conf("pay:pay2pay-secretKey"))
            throw new Exception("Pay2Pay: не задан секретный ключ");
    }    
    
    /**
    * Зачисление денежных средств для драйвера
    *
    * @return void
    **/
    public function index_result($p = NULL) {
        
        self::loadConf();
        
		// Если не получили параметры xml и sign, то нечего проверять
        if (!isset($_REQUEST['xml']) && !isset($_REQUEST['sign']))
            throw new Exception("Pay2Pay: не получили параметры xml и sign");        
		
		// Декодируем входные параметры
		$xml = base64_decode(str_replace(' ', '+', $_REQUEST['xml']));
		$sign = base64_decode(str_replace(' ', '+', $_REQUEST['sign']));
		
		// преобразуем входной xml в удобный для использования формат
		$vars = simplexml_load_string($xml);
		
		// Если поле order_id не заполнено, продолжать нет смысла.
		if (!$vars->order_id)
			throw new Exception("Pay2Pay: поле order_id не заполнено");
		
        //Загружаем счет
        $invoice = pay_invoice::get((integer)$vars->order_id);
		
		if (!$invoice->exists())
			throw new Exception("Pay2Pay: не нашли счет с указанным номером");
		
		if ($invoice->paid()) {
			$invoice->log("Не доступен для оплаты, т.к. счет уже был оплачен ранее");
			throw new Exception("Pay2Pay: Не доступен для оплаты, т.к. счет уже был оплачен ранее");
		}
		
		if (md5(self::$secretKey . $xml . self::$secretKey) != $sign)
			throw new Exception("Pay2Pay: Неверная подпись");
		
		//Нельзя принимать платеж, если валюта платежа и счета не совпали
		if ($invoice->currencyName() != (string)$vars->currency)
			throw new Exception("Pay2Pay: валюта платежа и счета не совпали");
		
		//Если статус платежа окончательный, то нужно обновить заказ - дословно из документации.
		if ((string)$vars->status == "success") {
			//Зачисляем средства
	        $invoice->incoming(array("sum" => (string)$vars->amount, "driver" => "Pay2Pay"));
		}
		
		
		//Если зачислили деньги то возвращаем успешный ответ платежной системе
		if ($invoice->paid()) {
			$ret = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?".">
					<response>
			  		  <status>yes</status>
			          <err_msg></err_msg>
			        </response>";
			die($ret);
		}
     
    }
    
    /**
    * Выполнено зачисление средств
    *
    * @return void
    **/
    public function index_success($p = NULL) {
       
        self::loadConf();
        
		// Если не получили параметры xml и sign, то нечего проверять
        if (!isset($_REQUEST['xml']) && !isset($_REQUEST['sign']))
            throw new Exception("Pay2Pay: не получили параметры xml и sign");        
		
		// Декодируем входные параметры
		$xml = base64_decode(str_replace(' ', '+', $_REQUEST['xml']));
		$sign = base64_decode(str_replace(' ', '+', $_REQUEST['sign']));
		
		// Преобразуем входной xml в удобный для использования формат
		$vars = simplexml_load_string($xml);
		
		// Неверная подпись
		if (md5(self::$secretKey . $xml . self::$secretKey) != $sign)
			throw new Exception("Pay2Pay: Неверная подпись");
		
		// Если поле order_id не заполнено, продолжать нет смысла.
		if (!$vars->order_id)
			throw new Exception("Pay2Pay: поле order_id не заполнено");
		
        // Загружаем счет
        $invoice = pay_invoice::get((integer)$vars->order_id);
		
        header("location: {$invoice->url()}");
        die();
    }
    
    /**
    * Ошибка при зачисление денежных средств
    *
    * @return void
    **/
    public function index_fail($p = NULL) {
		
        self::loadConf();
        
		// Если не получили параметры xml и sign, то нечего проверять
        if (!isset($_REQUEST['xml']) && !isset($_REQUEST['sign']))
            throw new Exception("Pay2Pay: не получили параметры xml и sign");        
		
		// Декодируем входные параметры
		$xml = base64_decode(str_replace(' ', '+', $_REQUEST['xml']));
		$sign = base64_decode(str_replace(' ', '+', $_REQUEST['sign']));
		
		// Преобразуем входной xml в удобный для использования формат
		$vars = simplexml_load_string($xml);
		
		// Неверная подпись
		if (md5(self::$secretKey . $xml . self::$secretKey) != $sign)
			throw new Exception("Pay2Pay: Неверная подпись");
		
		// Если поле order_id не заполнено, продолжать нет смысла.
		if (!$vars->order_id)
			throw new Exception("Pay2Pay: поле order_id не заполнено");
		
        // Загружаем счет
        $invoice = pay_invoice::get((integer)$vars->order_id);
		
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
		
		$url = mod_action::get("pay_vendors_pay2pay", "redirect")->url()."?";
		
		$merchantId = self::$merchantId;
		$orderId = $this->invoice()->id();
		$amount = $this->invoice()->sum();
		$currency = $this->invoice()->currencyName();
		$desc = mb_substr($this->invoice()->details(), 0, 99);
		
        $paymode = "";
        
        if ($this->paymode()) {
            $paymode = "<paymode><code>{$this->paymode()}</code></paymode>";
        }
        
		// Формируем xml
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?".">
			    <request>
					<version>1.2</version>
					<merchant_id>$merchantId</merchant_id>
					<language>ru</language>
					<order_id>$orderId</order_id>
					<amount>$amount</amount>
					<currency>$currency</currency>
					<description>$desc</description>
                    $paymode
		        </request>";
		
		
		// Вычисляем подпись
		$sign = md5(self::$secretKey . $xml . self::$secretKey);
		
		// Кодируем данные в BASE64
		$xml_encode = base64_encode($xml);
		$sign_encode = base64_encode($sign);
		
		
        $parameters = array(
            'xml' => $xml_encode,
            'sign' => $sign_encode,
        );
        
        $url .= http_build_query($parameters);
        
        return $url;
    }    
    
	/**
    * Редирект для отправки POST формы
    *
    * @return void
    **/
    public function index_redirect() {
        tmp::exec("/pay/vendors/pay2pay", array(
            "xml_encode" => $_GET["xml"],
            "sign_encode" => $_GET["sign"],
            ));
    }
	
} //END CLASS
