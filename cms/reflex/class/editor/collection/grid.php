<?

/**
 * Режим просмотра - список
 **/
class reflex_editor_collection_grid extends reflex_editor_collection_view {

    public function title() {
        return "Таблица";
    }

    public function icon() {
        return "table";
    }

    /**
     * Возвращает данные для колонок таблицы
     **/
    public function cols() {
        return $this->collection()->editor()->gridCols();
    }


    /**
     * Данные таблицы
     **/
    public function inxData() {

        // Сюда складываем данные
        $ret = array();

        // Перебираем коллекцию и складываем данные
        foreach($this->collection()->editors() as $editor) {
        
            $row = $editor->gridData();
            $row["id"] = $editor->hash();
            $ret[] = $row;
        }

        return $ret;
    }

}
