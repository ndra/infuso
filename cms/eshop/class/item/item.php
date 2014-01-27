<?

/**
 * Модель товара
 **/
class eshop_item extends reflex {

	public function reflex_table() {
	
		return array (
		  'name' => 'eshop_item',
		  'fields' =>
		  array (
		    array (
		      'name' => 'id',
		      'type' => 'jft7-kef8-ccd6-kg85-iueh',
		      'editable' => '0',
		      'indexEnabled' => '0',
		    ),
		    array (
		      'name' => 'priority',
		      'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
		      'editable' => '0',
		      'label' => 'Приоритет',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'title',
		      'type' => 'v324-89xr-24nk-0z30-r243',
		      'editable' => '1',
		      'label' => 'Название товара',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'parent',
		      'type' => 'pg03-cv07-y16t-kli7-fe6x',
		      'editable' => '1',
		      'label' => 'Группа товаров',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		      'class' => 'eshop_group',
		    ),
		    array (
		      'name' => 'price',
		      'type' => 'nmu2-78a6-tcl6-owus-t4vb',
		      'editable' => '1',
		      'label' => 'Стоимость',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'description',
		      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
		      'editable' => '1',
		      'label' => 'Описание товара',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'photos',
		      'type' => 'f927-wl0n-410x-4grx-pg0o',
		      'editable' => '1',
		      'label' => 'Фотографии',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'active',
		      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
		      'editable' => '1',
		      'label' => 'Активный',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'starred',
		      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
		      'editable' => '1',
		      'label' => 'Избранный',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'instock',
		      'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
		      'editable' => '1',
		      'label' => 'В наличии (шт)',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'order',
		      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
		      'editable' => '1',
		      'label' => 'Возможность заказа',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		      'help' => 'Установка этого чекбокса разрешает заказывать товар, даже если его нет в наличии',
		    ),
		    array (
		      'name' => 'activeSys',
		      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
		      'editable' => '0',
		      'label' => 'Активна ли группа',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'vendor',
		      'type' => 'pg03-cv07-y16t-kli7-fe6x',
		      'editable' => '1',
		      'label' => 'Производитель',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		      'class' => 'eshop_vendor',
		    ),
		    array (
		      'name' => 'model',
		      'type' => 'v324-89xr-24nk-0z30-r243',
		      'editable' => '1',
		      'label' => 'Модель',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'article',
		      'type' => 'v324-89xr-24nk-0z30-r243',
		      'editable' => '1',
		      'label' => 'Артикул',
		      'group' => 'Основные',
		      'indexEnabled' => '1',
		    ),
		    array (
		      'name' => 'extra',
		      'type' => 'puhj-w9sn-c10t-85bt-8e67',
		      'editable' => '1',
		      'label' => 'Дополнительно',
		      'group' => 'Дополнительно',
		      'indexEnabled' => '0',
		    ),
		    array (
		      'name' => 'created',
		      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
		      'editable' => '2',
		      'label' => 'Дата создания',
		      'group' => 'Дополнительно',
		      'indexEnabled' => '1',
		    ),
		  ),
		  'indexes' =>
		  array (
		    array (
		      'name' => 'title-f',
		      'fields' => 'title',
		      'type' => 'fulltext',
		    ),
		    array (
		      'name' => 'a-p-s',
		      'fields' => 'activeSys,priority,starred',
		      'type' => 'index',
		    ),
		  ),
		);
	}

    /**
     * Поведения по умолчанию
     **/
    public function defaultBehaviours() {
        $ret = parent::defaultBehaviours();
        $ret[] = "eshop_item_behaviour";
        return $ret;
    }

    /**
     * Видимость класса из браузера
     **/
    public static function indexTest() {
        return true;
    }

    /**
     * Экшн страницы товара
     **/
    public static function index_item($p) {
        $item = self::get($p["id"]);
        $item->addToHistory();
        tmp::param("activeGroupID",$item->group()->id());
        tmp::exec("eshop:item",array(
            "p1" => $item,
            "item" => $item,
        ));
    }

    /**
     * Включаем метаданные у товара
     **/
    public final function reflex_meta() {
        return true;
    }

    /**
     * После каждого действия с тоаром ставим задачу пересчитать родителей
     **/
    public final function reflex_afterStore() {
        if($this->field("parent")->changed() || $this->field("activeSys")->changed()) {
            $this->taskUpdateParents();
		}
    }

    /**
     * После удаления товара
     **/
    public final function reflex_afterDelete() {
        $this->taskUpdateParents();
    }

    /**
     * Ставит задачу обновить родителей элемента и вендора
     **/
    public final function taskUpdateParents() {
        foreach($this->groups() as $group) {
            reflex_task::add("eshop_group",$group->id(),"updateItemsNumber","",50);
		}
        reflex_task::add("eshop_vendor",$this->vendor()->id(),"updateItemsNumber","",0);
    }

	/**
	 * Запоминает дату создания товара
	 **/
    public final function reflex_beforeCreate() {
        $this->data("created",util::now());
    }

	/**
	 * Обновляет цепочку родителей и видимость товара
	 **/
    public function updateParentsChain() {
    
        // Обновляем поле с цепочкой родителей
        $parents = array();
        foreach($this->parents() as $parent) {
            if(get_class($parent)=="eshop_group") {
                $d = $parent->data("depth")+1;
                $this->data("group-{$d}",$parent->id());
            }
		}

        // Расчитываем признак активности товара - поле `activeSys`
		// Товар активен, если все его родительские группы активны и у товара поле `active` = 1
        $active = $this->data("active");
        
        // Если хотя бы одна из родительских групп неактивна, товар неактивен
        foreach($this->parents() as $parent) {
            if(!$parent->active()) {
                $active = false;
			}
		}

		// Записываем признак активности товара
        $this->data("activeSys",$active*1);
    }

    /**
     * Чинит элемент
     **/
    public final function reflex_beforeStore() {
        $this->updateParentsChain();
    }

    /**
     * @return Возвращает родительскую группу данного товара
     **/
    public final function reflex_parent() {
        return $this->group();

    }

    /**
     * @return Возвращает родительскую группу данного товара
     **/
    public final function group() {
        return eshop::group($this->data("parent"));
    }

    /**
     * @return Возвращает коллекцию групп для данного товара
     **/
    public function groups() {
        $idList = array();
        foreach($this->parents() as $parent)
            $idList[] = $parent->id();
        return eshop_group::allEvenHidden()->eq("id",$idList)->desc("depth");
    }

    public function reflex_classTitle() {
        return "Товарная позиция";
    }

    /**
     * @return Возвращает производителя товара
     **/
    public function vendor() {
        return $this->pdata("vendor");
    }

    /**
     * Возвращает коллекцию всех товаров, включая скрытые
     **/
    public static function allEvenHidden() {
        return reflex::get(get_class())
            ->param("sort",true)
            ->param("menu",false);
    }

    /**
     * Возвращает коллекцию активных товаров
     **/
    public static function all() {
        return self::allEvenHidden()->eq("activeSys",1);
    }

    /**
     * Возвращает товар по `id`
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    /**
     * Включаем лог у товаров
     **/
    public static function reflex_log() {
        return "true";
    }

    public function reflex_published() {
        if(!$this->data("active"))
            return false;
        if(!$this->data("activeSys"))
            return false;
        return true;
    }

    /**
     * Проверка возможности покупки $n штук товара
     **/
    public function tryBuy($n) {
        // Если у товара стоит галочка "на заказ", тестовая покупка всегда проходит
        if($this->data("order"))
            return true;
        // Если в наличии достаточно товара
        if($this->data("instock")>=$n)
            return true;
        return false;
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

    private static $historyCookie = "usn8vi";

    /**
     * @return Добавляет данный товар в историю просмотра
     **/
    protected final function addToHistory() {
        $history = explode(":",$_COOKIE[self::$historyCookie]);
        array_unshift($history,$this->id());
        $history = array_unique($history);
        array_splice($history, 5);
        $history = join(":",$history);
        setcookie(self::$historyCookie,$history,-1,"/");
        $_COOKIE[self::$historyCookie] = $history;
    }

    /**
     * @return Возвращает историю просмотра - коллекцию товаров
     **/
    public static function history() {
        $history = explode(":",$_COOKIE[self::$historyCookie]);
        return self::all()->eq("id",$history)->setPrioritySequence($history);
    }

    /**
     * Возвращает количество товаров в наличии
     **/
    public function inStock() {
        return $this->data("instock");
    }

    /**
     * Возвращает близкие по цене товары из той же группы
     **/
    public function similar() {
        $price = $this->price()*1;
        return $this->group()->items()->neq("id",$this->id())->orderByExpr("abs(`price`-'$price')");
    }
    
    public function photos() {

        $fn = "photos";
        $ret = array();
        foreach(array_reverse($this->behaviours()) as $b) {
            if(method_exists($b,$fn)) {
                $items = call_user_func(array($b,$fn));
                foreach($items as $item) {
                    $ret[] = $item;
                }
            }
        }

        if(!sizeof($ret)) {
            if($nophoto = $this->nophoto()) {
                $ret[] = $nophoto;
            }
        }

        return new file_list($ret);
    }

    /**
     * Возвращает картинку по умолчанию для товара,
     * например, лого производителя
     **/
    public function _nophoto() {}

    /**
     * Возвращает список товаров которые часто покупают с данным товаром
     * Список строится на основании заказов
     **/
    public function alsoBuy() {
        // Определяем, в каких заказах присутствует данный товар
        $orders = eshop_order_item::all()->eq("itemID",$this->id())->distinct("orderID");
        $items = eshop_order_item::all()->eq("orderID",$orders)->distinct("itemID");
        return eshop_item::all()->eq("id",$items)->neq("id",$this->id());

    }

    /**
     * @return bool добавлен ли товар в корзину?
     **/
    public function inCart() {
        foreach(eshop_order::cart()->items() as $item) {
            if($item->item()->id()==$this->id()) {
                return true;
			}
		}
    }

}
