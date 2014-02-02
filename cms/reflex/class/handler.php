<?

namespace Infuso\Cms\Reflex;

use \user_role, \user_operation;
use \mod, \file, \util;

class handler extends \Infuso\Core\Component implements \mod_handler {

	public function on_mod_init() {
	
	    // Создаем роль «Контент-менеджер»
	    
	    $role = user_role::create("reflex:content-manager","Контент-менеджер");
	    $role->appendTo("admin");
	    user_operation::get("admin:showInterface")->appendTo("reflex:content-manager");
	    
	    // Добавляем операции
	
	    $op = user_operation::create("reflex:editItem");
	    $op->appendTo("reflex:content-manager");
	    
	    $op = user_operation::create("reflex:editConfValue","Редактирование значения настройки")
			->appendTo("admin");
			
		$op = user_operation::create("reflex:viewConf","Просмотр настроек")
			->appendTo("admin");
			
		// Добавляем вкладки в каталоге
        rootTab::create(array(
            "title" => "Контент",
            "name" => "",
            "icon" => self::inspector()->bundle()->path()."/res/icons/48/content.png",
            "priority" => 1000,
		));
		
        rootTab::create(array(
            "title" => "Системные",
            "name" => "system",
            "icon" => self::inspector()->bundle()->path()."/res/icons/48/system.png",
		));
		
		self::buildEditorMap();

	}
	
	public static function buildEditorMap() {
	    $map = array();
		foreach(\mod::service("classmap")->classes("reflex_editor") as $class) {
		    $e = new $class;
		    $map[$e->itemClass()][] = $class;
		}
		$path = mod::app()->varPath()."/reflex/editors.php";
		file::mkdir(file::get($path)->up());
		util::save_for_inclusion($path,$map);
	}
	
}
