<?

class form_conf extends mod_conf {

	public function name() {
	    return "form";
	}

	/**
	 * Возвращает все параметры конфигурации
	 **/
	public function conf() {
	    return array(
	        array(
	            "id" => "form:white_noise_density",
	            "title" => "Плотность белого шума капчи, 1/x (default=6)",
	        ),array(
	            "id" => "form:black_noise_density",
	            "title" => "Плотность черного шума капчи, 1/x (default=30)",
	        ),array(
	            "id" => "form:fluctuation_amplitude",
	            "title" => "Амплитуда колебания символов капчи по вертикали (default=8)",
	        ),array(
	            "id" => "form:rand9",
	            "title" => "Нижний порог амплитуды колебания символов капчи по горизонтали (default=330)",
	        ),array(
	            "id" => "form:rand10",
	            "title" => "Верхний порог амплитуды колебания символов капчи по горизонтали (default=420)",
	        ),
	    );
	}

}
