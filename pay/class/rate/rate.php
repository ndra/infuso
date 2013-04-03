<?

class pay_rate extends reflex {

    /**
     * Получить курс
     **/
    public static function get($from,$to) {
        return reflex::get(get_class())->eq("from",$from)->eq("to",$to)->one();
    }

    /**
     * Возвращает коллекцию всех курсов
     **/
    public static function all() {
        return reflex::get(get_class())->asc("from")->asc("to",true);
    }

    /**
     * Возвращает значение курса
     **/
    public function rate() {
        return $this->data("rate");
    }

    /**
     * Разделы для каталога
     **/
    public function reflex_root() {
        return array(
            self::all()->param("title","Курсы валют")->param("tab","system"),
        );
    }

    public function reflex_title() {
        return "1 ".$this->field("to")->code()." = ".$this->rate()."&nbsp;".$this->field("from")->code();
    }

    public function setRate($from,$to,$rate) {

        if(!array_key_exists($from,mod_field_currency::$codes))
            return;

        if(!array_key_exists($to,mod_field_currency::$codes))
            return;

        $item = self::get($from,$to);
        if(!$item->exists()) {
            $item = reflex::create(get_class(),array(
                "from" => $from,
                "to" => $to,
                "rate" => $rate,
            ));
        }

        $item->data("rate",$rate);
        $item->data("updated",util::now());

    }

    public function getRate($srcCurrency,$destCurrency) {

        $item = self::get($srcCurrency,$destCurrency);

        if($srcCurrency == $destCurrency) {
            return 1;
        }

        if(!$item->exists()) {
            throw new Exception("Курс валют ".mod::field("currency")->value($srcCurrency)->code()." -> ".mod::field("currency")->value($destCurrency)->code()." не найден");
        }

        return $item->rate();
    }

    public function convert($srcAmount, $srcCurrency,$destCurrency) {
        return $srcAmount / self::getRate($srcCurrency,$destCurrency);
    }

}
