<?

class google_translate_log extends reflex {

    public function reflex_table() {

        return array(
            'name' => get_class(),
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    'name' => 'datetime',
                    'type' => 'datetime',
                    'label' => 'Дата / время',
                    'editable' => 2,
                    'default' => "now()",
                ), array (
                    'name' => 'ip',
                    'type' => 'string',
                    "length" => 13,
                    'label' => 'IP адрес',
                    "editable" => 2,
                ), array (
                    'name' => 'original',
                    'type' => 'textfield',
                    "length" => 40,
                    'label' => 'Оригинальный текст',
                    "editable" => 2,
                ), array (
                    'name' => 'originalLength',
                    'type' => 'bigint',
                    'label' => 'Длина оригинального текста',
                    "editable" => 2,
                ),  
            ),
        );
    }

    public static function all() {
        return reflex::get(get_class())->desc("datetime");
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

   
}
