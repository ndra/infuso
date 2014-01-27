<?

class moduleManager_tableManager extends mod_controller {

	public static function postTest() {
		return mod_superadmin::check();
	}

	/**
	 * Контроллер получения списка таблиц
	 **/
	public static function post_listTables($p) {
	
	    $ret = array();
		foreach(reflex_table::factoryModuleTables($p["module"]) as $table) {
		    $name = $table->name();
		    $ret["data"][] = array(
				"id" => $table->id(),
		        "data" => array(
			        "text" => $name,
		        ),
			);
		}
		return $ret;
	}

	/**
	 * Контроллер добавления таблирцы
	 **/
	public static function post_addTable($p) {
		reflex_table::create($p["module"]);
		mod::msg("Таблица создана");
	}

    /**
	 * Контроллер удаления таблицы
	 **/
	public static function post_deleteTable($p) {
		reflex_table::factory($p["id"])->delete();
		mod::msg("Таблица удалена");
	}

    /**
	 * Получение настроек таблицы
	 **/
	public static function post_getTableConf($p) {
		$table = reflex_table::factory($p["tableID"]);
		return array(
		    "name" => $table->name(),
		);
	}

    /**
	 * Сохранение настроек таблицы
	 **/
	public static function post_saveTableConf($p) {
		$table = reflex_table::factory($p["tableID"]);
		$table->setName($p["data"]["name"]);
		$table->saveConf();
	}

	public static function post_describeTable($p) {
	
		$table = reflex_table::factory($p["tableID"]);

		$ret = array(
			"cols" => array (
			    array("name" => "editable","type"=>"image"),
			    array("name" => "name", "title"=>"Имя","width"=>100),
			    array("name" => "group", "title"=>"Группа","width"=>100),
			    array("name" => "type", "title"=>"Тип","width"=>200),
			    array("name" => "label", "title"=>"Метка","width"=>200),
			    array("name" => "default", "title"=>"По умолчанию","width"=>200),
			    array("name" => "indexEnabled", "title"=>"Индексировать","type"=>"image"),
			),
			"name" => $table->name(),
			"data" => array(),
		);

		// Поля
		foreach($table->fields() as $field) {

		    $icon = "";
		    if($field->editable())
				$icon = "edit";
				
		    if($field->readonly())
				$icon = "view";

		    $ret["data"][] = array(
		        "id" => $field->id(),
		        "name" => $field->name(),
		        "group" => $field->group(),
				"type" => $field->typeName(),
				"editable" => $icon,
				"label" => $field->label(),
				"default" => $field->defaultValue(),
				"indexEnabled" => $field->indexEnabled() ? "ok" : "",
			);
		}
		
		return $ret;
	}

	public static function post_addField($p) {
		$table = reflex_table::factory($p["tableID"]);
		
		$field = mod::field("textfield")
			->name("new_field");
		
		$table->addField($field);
		$table->saveConf();
	}

	public static function post_deleteField($p) {
		$table = reflex_table::factory($p["tableID"]);
		foreach($p["ids"] as $id)
			$table->deleteField($id);
		$table->saveConf();
	}

	public static function post_getField($p) {

		$table = reflex_table::factory($p["tableID"]);
		$field = $table->fields()->id($p["fieldID"]);

		return array(
		    "field" => $field->conf(),
			"conf" => $field->inxConf(),
		);

	}

	public static function post_getFieldDescr($p) {
		$field = mod_field::get(array(
		    "type" => $p["fieldType"],
		));
		return $field->descr();
	}

	public static function post_getFieldConf($p) {
		$table = reflex_table::factory($p["tableID"]);
		$field = $table->fields()->id($p["fieldID"]);
		$data = $field->conf();
		$data["type"] = $p["typeID"];
		$field = mod_field::get($data);
		$field->addBehaviour("reflex_table_fieldBehaviour");
		return $field->inxConf();
	}

	public static function post_saveField($p) {
		$table = reflex_table::factory($p["tableID"]);
		$field = $table->fields()->id($p["fieldID"]);

		foreach($p["data"] as $key=>$val)
		    $field->conf($key,$val);
		$table->saveConf();
	}

	/**
	 * Поднять поле на одну позицию вверх
	 **/
	public static function post_upField($p) {
		$table = reflex_table::factory($p["tableID"]);
		$table->moveFieldUp($p["fieldID"]);
		$table->saveConf();
	}

	/**
	 * Опустить поле на одну позицию вниз
	 **/
	public static function post_downField($p) {
		$table = reflex_table::factory($p["tableID"]);
		$table->moveFieldDown($p["fieldID"]);
		$table->saveConf();
	}

	/**
	 * Экшн возвращающий список индексов для редактора индексов таблицы
	 **/
	public static function post_listIndexes($p) {

		$ret = array(
			"cols" => array(
			    array("name" => "name", "title"=>"Имя","width"=>100),
			    array("name" => "fields",  "title"=>"Поля","width"=>400),
			    array("name" => "type", "title"=>"Тип","width"=>200),
		    ),
		    "data" => array(),
		);
		
		$table = reflex_table::factory($p["tableID"]);
		foreach($table->indexes() as $index) {

		    $ret["data"][] = array (
				"id" => $index->id(),
		        "data" => array(
					"name" => $index->name(),
					"fields" => $index->fields(),
					"type" => $index->type(),
				),
				"css" => array(
				    "background" => $index->automatic() ? "gray" : "",
				),
				
			);
			
		}

		return $ret;
	}

	/**
	 * Экшн добавления индекса
	 **/
	public static function post_addIndex($p) {
		$table = reflex_table::factory($p["tableID"]);
		$table->addIndex();
		$table->saveConf();
	}

	/**
	 * Экшн добавления в индекс всех полей
	 **/
	public static function post_addFullIndex($p) {

		$table = reflex_table::factory($p["tableID"]);

		$fields = array();
		foreach($table->fields() as $field)
		    $fields[] = $field->name();

		$table->addIndex(array(
		    "name" => "fullIndex",
		    "fields" => implode(",",$fields),
		));

		$table->saveConf();
	}


	/**
	 * Экшн удаления индекса
	 **/
	public static function post_deleteIndex($p) {
		$table = reflex_table::factory($p["tableID"]);
		foreach($p["ids"] as $id)
			$table->deleteIndex($id);
		$table->saveConf();
	}

	/**
	 * Экшн возвращает свойства индекса
	 **/
	public static function post_getIndex($p) {
	
		$table = reflex_table::factory($p["tableID"]);
		foreach($table->indexes() as $index) {
		    if($index->id()==$p["indexID"]) {
				return array(
				    "index" => $index->serialize(),
				);
			}
		}
	}

	public static function post_saveIndex($p) {
		$table = reflex_table::factory($p["tableID"]);
		$index = $table->index($p["indexID"]);
		$index->setData($p["data"]);
		$table->saveConf();
	}

	public static function post_getFieldTypes() {
	    $ret = array();
	    foreach(mod_field::all() as $type)
	        $ret[] = array(
	            "id" => $type->typeID(),
	            "text" => $type->typeName(),
	        );
	    return $ret;
	}

}
