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
            ),array(
                "id" => "form:foreground_color",
                "title" => "Цвет шрифта, в формате 'R,G,B' (default= mt_rand(0,80), mt_rand(0,80), mt_rand(0,80))",
            ),array(
                "id" => "form:background_color",
                "title" => "Задний фон капчи, в в формате 'R,G,B' (default= mt_rand(220,255), mt_rand(220,255), mt_rand(220,255))",
            ),array(
                "id" => "form:allowed_symbols",
                "title" => "Символы используемые для генерации капчи(default=23456789abcdegikpqsvxyz)",
            ),
            
        );
    }

}
