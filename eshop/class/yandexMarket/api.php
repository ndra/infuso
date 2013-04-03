<?

/**
 * Класс для вызова методов yandex market api
 **/
class eshop_yandexMarket_api extends mod_component {

public function call($url,$data) {

    $token = self::getToken();
    $data = json_encode($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER , array(
        "Content-Type: application/json",
        "Authorization: OAuth oauth_token=\"$token\", oauth_client_id=\"cabd04864bbe4d58a74be228a22f73f7\", oauth_login=\"studiondra\" "
    ));
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT , 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
    
    ob_start();
    curl_exec($ch);
    $json = ob_get_clean();
    $data = json_decode($json,1);
    
    curl_close($ch);
    return $data; 
    
}

public function getToken() {

    $token = mod_cache::get("864bbe4d58a74be2");
    if(!$token) {

        $postdata = http_build_query(
            array(
                'grant_type' => 'password',
                'username' => 'studiondra',
                'password' => 'fktrcfylhujkbrjd',
                'client_id' => 'cabd04864bbe4d58a74be228a22f73f7',
                'client_secret' => '474f90b06b7148f98e802a4837a30121',
            )
        );      
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://oauth.yandex.ru/token");
        curl_setopt($ch, CURLOPT_POST , 1);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_TIMEOUT , 10);                 
        curl_setopt($ch, CURLOPT_POSTFIELDS , $postdata);
        
        ob_start();
        curl_exec($ch);
        $json = ob_get_clean();
        $data = json_decode($json,1);
        
        curl_close($ch);
        
        $token = $data["access_token"];
    }
    
    mod_cache::set("864bbe4d58a74be2",$token);
    return $token;
}

}
