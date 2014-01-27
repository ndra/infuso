<?

/**
 * Модель группы для интернет-магазина
 **/
class eshop_group extends reflex {

	public function defaultBehaviours() {
	    $ret = parent::defaultBehaviours();
	    $ret[] = "eshop_group_behaviour";
	    return $ret;
	}

	public static function indexTest() {
	    return true;
	}

	public static function index_item($p) {
	    $item  = self::get($p["id"]);
	    tmp::param("activeGroupID",$item->id());
	    tmp::exec("eshop:group",$item,$p);
	}

	public function reflex_meta() {
	    return true;
	}

	public function reflex_title() {
	    $title = trim($this->data("title"));
	    if(!$title)
			$title = "Группа товаров {$this->id()}";
	    return $title;
	}

	/**
	 * @return Возвращает коллекцию подгрупп, включая неактивные
	 **/
	public function subgroupsEvenHidden() {
	    return self::allEvenHidden()->eq("parent",$this->id());
	}

	/**
	 * @return Возвращает коллекцию активных подгрупп
	 **/
	public function subgroups() {
	    return self::all()->eq("parent",$this->id());
	}

	/**
	 * @return Возвращает коллекцию активных подгрупп всех уровней
	 **/
	public function subgroupsRecursive() {

	    $buf = array();
	    $groups = $this->subgroups()->limit(0);
	    while($groups->count()) {
	        $buf = array_merge($buf,$groups->idList());
	        $groups = eshop_group::all()->eq("parent",$groups->idList());
	    }
	    return eshop_group::all()->eq("id",$buf);
	}

	/**
	 * @return Возвращает коллекцию товаров в группе
	 **/
	public function items() {
	    return self::itemsEvenHidden()->eq("activeSys",1);
	}

	/**
	 * @return Возвращает коллекцию товаров в группе, включая скрытые товары
	 **/
	public function itemsEvenHidden() {
	    $key = "group-".($this->data("depth")+1);
	    return reflex::get("eshop_item")->eq($key,$this->id());
	}

	public function reflex_children() {
	    $ret = array (
	        $this->subgroupsEvenHidden()
				->title("Подразделы"),
	        $this->itemsEvenHidden()
				->title("Товары")
				->def("parent",$this->id())
				->param("sort",!$this->subgroups()->count()), // Сортируем товары только если в группе нет подгрупп
	    );
	    return $ret;
	}

	public function reflex_parent() {
	    return self::get($this->data("parent"));
	}

	/**
	 * Возвращает группу первого уровня
	 **/
	public function level0() {
	    foreach($this->parents() as $parent)
	        if(!$parent->parent()->exists())
	            return $parent;
	    return $this;
	}

	/**
	 * Возвращает группу заданного уровня
	 **/
	public function level($level=0) {
	    foreach($this->parents() as $parent)
	        if($parent->depth()==$level)
	            return $parent;
	    return $this;
	}

	/**
	 * Возвращает глубину группы
	 * Группы верхнего уровня имеют глубину 0
	 **/
	public function depth() {
		return $this->data("depth");
	}

	/**
	 * Возвращает количество товаров в группе, используя сохраненное в таблице число
	 **/
	public function numberOfItems() {
	    return $this->data("numberOfItems");
	}

	/**
	 * Возвращает количество товаров в группе, используя сохраненное в таблице число
	 **/
	public function countItems() {
	    return $this->numberOfItems();
	}

	/**
	 * Возвращает количество подгрупп, используя сохраненное в таблице число
	 **/
	public function numberOfSubgroups() {
	    return $this->data("numberOfSubgroups");
	}

	public function reflex_beforeStore() {
	    $this->data("depth",sizeof($this->parents()));
	}

	public function handleStructureChanged() {
	
	    // Обновляем количество информации о товарах и подгруппах
		$this->updateSubgroupsNumber();
	    $this->updateItemsNumber();
	
		// Обновляем количество информации о товарах и подгруппах в подразделах
	    foreach($this->parents() as $group) {
	        $group->updateSubgroupsNumber();
	        $group->updateItemsNumber();
	    }

	    reflex_task::add("eshop_item","`parent`='{$this->id()}'","updateParentsChain","",100);
	    foreach($this->subgroupsRecursive() as $group) {
	        reflex_task::add("eshop_item","`parent`='{$group->id()}'","updateParentsChain","",100);
	    }
	}
	
	public function taskUpdateItems() {
	    return $this->handleStructureChanged();
	}

	public function reflex_afterStore() {
	    if($this->field("active")->changed() || $this->field("parent")->changed()) {
	        $this->taskUpdateItems();
	    }
	}

	public function reflex_afterDelete() {
	    foreach($this->parents() as $group) {
	        $group->updateSubgroupsNumber();
	        $group->updateItemsNumber();
	    }
	}

	public function reflex_repair() {
	    // Обновляем глубину
	    $this->updateItemsNumber();
	    $this->updateSubgroupsNumber();
	}

	/**
	 * Пересчитывает количество товаров в группе
	 **/
	public function updateItemsNumber() {
	    $this->data("numberOfItems",$this->items()->count());
	}

	/**
	 * Пересчитывает количество подгрупп
	 **/
	public function updateSubgroupsNumber() {
	    $this->data("numberOfSubgroups",$this->subgroups()->count());
	}

	/**
	 * Коллекция произвождителей в данной группе
	 **/
	public function vendors() {
	    $ids = $this->items()->distinct("vendor");
	    return eshop::vendors()->eq("id",$ids)->limit(0);
	}

	public function active() {
	    return $this->data("active");
	}

	public static function allEvenHidden() {
	    return reflex::get(get_class())->asc("priority")->param("sort",true);
	}

	public static function all() {
	    return self::allEvenHidden()->eq("active",1);
	}

	public static function get($id) {
	    return reflex::get(get_class(),$id);
	}

	public function reflex_published() {
	    if(!$this->data("active"))
	        return false;
	    return true;
	}

	public function extra($key,$val=null) {
	    $extra = $this->pdata("extra");
	    if(func_num_args()==1) {
	        return $extra[$key];
	    }
	    if(func_num_args()==2) {
	        $extra[$key] = $val;
	        $this->data("extra",json_encode($extra));
	    }
	}

	public function reflex_classTitle() {
	    return "Группа товаров";
	}

}
