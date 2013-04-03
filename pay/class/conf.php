<?

class pay_conf extends mod_conf {

    public function name() {
        return "pay";
    }

    public function conf() {

        return array(
            array(
                "id" => "pay:accountCurrency",
                "title" => "Числовой код валюты внутреннего счета пользователя ISO_4217",
                "descr" => "643 - RUB, 840 - USD и т.п.",
            ),
        );
    
    }

}
