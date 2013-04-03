<?

/**
 * Добавляет функции в АДминку (каталог - редактирование отображения объектов или списка объектов)
 **/
class pay_operationLog_editor extends reflex_editor {

    // Убирает возможность ручного добавления записи
    public function disable() {
        return array("add", "delete", "list");
    }

}
