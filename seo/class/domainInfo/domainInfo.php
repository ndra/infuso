<?

class seo_domainInfo extends reflex {

    public function reflex_table() {
        return array (
            'name' => get_class(),
            'fields' => array (
                array (
                  'name' => 'id',
                  'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                  'name' => 'date',
                  'type' => 'date',
                  'editable' => '2',
                  'label' => 'Дата записи',
                  "default" => "now()",
                ), array (
                  'name' => 'domain',
                  'type' => 'textfield',
                  'length' => '50',
                  'label' => 'Домен',
				), array (
                  'name' => 'cy',
                  'type' => 'bigint',
                  'editable' => '2',
                  'label' => 'Yandex CY',
                ),
			),
        );
    }

    /**
     * Возвращает коллекцию всех элементов
     **/
    public static function all() {
        return reflex::get(get_class())->desc("date");
    }

    /**
     * Возвращает элемент по id
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

}
