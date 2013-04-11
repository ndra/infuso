<?php


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
        
        $ret = array(
            array("id"=>"pay:pay2pay-merchantId", "title"=>"Pay2Pay: Идентификатор магазина в Pay2Pay"),
            array("id"=>"pay:pay2pay-secretKey", "title"=>"Pay2Pay: Секретный ключ"),
            
            
            array("id"=>"pay:qiwi-merchantId", "title"=>"QIWI: Идентификатор магазина"),
            array("id"=>"pay:qiwi-secretKey", "title"=>"QIWI: Секретный ключ пароль"),
            
            array("id"=>"pay:interkasssa-key","title"=>"Interkasssa: секретный ключ"),
            array("id"=>"pay:interkasssa-shopid","title"=>"Interkasssa: Идентификатор магазина"),
            
            array("id"=>"pay:robokassa-key","title"=>"Robokassa: секретный ключ"),
            array("id"=>"pay:robokassa-login","title"=>"Robokassa: логин"),
            array("id"=>"pay:robokassa-secure-1","title"=>"Robokassa: подпись 1"),
            array("id"=>"pay:robokassa-secure-2","title"=>"Robokassa: подпись 2"),
        );
        
        
        
        //UPD: добавлена возможность использовать несколько конфигураций Chronopay
        $ret[] = array("id"=>"pay:chronopay-num-conf","title"=>"Chronopay: кол-во аккаунтов");
        
        $ret[] = array("id"=>"pay:chronopay-key","title"=>"Chronopay: секретный ключ");
        $ret[] = array("id"=>"pay:chronopay-id-site","title"=>"Chronopay: id сайта");
        $ret[] = array("id"=>"pay:chronopay-secure-1","title"=>"Chronopay: подпись");
        
        
        if (mod::conf("pay:chronopay-num-conf") != NULL && mod::conf("pay:chronopay-num-conf") > 1) {
            for ($i=2; $i <= mod::conf("pay:chronopay-num-conf"); $i++) {
                $ret[] = array("id"=>"pay:chronopay{$i}-key","title"=>"Chronopay {$i}: секретный ключ");
                $ret[] = array("id"=>"pay:chronopay{$i}-id-site","title"=>"Chronopay {$i}: id сайта");
                $ret[] = array("id"=>"pay:chronopay{$i}-secure-1","title"=>"Chronopay {$i}: подпись");               
            }
        }
        
        
        return $ret;
        
    }

}
