<?

namespace Infuso\Cms\Reflex;

use \user, \admin, \mod, \inx;

/**
 * Основной контроллер каталога
 **/
class controller extends \Infuso\Core\Controller {

    /**
     * Видимость для браузера
     **/
    public static function indexTest() {
        return user::active()->checkAccess("admin:showInterface");
    }

    /**
     * Видимость для POST-команд
     **/
    public static function postTest() {
        return user::active()->checkAccess("admin:showInterface");
    }

    /**
     * Основной контроллер
     **/
    public static function index($p) {
    
        admin::header("Каталог");

        mod::service("reflexEditor")->clearCache();

        inx::add(array(
            "type" => "inx.mod.reflex.editor",
            "menu" => $p["menu"],
            "tabData" => $p["menu"]=="hide" ? null : self::tabData(),
        ));

        admin::footer();
    }
    
    private static function tabData() {

        $ret = array();
        
        foreach(rootTab::allVisible() as $tab) {
	        $ret[] = array(
	            "text" => $tab->title(),
	            "name" => $tab->data("name"),
	            "icon" => $tab->icon()."",
			);
		}

        return $ret;
        
    }

    /**
     * Контроллер просмотра элемента
     * Перенаправляет браузер на страницу элемента
     **/
    public function index_view($p) {

        $item = self::get($p["id"])->item();

        if(!$item->published()) {
            echo "<script>close();</script>";
            die();
        }

        $url = $item->url();

        if($url) {
            header("location:$url");
            die();
        }

        echo "<script>close();</script>";

    }

    /**
     * Если не получилось войти - показываем страницу авторизации
     **/
    public static function indexFailed() {
        admin::fuckoff();
    }

    /**
     * Возаращает объект класса reflex_editor по индексу
     **/
    public function get($index) {

        if(!$index) {
            $editor = new \reflex_editor_root_editor(0);
            $index = $editor->hash();
        }

        $editor = \reflex_editor::byHash($index);

        return $editor;

    }

    public function byOldIndex($index) {

        list($class,$id) = explode(":",$index);
        return reflex::get($class,$id);

    }

    /**
     * Возвращает список на основе переменной $_REQUEST
     **/
    public function getListByP($p,$filter=true) {
        $list = \Infuso\ActiveRecord\Collection::unserialize($p["listData"]);
        $list->addBehaviour("reflex_editor_collection");
        $list->applyParams($p);
        return $list;
    }

    /**
     * Загрузчик нод дерева в левом меню
     **/
    public static function post_views($p) {

        $p["expanded"][] = $p["id"]."";

        $node = self::treeNode($p["id"],$p["expanded"],$p["tab"]);
        $nodes = $node["children"];

        return array(
			"data" => $nodes,
        );
    }

    /**
     * Возвращает данные ноды дерева
     **/
    public function treeNode($nodeID,&$expanded=array(),$tab=null) {

        $index = explode("/",$nodeID);
        $index = end($index);
        $editor = self::get($index);
        $item = $editor->item();

        // Название элемента
        $text = $editor->title();
        if(!trim($text))
            $text = "&mdash;";

        // Мета-заголовок элемента
        $title = $item->meta("title");
        if($title)
            $text.= "&nbsp;&mdash; <i style='color:gray' >{$title}</i>";

        // Количество дочерних элементов
        if($n = $editor->numberOfChildren())
            $text.= " ($n)";

        // Иконка элемента
        $icon = $editor->icon();

        $node = array(
            "id" => $nodeID,
            "text" => $text,
            "icon" => $icon,
            "folder" => $editor->numberOfChildren(),
        );

        // Подписываем ноду
        $node["dataHash"] = md5(serialize($node).":".serialize($item->data()));

        // Если нода находится в списке раскрытых, мы рендерим ее потомков
        if(in_array($nodeID,$expanded)) {

            // Собираем ноды дочерних элемиентов
            $children = array();
            $lastGroup = null;
            foreach($item->editor()->editorChildren() as $key=>$editor) {
                if($editor->tab() == $tab) {

                    // Пропускаем редакторы, которые нельзя просматривать
                    if(!$editor->beforeView()) {
                        continue;
                    }

                    // Эта часть кода сработает только для первого уровня каталога
                    // (Основного меню)
                    // Для первого уровня мы добавляем заголовки групп и разделители
                    if($nodeID=="0") {

                        $group = $editor->group();

                        if($group!=$lastGroup) {

                            // Выводим разделитель (только если это не первая нода)
                            if($key)
                                $children[] = array(
                                    "text" => "<div style='padding:5px;' ><div style='border-bottom:1px solid #ededed;' ></div></div>",
                                    "noedit" => true,
                                    "selectable" => false,
                                );

                            // Выводим группу нод
                            $children[] = array(
                                "text" => "<div style='font-size:18px;' >{$group}</div>",
                                "noedit" => true,
                                "selectable" => falsem
                            );

                        }

                        $lastGroup = $group;
                    }


                    $childID = $nodeID."/".$editor->hash();
                    $children[$childID] = self::treeNode($childID,$expanded);
				}
            }

            // На данный момент в массиве $children ключами нод являются их id
            // Делаем нумерацию с нуля в массиве $children
            $children = array_values($children);

            if(count($children)) {
                $node["children"] = $children;
                $node["expanded"] = true;
            }
        }

        return $node;
    }

    /**
     * Проверяет, не изменились ли данные в дереве (левое меню каталога)
     * $p - массив типа nodeID => dataHash
     * В этом массиве содержатся все видимые ноды в каталоге
     * Метод пробегается по всем нодам из массива, и, если найдет хоть одно изменение, вернет true
     **/
    public function post_checkTreeChanges($p) {

        foreach($p["data"] as $id=>$hash) {
            $data = self::treeNode($id);
            if($data["dataHash"]!=$hash) {
                return true;
            }
        }
    }

    /**
     * Контроллер получения списка данных
     **/
    public static function post_getList($p) {
        $list = self::getListByP($p);
        if(!$list->editor()->beforeCollectionView()) {
            $msg = "У вас нет прав для просмотра списка элементов ".get_class($list->editor());
            mod::msg($msg,1);
            return false;
        }
        return $list->inxData();
    }

    /**
     * Контроллер получения данных одного элемента
     * Используется для вывода карточки элмента в каталоге
     **/
    public static function post_getItem($p) {

        $editor = self::get($p["index"]);
        $item = $editor->item();

        // Проверяем возвожность редактировать элемент
        if(!$editor->beforeView()) {
            return array (
                "item" => array(
                    "html" => "У вас нет прав для редактирвоания этого объекта.",
                    style => array(
                        padding => 50,
                    )
                )
            );
        }

        // Составляем список родителей
        $parents = array();
        foreach($item->parents() as $parent) {
            $parents[] = array(
                "index" => $parent->editor()->hash(),
                "text" => $parent->title()
            );
        }

        // Добавляем в крошки сам элемент
        $parents[] = array(
            "index" => $editor->hash(),
            "text" => $editor->title(),
        );

        return array(
            "item" => $editor->inxEditor(),
            "parents" => $parents,
        );
    }

    /**
     * Сохраняет объект
     **/
    public static function post_save($p) {

        $editor = self::get($p["index"]);
        $item = $editor->item();

        if(!$editor->beforeEdit()) {
            mod::msg("У вас нет прав для редактирования этого объекта",1);
            return;
        }

        foreach($p["data"] as $key=>$val) {
            $item->data($key,$val);
        }

        if($item->testForParentsRecursion()) {
            $item->revert();
            mod::msg("Обнаружена рекурсия, объект не сохранен",1);
            return;
        }

        // Список измененных полей в текстовом формате
        $dirty = array();
        foreach($item->fields()->changed() as $field) {
            $dirty[] = $field->name();
        }
        $dirty = implode(",",$dirty);

        if(!$item->fields()->changed()->count()) {

            mod::msg("Изменений не обнаружено");

        } elseif($item->store()) {

            mod::msg("Сохранено");

            if($editor->log()) {
                $item->log("Изменение данных ".$dirty);
			}
			
			$editor->afterChange();

            return true;

        } else {
            mod::msg("Объект не сохранен",1);
        }

    }

    /**
     * Возвращает ссылку на страницу элемента
     **/
    public function post_viewItem($p) {
        $item = self::get($p["index"]);
        if(!$item->exists())
            return false;
        return $item->url();
    }

    /**
     * Врзвращает данные для фильтра
     **/
    public function post_getFilter($p) {
        $list = self::getListByP($p);

        if(!$list->editor()->beforeCollectionView())
            return false;

        return $list->editor()->inxFilter($list);
    }

    /**
     * Возвращает данные для редактора поля
     **/
    public static function post_getField($p) {

        $editor = reflex_editor::byHash($p["editor"]);

        if(!$editor->beforeEdit()) {
            mod::msg("Ошибка доступа: вы не можете редактировать объект",1);
            return false;
        }

        $field = $editor->item()->field($p["name"]);

        if($field->editable()) {
            return array(
                "mode" => "editor",
                "editor" => $field->editorInxFull($item),
            );
        }

        mod::msg("Ошибка доступа: выбранное поле нельзя редактировать",1);

    }

    /**
     * Сохраняет данные поля (редактирование поля)
     **/
    public static function post_saveField($p) {

        $editor = reflex_editor::byHash($p["editor"]);

        if(!$editor->beforeEdit()) {
            mod::msg("Ошибка доступа: вы не можете редактировать объект",1);
            return false;
        }

        $item = $editor->item();

        $field = $editor->item()->field($p["name"]);
        $item->data($field->name(),$p["value"]);

        if($item->store()) {
            mod::msg("Сохранено");
            $item->log("Изменение поля {$p[name]}");
        } else {
            mod::msg("Объект не сохранен",1);
        }
    }

    /**
     * Контроллер создания конструктора
     **/
    public static function post_create($p) {

        $list = self::getListByP($p);

        // Создаем конструктор элемента
        $item = reflex::create("reflex_editor_constructor",array(
            "listData" => $list->serialize(),
        ));

        $url = $item->editor()->hash();

        return $url;
    }

    /**
     * Контроллер создания элемента из конструктора
     **/
    public static function post_createItem($p) {

        $constructor = reflex_editor_constructor::get($p["constructorID"]);
        $list = $constructor->getList();
        
        $ret = $list->editor()->beforeCreate($p["data"]);

        if(!$ret) {
            mod::msg("У вас нет прав для создания объекта",1);
            return;
        }
        
        if(is_array($ret)) {
            $p["data"] = $ret;
        }

        $item = $list->create($p["data"]);
        $item->log("Объект создан");

        if(!$item->exists())
            return;

        $constructor->delete();
        $item->editor()->afterCreate();
        return $item->editor()->actionAfterCreate();

    }

    /**
     * Контроллер закачивания файла
     **/
    public static function post_upload($p,$files) {
        $list = self::getListByP($p);

        if(!$list->editor()->beforeCreate(array())) {
            mod::msg("У вас нет прав для создания объекта",1);
            return;
        }

        $item = $list->create();
        $editor = $item->editor();
        $file = $_FILES["file"];

        $ret = $editor->uploadFileIntoItem( array (
            "tmpName" => $file["tmp_name"],
            "name" => $file["name"]
        ));

        if(!$ret) {
            mod::msg("Создание объекта из файла недоступно. Попробуйте добавить объект.",1);
            $item->delete();
        }

    }

    /**
     * Контроллер удаления объекта
     **/
    public static function post_delete($p) {

        foreach($p["ids"] as $id) {

            $editor = self::get($id);
            $item = $editor->item();

            if($editor->beforeEdit()) { // Проверяем возможность удаления объекта

                $item->log("Объект {$item->title()} удален");
                if(get_class($item)!="reflex_editor_trash") {
                    $trash = reflex::create("reflex_editor_trash",array(
                        "title" => $item->title(),
                        "data" => json_encode($item->data()),
                        "meta" => json_encode($item->metaObject()->data()),
                        "img" => $item->editor()->img(),
                        "class" => get_class($item),
                    ));
                }
                $item->metaObject()->delete();
                $item->delete();
            } else {
                mod::msg("У вас нет прав для удаления этого объекта",1);
            }
        }
    }

    /**
     * Контроллер перемещения на одну позицию вверх
     **/
    public static function post_moveUp($p) {
        $list = self::getListByP($p)->limit(0);
        $name = $list->param("*priority");

        if(!$list->param("sort") || !$name) {
            mod::msg("Сортировка этого списка не включена",1);
            return;
        }

        foreach($list as $item) {
            if(!$item->editor()->beforeEdit()) {
                mod::msg("У вас нет прав для изменения объекта",1);
                return;
            }
        }

        $itemID = self::get($p["itemID"])->item()->id();
        foreach($list->copy() as $key=>$item) {
            $key*=2;
            if($item->id()==$itemID) {
                $key-=$p["side"]==1 ? -3 : 3;
            }
            $item->data($name,$key);
        }
    }

    /**
     * Контроллер сохранения сортировки элементов
     * Вызывается после перетаскивания объектов в каталоге
     **/
    public static function post_sortItems($p) {

        $collection = self::getListByP($p);
        $pages = $collection->pages();

        // Поле, по которому будет производиться сортировка
        $name = $collection->param("*priority");
        if(!$collection->param("sort") || !$name) {
            mod::msg("Сортировка этого списка не включена",1);
            return;
        }

        $n = 0;

        $idList = array();
        foreach($p["priority"] as $editorParam) {
            $editor = self::get($editorParam);
            $idList[] = $editor->itemID();
        }

        // Проходим по всем страницам коллекции
        // и назначаем элементам приоритет в том порядке в котором они встретились нам
        // Если мы на странице, которую отсортировал пользователь,
        // Назначаем коллекции приоритет элементам методом setPrioritySequence
        for($i=1;$i<=$pages;$i++) {

            $collectionPage = $collection->copy()->page($i);

            if($i==$p["page"]) {
                $collectionPage->setPrioritySequence($idList);
            }

            foreach($collectionPage->editors() as $editor) {
                $editor->item()->data($name,$n);
                $n++;
            }
        }

    }

    /**
     * Контроллер команды "Вставить"
     **/
    public static function post_paste($p) {

        $list = self::getListByP($p);
        $eqs = $list->eqs();

        foreach($p["items"] as $itemHash) {

            $editor = self::get($itemHash);
            $item = $editor->item();

            if($editor->beforeEdit()) {

                if(get_class($item)!=$list->itemClass()) {
                    mod::msg("Выбранный объект нельзя вставить в этот список.",1);
                    return;
                }

                foreach($eqs as $key=>$val)
                    $item->data($key,$val);

                if($item->testForParentsRecursion()) {
                    mod::msg("Рекурсия",1);
                    $item->revert();
                }

            } else {

                mod::msg("У вас нет прав для изменения объекта",1);

            }
        }
    }

    /**
     * Контроллер, возвращающий полный список элементов коллекции
     * @todo Надо возвращать сериализованныю колекцию, а не список id (и на клиенте учитывать ее)
     **/
    public static function post_getAll($p) {

        $list = self::getListByP($p);

        $ids = $list->limit(0)->idList();

        return array(
            "ids" => $ids,
            "class" => $list->itemClass(),
        );

    }

    /**
     * Контроллер получения лога для данного объекта
     **/
    public static function post_log($p) {

        $items = self::get($p["index"])->item()->getLog();

        $ret = array();

        foreach($items as $item) {
            $txt = "";
            $txt.= "<div style='font-size:11px;opacity:.5;' >";
            $txt.= $item->pdata("user")->title()." / ".$item->pdata("datetime")->txt();
            $txt.= "</div>";

            $inject = $item->data("comment") ? " style='border-radius:10px;border:1px solid #ccc;padding:5px;' " : "";
            $txt.= "<div $inject>".$item->msg()."</div>";
            $ret[] = array(
                "text" => $txt,
            );
        }

        return $ret;

    }

    /**
     * Контроллер комментирования
     **/
    public static function post_comment($p) {
        $editor = self::get($p["index"]);
        if(!$editor->beforeEdit()) {
            mod::msg("Вы не можете оставлять комментарии для данного объекта",1);
            return;
        }
        $editor->item()->log($p["txt"],array("comment"=>true));
    }

    /**
     * Контроллер выполнения экшна
     **/
    public static function post_doAction($p) {
        $editor = self::get($p["id"]);
        if(!$editor->beforeEdit()) {
            mod::msg("Вы не можете редактировать данный объект",1);
            return;
        }
        call_user_func(array($editor,"action_$p[action]"));
    }

}
