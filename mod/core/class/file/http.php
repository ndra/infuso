<?

/**
 * Класс для работы с внешними файлами
 **/
class mod_file_http extends mod_file {

    private $lastCurl = null;

    public function initialParams() {

        return array(
            "curlOptions" => array(),
        );
    }

    public function __construct($path) {
        $this->path = $path;
    }

	/**
	 * Служебная функция.
	 * Выполняет запрос, обрабатывая редиректы.
	 * Есть параметр CURLOPT_FOLLOWLOCATION, который нельзя применять в safe_mode
	 * или когда задано open_basedir. Эта функция полвзояет обойти данное ограничение
	 **/
    private function curlExecFollow(/*resource*/ $ch, /*int*/ $maxredirect = null) {
        $mr = $maxredirect === null ? 5 : intval($maxredirect);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            if ($mr > 0) {
                $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

                $rch = curl_copy_handle($ch);
                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/', $header, $matches);
                            $newurl = trim(array_pop($matches));
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);
                curl_close($rch);
                if (!$mr) {
                    if ($maxredirect === null) {
                        trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                    } else {
                        $maxredirect = 0;
                    }
                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        return curl_exec($ch);
    }
    
    public function getRedirect($n=20) {
    
        $url = $this."";

        while(1) {
    
	        $ch = $this->getCurl();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$a = curl_exec($ch);
			
			preg_match('/^location: (.*)/im', $a, $r);
			$redirect = trim($r[1]);
			
			preg_match('/^http\/[\d\.]+ (\d+)/im', $a, $r);
			$code = trim($r[1]);

			if($redirect && in_array($code,array(301,302))) {
			    $url = $redirect;
			} else {
			    return $url;
			}
			
			$n--;
			
			if($n<=0) {
			    return $url;
			}
		
		}
    }

    /**
     * Возвращает ресурс curl c базовыми настройкми
     **/
    private function getCurl() {

        $ch = curl_init($this->path());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        foreach($this->param("curlOptions") as $key => $val) {
            curl_setopt($ch, constant($key),$val);
        }

        $this->lastCurl = $ch;

        return $ch;
    }

    /**
     * Возвращает содержимое внешнего файла
     **/
    public function contents() {

        mod_profiler::beginOperation("file","http-contents",$this->path());

        $ch = $this->getCurl();

        $ret = self::curlExecFollow($ch,10);

        curl_close($ch);

        mod_profiler::endOperation();

        return $ret;
    }

    /**
     * Возвращает полный путь к файлу
     **/
    public function native() {
        return $this->path();
    }

    /**
     * Проверяет наличие внешнего файла
     **/
    public function exists() {

        $ch = $this->getCurl();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);  // this works
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        $connectable = curl_exec($ch);
        curl_close($ch);
        return $connectable;
    }

    public function info() {
        return curl_getInfo($this->lastCurl);
    }

    public function errorText() {
        return curl_error($this->lastCurl);
    }

}
