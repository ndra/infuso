<?

/**
 * Драйвер системы оплаты 'единая касса'
 * https://www.walletone.com/
 *
 * @version 0.1
 * @package pay
 * @author Alexey.Dvourechesnky <alexey@ndra.ru>
 **/
class pay_vendors_walletone extends pay_vendors {

    /**
     * Защитный ключ
     **/
    private static $key = null;
    private static $login = null;

    /**
    * Заполняем данные по умолчанию для драйвера Interkassa
    *
    * @return void
    **/
    private function loadConf() {

        if (null == self::$key = mod::conf("pay:walletone-key")) {
            throw new Exception("Не задан секретный ключ");
        }

        if (null == self::$login = mod::conf("pay:walletone-shopid")) {
            throw new Exception("Не задан логин");
        }
    }

    /**
    * Зачисление денежных средств для драйвера walletone
    *
    * @return void
    * @todo Изменить вызов метода incoming у инвойса
    **/
    public function index_result($p = null) {

        self::loadConf();
        $p = $_POST;
        $skey = self::$key;

        // Проверка наличия необходимых параметров в POST-запросе
        if (!isset($p["WMI_SIGNATURE"])) {
            self::printAnswer("Retry", "Отсутствует параметр WMI_SIGNATURE");
        }

        if (!isset($p["WMI_PAYMENT_NO"])) {
            self::printAnswer("Retry", "Отсутствует параметр WMI_PAYMENT_NO");
        }

        if (!isset($p["WMI_ORDER_STATE"])) {
            self::printAnswer("Retry", "Отсутствует параметр WMI_ORDER_STATE");
        }

        // Извлечение всех параметров POST-запроса, кроме WMI_SIGNATURE

        foreach($p as $name => $value) {
            if ($name !== "WMI_SIGNATURE") {
                $params[$name] = $value;
            }
        }

        // Сортировка массива по именам ключей в порядке возрастания
        // и формирование сообщения, путем объединения значений формы

        uksort($params, "strcasecmp");
        $values = "";

        foreach($params as $name => $value) {

            // Конвертация из текущей кодировки (UTF-8)
            // необходима только если кодировка магазина отлична от Windows-1251
            // Я убрал это, хотя непонятно почему [Голиков]
            // $value = iconv("windows-1251","utf-8", $value);

            $values .= $value;
        }

        // Формирование подписи для сравнения ее с параметром WMI_SIGNATURE

        $signature = base64_encode(pack("H*", md5($values . $skey)));

        //Сравнение полученной подписи с подписью W1

        if ($signature == $p["WMI_SIGNATURE"]) {

            if (strtoupper($p["WMI_ORDER_STATE"]) == "ACCEPTED") {

                // Загружаем счет
                $inv_id = $p["WMI_PAYMENT_NO"];
                $invoice = pay_invoice::get((integer)$inv_id);

                //Зачисляем средства
                $result = $invoice->incoming(array(
                    "sum" => (string)$p["WMI_PAYMENT_AMOUNT"],
                    "driver" => "walletone")
                );

                if($result) {
                    self::printAnswer("Ok", "Заказ #" . $p["WMI_PAYMENT_NO"] . " оплачен!");
                }
            }

            else {

                // Случилось что-то странное, пришло неизвестное состояние заказа
                self::printAnswer("Retry", "Неверное состояние ". $p["WMI_ORDER_STATE"]);
            }

        } else {

            // Подпись не совпадает, возможно вы поменяли настройки интернет-магазина
            self::printAnswer("Retry", "Неверная подпись " . $p["WMI_SIGNATURE"]);
        }

    }

    private static function printAnswer($result, $description) {

        mod::service("log")->log(array(
            "type" => "pay/walletone",
            "text" => "$result / $description",
        ));

        print "WMI_RESULT=" . strtoupper($result) . "&";
        print "WMI_DESCRIPTION=" .urlencode($description);
        exit();

    }

    /**
    * Выполнено зачисление средств
    *
    * @return void
    **/
    public function index_success($p = null) {

       self::loadConf();
       $inv_id = $_REQUEST["WMI_PAYMENT_NO"];
       $invoice = pay_invoice::get((integer)$inv_id);

       header("location: {$invoice->url()}");
       die();

    }

    /**
    * Ошибка при зачисление денежных средств
    *
    * @return void
    **/
    public function index_fail($p = null) {

       self::loadConf();
       $inv_id = $_REQUEST["WMI_PAYMENT_NO"];
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

        $url = mod_action::get("pay_vendors_walletone", "create", array(
                "id" => $this->invoice()->id(),
            ))->url();

        return $url;
    }

    public function generatePaymentData() {

        self::loadConf();
        $invoice = $this->invoice();
        //Секретный ключ интернет-магазина
        $key = self::$key;

        $fields = array();

        // Добавление полей формы в ассоциативный массив
        $fields["WMI_MERCHANT_ID"]    = self::$login;
        $fields["WMI_PAYMENT_AMOUNT"] = $invoice->sum();

        $currency = $invoice->data("currency");
        if(!$currency) {
            throw new Exception("Счет № {$invoice->id()}: не задана валюта оплаты");
        }

        $fields["WMI_CURRENCY_ID"]    = $currency;
        $fields["WMI_PAYMENT_NO"]     = $invoice->id();
        $fields["WMI_DESCRIPTION"]    = "BASE64:".base64_encode($invoice->data("title"));
        $fields["WMI_EXPIRED_DATE"]   = $invoice->pdata("date")->shiftYear(1);
        $fields["WMI_SUCCESS_URL"]    = "http://turbotao.com/pay_vendors_walletone/success/";
        $fields["WMI_FAIL_URL"]       = "http://turbotao.com/pay_vendors_walletone/fail/";


        //Сортировка значений внутри полей
        foreach($fields as $name => $val) {
            if (is_array($val)) {
                usort($val, "strcasecmp");
                $fields[$name] = $val;
            }
        }

        // Формирование сообщения, путем объединения значений формы,
        // отсортированных по именам ключей в порядке возрастания.
        uksort($fields, "strcasecmp");
        $fieldValues = "";

        foreach($fields as $value) {

            if (is_array($value)) {

                foreach($value as $v) {
                //Конвертация из текущей кодировки (UTF-8)
                //необходима только если кодировка магазина отлична от Windows-1251
                    $v = iconv("utf-8", "windows-1251", $v);
                    $fieldValues .= $v;
                }

            } else {
                //Конвертация из текущей кодировки (UTF-8)
                //необходима только если кодировка магазина отлична от Windows-1251
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= $value;
            }
        }

        // Формирование значения параметра WMI_SIGNATURE, путем
        // вычисления отпечатка, сформированного выше сообщения,
        // по алгоритму MD5 и представление его в Base64

        $signature = base64_encode(pack("H*", md5($fieldValues . $key)));

        //Добавление параметра WMI_SIGNATURE в словарь параметров формы

        $fields["WMI_SIGNATURE"] = $signature;

        // Формирование HTML-кода платежной формы



        return $fields;


    }

    /**
     * Создает счет для отправки POST формы
     *
     * @return void
     **/
    public function index_create($p = null) {

        self::loadConf();

        //Загружаем счет
        $invoice = pay_invoice::get((integer)$p['id']);

        if (!$invoice->exists()) {
            throw new Exception("Единая касса: не нашли счет с указанным номером");
        }

        if ($invoice->paid()) {
            $invoice->log("Недоступен для оплаты, т.к. счет уже был оплачен ранее");
            throw new Exception("Единая касса: Недоступен для оплаты, т.к. счет уже был оплачен ранее");
        }

        if (!$invoice->my()) {
            throw new Exception("Единая касса: вы не являетесь владельцем счета");
        }

        tmp::exec("/pay/vendors/walletone", array(
            "invoice" => $invoice,
            "login" => self::$login
        ));
    }

}
