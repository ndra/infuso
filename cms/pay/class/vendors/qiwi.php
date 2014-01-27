<?php
/**
 * Драйвер системы оплаты QIWI
 * http://ishop.qiwi.ru/
 *
 * @version 0.1
 * @package pay
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
class pay_vendors_qiwi extends pay_vendors {
    
    /**
     * @var array Коды ошибок
     **/
    static $errors = array(
        300 => 'Неизвестная ошибка',
        13  => 'Сервер занят. Повторите запрос позже',
        150 => 'Неверный логин или пароль',
        215 => 'Счёт с таким номером уже существует',
        278 => 'Превышение максимального интервала получения списка счетов',
        298 => 'Агент не существует в системе',
        330 => 'Ошибка шифрования',
        370 => 'Превышено макс. кол-во одновременно выполняемых запросов',
        341 => 'Не указан номер кошелька',
        0   => 'OK',
    );
    
    /**
     * @var array Коды статусов ответа сервера
     **/
    static $statuses = array(
        50  => 'Неоплаченный счёт',
        60  => 'Оплаченный счёт',
        150 => 'Счёт отклонён',
    );
    
    /**
     * Видимость класса для post запросов
     *
     * @return boolean
     **/
    public static function postTest() { 
        return true;
    }
    
    /**
     * Идентификатор магазина
	 * @var $merchantId string
     **/
    private static $merchantId = NULL;
    
    /**
     * Секретный ключ пароль
	 * @var $secretKey string
     **/
    private static $secretKey = NULL;
    
    /**
     * Валюта зачисляемых денежных средств: только RUB (код 643)
     **/
    private static $currency = 643;
    
    /**
     * Заполняем данные по умолчанию для драйвера
     *
     * @return void
     **/
    private function loadConf() {
        if (NULL == self::$merchantId = mod::conf("pay:qiwi-merchantId"))
            throw new Exception("QIWI: не задан идентификатор магазина");
        
        if (NULL == self::$secretKey = mod::conf("pay:qiwi-secretKey"))
            throw new Exception("QIWI: не задан секретный ключ пароль");
    }
    
    /**
     * Враппер методов для доступа к параметрам
     *
     * @return array
     **/
    public function dataWrappers() {
        return array(
            "ltime" => "mixed",
        );
    }
    
    /**
     * Параметры по умолчанию
     *
     * @return array
     **/
    public function initialParams() {
        return array(
            "ltime" => 24, //Время жизни счета для оплаты с помощью qiwi
        );
    }
    
	public function confDescription() {
	    return array(
	        "components" => array(
	            get_called_class() => array(
	                "params" => array(
	                    "ltime" => "Время жизни счета для оплаты с помощью qiwi",
					),
				),
			),
		);
	}
    
    /**
     * Сгенерировать ключ для шифрования
     *
     * @return string
     **/
    public function key() {
        $merchantId = self::$merchantId;
        $secretKey =  self::$secretKey;
        
        $passwordMD5 = md5(self::$secretKey, TRUE);
        $salt = md5(self::$merchantId . bin2hex($passwordMD5), TRUE);
        $key = str_pad($passwordMD5, 24, '\0');
        
        for ($i = 8; $i < 24; $i++) {
          if ($i >= 16) {
              $key[$i] = $salt[$i-8];
          } else {
              $key[$i] = $key[$i] ^ $salt[$i-8];
          }
        }
        
        return $key;
    }
    
    /**
     * Шифровать данные перед отправкой
     *
     * @return string
     **/
    public function encrypt($xml) {
        $n = 8 - strlen($xml) % 8;
        $pad = str_pad($xml, strlen($xml) + $n, ' ');
        
        $encrypt = mcrypt_encrypt(MCRYPT_3DES, self::key(), $pad, MCRYPT_MODE_ECB, '\0\0\0\0\0\0\0\0');
        
        $result = "qiwi" . str_pad(self::$merchantId, 10, "0", STR_PAD_LEFT) . "\n";
        $result .= base64_encode($encrypt);
        
        return $result;
    }
    
    /**
     * Сгенерировать адрес платежной системы для оплаты
     *
     * @return string
     **/
    public function payUrl() {
        
        $url = mod_action::get("pay_vendors_qiwi", "create", array(
                "id" => $this->invoice()->id(),
            ))->url();
        
        return $url;
    }    
    
    /**
     * Создает счет для отправки POST формы
     *
     * @return void
     **/
    public function index_create($p = null) {
        
        //Загружаем счет
        $invoice = pay_invoice::get((integer)$p['id']);
		
		if (!$invoice->exists())
			throw new Exception("QIWI: не нашли счет с указанным номером");
		
		if ($invoice->paid()) {
			$invoice->log("Не доступен для оплаты, т.к. счет уже был оплачен ранее");
			throw new Exception("QIWI: Не доступен для оплаты, т.к. счет уже был оплачен ранее");
		}
        
        if (!$invoice->my())
            throw new Exception("QIWI: вы не являетесь владельцем счета");
        
        tmp::exec("/pay/vendors/qiwi", array(
            "id" => $p["id"],
            "number" => $_POST["number"]
        ));
    }
    
    /**
     * Создаем счет в системе QIWI
     *
     * @return void
     **/
    public function post_create($p = null) {
        
        self::loadConf();
        
        $merchantId = self::$merchantId;
        $secretKey =  self::$secretKey;
        
		if (!$p['id'])
			throw new Exception("QIWI: счет не заполнен");
		
        //Загружаем счет
        $invoice = pay_invoice::get((integer)$p['id']);
		
		if (!$invoice->exists())
			throw new Exception("QIWI: не нашли счет с указанным номером");
		
		if ($invoice->paid()) {
			$invoice->log("Не доступен для оплаты, т.к. счет уже был оплачен ранее");
			throw new Exception("QIWI: Не доступен для оплаты, т.к. счет уже был оплачен ранее");
		}
        
        if (!$invoice->my())
            throw new Exception("QIWI: вы не являетесь владельцем счета");
        
        $invoiceId = $invoice->num();
        $invoiceAmount = $invoice->sum();
        
        $desc = mb_substr($invoice->details(), 0, 99);
        
        $ltime = $this->ltime();
        
        $xml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<request>
<protocol-version>4.00</protocol-version>
<request-type>30</request-type>
<extra name="password">{$secretKey}</extra>
<terminal-id>{$merchantId}</terminal-id>
<extra name="comment">$desc</extra>
<extra name="to-account">{$p['number']}</extra>
<extra name="amount">{$invoiceAmount}</extra>
<extra name="txn-id">{$invoiceId}</extra>
<extra name="ALARM_SMS">0</extra>
<extra name="ACCEPT_CALL">0</extra>
<extra name="ltime">{$ltime}</extra>
</request>
EOF;
        //Кодируем XML для отправки на сервер
        $content = self::encrypt($xml);
        
        $params = array('http' => array(
            'method' => 'POST',
            'header' => "Content-Type: text/xml; encoding=utf-8\r\n",
            'content' => $content,
        ));
        
        $ctx = stream_context_create($params);
        
        $url = "http://ishop.qiwi.ru/xml";
        
        $file = @fopen($url, 'rb', false, $ctx);
        
        //Читаем ответ от сервера
        $response = "";
        
        while (!feof($file)) {
          $response .= fread($file, 8192);
        }
        
        //Закрываем соединение
        fclose($file);
        
        $responseXml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>' . $response);
        
        $responseCode = (string)$responseXml->{'result-code'};
        
        if ($responseCode == 0) {
            
            //Теперь этот счет можно оплатить только драйвером  QIWI
            $invoice->data("driverUseonly", "qiwi");
            
            //Счет требеут проверки
            $invoice->status(pay_invoice::STATUS_CHECK);
            
            header("location: {$invoice->url()}");
            die();
        }
        
        $responseCodeText = self::$errors[$responseCode];
        tmp::param("pay-vendors-qiwi-error", "Код ответа: " . " ($responseCode) " . $responseCodeText);
    }
    
    /**
     * Проверка статуса оплаты счета платежной системы QIWI
     *
     * @return void
     **/
    public function check() {
        
        self::loadConf();
        
        $merchantId = self::$merchantId;
        $secretKey =  self::$secretKey;
        
        $invoiceId = $this->invoice()->num();
        
        $xml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<request>
<protocol-version>4.00</protocol-version>
<request-type>33</request-type>
<extra name="password">{$secretKey}</extra>
<terminal-id>{$merchantId}</terminal-id>
<bills-list>
<bill txn-id="$invoiceId"/>
</bills-list>
</request>
EOF;
        
        //Кодируем XML для отправки на сервер
        $content = self::encrypt($xml);
        
        $params = array('http' => array(
            'method' => 'POST',
            'header' => "Content-Type: text/xml; encoding=utf-8\r\n",
            'content' => $content,
        ));
        
        $ctx = stream_context_create($params);
        
        $url = "http://ishop.qiwi.ru/xml";
        
        $file = @fopen($url, 'rb', false, $ctx);
        
        //Читаем ответ от сервера
        $response = "";
        
        while (!feof($file)) {
          $response .= fread($file, 8192);
        }
        
        //Закрываем соединение
        fclose($file);
        
        $responseXml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>' . $response);
        
        $responseCode = (string)$responseXml->{'result-code'};
        
        if ($responseCode == 0 ) {
            //Получили ответ от сервера
            
            $responseBill = $responseXml->{'bills-list'}->{'bill'}[0];
            
            if ($responseBill['id'] == $invoiceId) {
                
                //Проверяем статус ответа
                $status = (integer)$responseBill['status'];
                
                //Записываем в лог
                $this->invoice()->log("Драйвер QIWI ответ сервера после проверки статуса оплаты: (код {$status})" . self::$statuses[$status]);
                
                //Отменяем счет
                if ($status == 150) {
                    $this->invoice()->status(pay_invoice::STATUS_CANCELED);
                }
                
                //Оплаченный счет
                if ($status == 60) {
                    //Зачисляем сумму
                    $this->invoice()->incoming(array(
                        "sum" => $responseBill['sum'],
                        "driver" => "QIWI",
                    ));
                }
                
            } else {
                $this->invoice()->log("Error драйвер QIWI: ответ сервера возвращает другой id счета");
            }
            
        } else {
            $responseCodeText = self::$errors[$responseCode];
            //Ошибка, записываем в лог счета
            $this->invoice()->log("Error драйвер QIWI: получили ошибку с сервера \n" . "Код ответа: " . " ($responseCode) " . $responseCodeText);
        }
        
        
        //Изменяем время проверки счета
        $this->invoice()->data("timeCheck", util::now());
        
    }
    
} //END CLASS
