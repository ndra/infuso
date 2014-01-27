<?

/**
 * Тип поля - валюта
 * В базе храним числовой код валюты
 * http://ru.wikipedia.org/wiki/ISO_4217
 **/
class mod_field_currency extends mod_field_select {

   public static $codes = array(
        643 => array(
            "code" => "RUB",
            "sign" => "р."
        ),
        840 => array(
            "code" => "USD",
            "sign" => "$",
        ),
        978 => array(
            "code" => "EUR",
            "sign" => "&euro;",
        ),
        980 => array(
            "code" => "UAH",
            "sign" => "&#8372;"
        ),
        156 => array(
            "code" => "CNY",
            "sign" => "",
        ),
        398 => array(
            "code" => "KZT", 
            "sign" => "T",   
        )
    );

    public function typeID() {
        return "rtwaho8esx49ijy9rtc1";
    }

    public function typeName() {
        return "Валюта";
    }

    public function mysqlType() {
        return "mediumint(3)";
    }

    public function prepareValue($val) {
        return (int)floor($val);
    }

    /**
     * Возвращает символьный код валюты, например USD
     **/
    public function code() {
        return self::$codes[$this->value()]["code"];
    }

    /**
     * Возвращает знак валюты, например, $
     **/
    public function sign() {
        return self::$codes[$this->value()]["sign"];
    }

    public function options() {
        $ret = array();
        foreach(self::$codes as $n=>$item)
            $ret[$n] = $item["code"];
        return $ret;
    }

}
