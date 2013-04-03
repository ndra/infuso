<?


class pay_vendors_conf extends mod_conf {

	public function name() {
	    return "pay";
	}
    
    /**
     * Возвращает все параметры конфигурации
     *
     * @return array
     **/
    public function conf() {
        return array(
            array("id"=>"pay:pay2pay-merchantId", "title"=>"Pay2Pay: Идентификатор магазина в Pay2Pay"),
            array("id"=>"pay:pay2pay-secretKey", "title"=>"Pay2Pay: Секретный ключ"),
            
            
            array("id"=>"pay:qiwi-merchantId", "title"=>"QIWI: Идентификатор магазина"),
            array("id"=>"pay:qiwi-secretKey", "title"=>"QIWI: Секретный ключ пароль"),
        );
    }

}
