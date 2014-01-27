<?

class mod_crypt {

	public static function hash($source) {
		$salt = mod::id(12);
		return $salt.md5($source.$salt);
	}

	public static function checkHash($hash,$source) {

		// Старый метод шифрования
		if(substr($hash,0,3)=='$1$') {
		    return crypt($source,$hash)==$hash;
		}

		$salt = mb_substr($hash,0,12);
		$hash2 = $salt.md5($source.$salt);
		return $hash == $hash2;
	}

}
